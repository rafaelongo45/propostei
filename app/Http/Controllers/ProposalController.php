<?php

namespace App\Http\Controllers;

use App\Jobs\SendProposalEmailJob;
use App\Models\Proposal;
use App\Services\ProposalPdfService;
use App\Services\ProposalSnapshotService;
use App\Services\UserEventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $proposals = $request->user()->proposals()
            ->with(['client', 'items', 'events'])
            ->latest()
            ->get()
            ->map(fn (Proposal $proposal) => $this->proposalPayload($proposal));

        return Inertia::render('Proposals/Index', [
            'proposals' => $proposals,
            'planUsage' => $request->user()->planUsage(),
            'onboarding' => [
                'clients_count' => $request->user()->clients()->count(),
            ],
            'stats' => [
                'total' => $proposals->count(),
                'accepted' => $proposals->where('status', 'accepted')->count(),
                'sent' => $proposals->where('status', 'sent')->count(),
                'draft' => $proposals->where('status', 'draft')->count(),
                'expired' => $proposals->where('is_expired', true)->count(),
                'canceled' => $proposals->where('status', 'canceled')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        if (! $request->user()->canCreateProposal()) {
            return redirect()->route('plans.index')->with('error', $this->freePlanLimitMessage());
        }

        $clients = $this->clientOptions($request);

        if ($clients->isEmpty()) {
            return redirect()->route('clients.create')->with('error', 'Cadastre um cliente antes de criar uma proposta.');
        }

        return Inertia::render('Proposals/Form', [
            'proposal' => null,
            'clients' => $clients,
            'statuses' => $this->editableStatuses(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, UserEventService $events, ProposalSnapshotService $snapshots): RedirectResponse
    {
        if (! $request->user()->canCreateProposal()) {
            return redirect()->route('plans.index')->with('error', $this->freePlanLimitMessage());
        }

        $data = $this->validatedData($request);

        $proposal = DB::transaction(function () use ($request, $data): Proposal {
            $proposalData = collect($data)->except('items')->all();

            if ($proposalData['status'] === 'accepted') {
                $proposalData += [
                    'accepted_at' => now(),
                    'accepted_by_name' => $request->user()->name,
                    'accepted_by_email' => $request->user()->email,
                    'accepted_by_ip' => $request->ip(),
                ];
            }

            $proposal = $request->user()->proposals()->create($proposalData);
            $this->syncItems($proposal, $data['items']);
            $proposal->recordEvent('created');

            return $proposal;
        });

        if ($proposal->status === 'accepted') {
            $proposal = $snapshots->ensureAcceptedSnapshot($proposal->load(['client', 'items', 'user']));
            $proposal->recordEvent('accepted_snapshot_created');
            $events->track('proposal_accepted_snapshot_created', [
                'proposal_id' => $proposal->id,
            ]);
        }

        $proposal->load('items');
        $events->track('proposal_created', [
            'proposal_id' => $proposal->id,
            'client_id' => $proposal->client_id,
            'total' => $proposal->total,
        ]);

        return redirect()->route('proposals.show', $proposal)->with('success', 'Proposta criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proposal $proposal): Response
    {
        $this->authorizeProposal($proposal);
        $proposal->load(['client', 'items', 'events']);

        return Inertia::render('Proposals/Show', [
            'proposal' => $this->proposalPayload($proposal),
        ]);
    }

    public function downloadPdf(Proposal $proposal, ProposalPdfService $pdfService, UserEventService $events)
    {
        $this->authorizeProposal($proposal);
        $proposal->recordEvent('pdf_downloaded');
        $events->track('proposal_pdf_downloaded', [
            'proposal_id' => $proposal->id,
        ]);

        return $pdfService->download($proposal);
    }

    public function send(Request $request, Proposal $proposal, UserEventService $events): RedirectResponse
    {
        $this->authorizeProposal($proposal);

        $data = $request->validate([
            'channel' => ['required', Rule::in(['whatsapp', 'email'])],
        ]);

        if ($data['channel'] === 'email') {
            if (! $proposal->client?->email) {
                return back()->with('error', 'Cadastre um e-mail para o cliente antes de enviar a proposta.');
            }

            if (in_array($proposal->status, ['accepted', 'rejected', 'canceled'], true)) {
                return back()->with('error', 'Propostas aceitas, recusadas ou canceladas não podem ser reenviadas por este fluxo.');
            }

            $proposal->recordEvent('email_queued', 'email', [
                'to' => $proposal->client->email,
            ]);

            SendProposalEmailJob::dispatch($proposal->id);

            return back()->with('success', 'Envio por e-mail colocado na fila.');
        }

        DB::transaction(function () use ($proposal, $data): void {
            $proposal->markAsSent($data['channel']);
            $proposal->recordEvent('sent_whatsapp', $data['channel']);
        });
        $events->track('proposal_sent_whatsapp', [
            'proposal_id' => $proposal->id,
            'client_id' => $proposal->client_id,
        ]);

        return back()->with('success', 'Envio registrado com sucesso.');
    }

    public function linkCopied(Proposal $proposal): RedirectResponse
    {
        $this->authorizeProposal($proposal);
        $proposal->recordEvent('link_copied');

        return back()->with('success', 'Link copiado.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Proposal $proposal): Response|RedirectResponse
    {
        $this->authorizeProposal($proposal);

        if ($this->isLockedProposal($proposal)) {
            return redirect()->route('proposals.show', $proposal)->with('error', $this->acceptedLockedMessage());
        }

        $proposal->load('items');

        return Inertia::render('Proposals/Form', [
            'proposal' => $this->proposalFormPayload($proposal),
            'clients' => $this->clientOptions($request),
            'statuses' => $this->editableStatuses(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proposal $proposal, ProposalSnapshotService $snapshots, UserEventService $events): RedirectResponse
    {
        $this->authorizeProposal($proposal);

        if ($this->isLockedProposal($proposal)) {
            return redirect()->route('proposals.show', $proposal)->with('error', $this->acceptedLockedMessage());
        }

        $data = $this->validatedData($request);

        DB::transaction(function () use ($request, $proposal, $data): void {
            $proposalData = collect($data)->except('items')->all();

            if ($proposalData['status'] === 'accepted' && $proposal->accepted_at === null) {
                $proposalData += [
                    'accepted_at' => now(),
                    'accepted_by_name' => $request->user()->name,
                    'accepted_by_email' => $request->user()->email,
                    'accepted_by_ip' => $request->ip(),
                ];
            }

            $proposal->update($proposalData);
            $this->syncItems($proposal, $data['items']);
            $proposal->recordEvent('updated');
        });

        if ($proposal->refresh()->status === 'accepted') {
            $proposal = $snapshots->ensureAcceptedSnapshot($proposal->load(['client', 'items', 'user']));
            $proposal->recordEvent('accepted_snapshot_created');
            $events->track('proposal_accepted_snapshot_created', [
                'proposal_id' => $proposal->id,
            ]);
        }

        return redirect()->route('proposals.show', $proposal)->with('success', 'Proposta atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal): RedirectResponse
    {
        $this->authorizeProposal($proposal);

        if ($this->isLockedProposal($proposal)) {
            return redirect()->route('proposals.show', $proposal)->with('error', $this->acceptedLockedMessage());
        }

        $proposal->delete();

        return redirect()->route('proposals.index')->with('success', 'Proposta removida com sucesso.');
    }

    public function duplicate(Request $request, Proposal $proposal, UserEventService $events): RedirectResponse
    {
        $this->authorizeProposal($proposal);

        if (! $request->user()->canCreateProposal()) {
            return redirect()->route('plans.index')->with('error', $this->freePlanLimitMessage());
        }

        $proposal->load('items');

        $copy = DB::transaction(function () use ($request, $proposal): Proposal {
            $copy = $request->user()->proposals()->create([
                'client_id' => $proposal->client_id,
                'title' => $proposal->title.' - nova versão',
                'introduction' => $proposal->introduction,
                'valid_until' => null,
                'delivery_estimate' => $proposal->delivery_estimate,
                'status' => 'draft',
                'internal_notes' => $proposal->internal_notes,
                'manual_discount' => $proposal->manual_discount,
            ]);

            foreach ($proposal->items as $index => $item) {
                $copy->items()->create([
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'sort_order' => $index + 1,
                ]);
            }

            $copy->recordEvent('duplicated', metadata: [
                'original_proposal_id' => $proposal->id,
            ]);

            return $copy;
        });

        $events->track('proposal_duplicated', [
            'proposal_id' => $copy->id,
            'original_proposal_id' => $proposal->id,
            'client_id' => $copy->client_id,
        ]);

        return redirect()->route('proposals.show', $copy)->with('success', 'Nova versão criada como rascunho.');
    }

    public function cancel(Request $request, Proposal $proposal, UserEventService $events, ProposalSnapshotService $snapshots): RedirectResponse
    {
        $this->authorizeProposal($proposal);

        if ($proposal->status !== 'accepted') {
            return redirect()->route('proposals.show', $proposal)->with('error', 'Somente propostas aceitas podem ser canceladas.');
        }

        $request->merge([
            'canceled_reason' => $this->cleanText($request->input('canceled_reason')),
        ]);

        $data = $request->validate([
            'canceled_reason' => ['required', 'string', 'min:5', 'max:4000'],
        ]);

        DB::transaction(function () use ($proposal, $data, $snapshots): void {
            $snapshots->ensureAcceptedSnapshot($proposal->load(['client', 'items', 'user']));

            $proposal->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'canceled_reason' => $data['canceled_reason'],
            ]);

            $proposal->recordEvent('canceled', metadata: [
                'message' => $data['canceled_reason'],
            ]);
        });

        $events->track('proposal_canceled', [
            'proposal_id' => $proposal->id,
            'client_id' => $proposal->client_id,
        ]);

        return redirect()->route('proposals.show', $proposal)->with('success', 'Proposta cancelada com sucesso.');
    }

    private function validatedData(Request $request): array
    {
        $request->merge($this->normalizedProposalInput($request));

        $data = $request->validate([
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('user_id', $request->user()->id),
            ],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'introduction' => ['nullable', 'string', 'max:4000'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'delivery_estimate' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in($this->editableStatuses())],
            'internal_notes' => ['nullable', 'string', 'max:4000'],
            'manual_discount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'min:2', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        $data['manual_discount'] ??= 0;

        return $data;
    }

    private function normalizedProposalInput(Request $request): array
    {
        return [
            'title' => $this->cleanText($request->input('title')),
            'introduction' => $this->cleanText($request->input('introduction')),
            'valid_until' => $this->cleanText($request->input('valid_until')),
            'delivery_estimate' => $this->cleanText($request->input('delivery_estimate')),
            'internal_notes' => $this->cleanText($request->input('internal_notes')),
            'manual_discount' => $this->cleanDecimal($request->input('manual_discount')),
            'items' => collect($request->input('items', []))
                ->map(function (mixed $item) {
                    if (! is_array($item)) {
                        return $item;
                    }

                    return [
                        ...$item,
                        'description' => $this->cleanText($item['description'] ?? null),
                        'quantity' => $this->cleanDecimal($item['quantity'] ?? null),
                        'unit_price' => $this->cleanDecimal($item['unit_price'] ?? null),
                    ];
                })
                ->all(),
        ];
    }

    private function cleanDecimal(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            return str_replace(',', '.', trim($value));
        }

        return $value;
    }

    private function cleanText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function syncItems(Proposal $proposal, array $items): void
    {
        $proposal->items()->delete();

        foreach ($items as $index => $item) {
            $proposal->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function clientOptions(Request $request)
    {
        return $request->user()->clients()
            ->orderBy('name')
            ->get(['id', 'name', 'company']);
    }

    private function authorizeProposal(Proposal $proposal): void
    {
        abort_unless($proposal->user_id === auth()->id(), 403);
    }

    private function freePlanLimitMessage(): string
    {
        return 'Você atingiu o limite de 5 propostas do plano grátis. Registre interesse no Premium por R$ 19,90/mês para criar propostas ilimitadas.';
    }

    private function acceptedLockedMessage(): string
    {
        return 'Propostas aceitas ou canceladas ficam bloqueadas para preservar o histórico. Crie uma nova versão para negociar alterações.';
    }

    private function editableStatuses(): array
    {
        return ['draft', 'sent', 'accepted', 'rejected'];
    }

    private function isLockedProposal(Proposal $proposal): bool
    {
        return in_array($proposal->status, ['accepted', 'canceled'], true);
    }

    private function proposalFormPayload(Proposal $proposal): array
    {
        return [
            'id' => $proposal->id,
            'client_id' => $proposal->client_id,
            'title' => $proposal->title,
            'introduction' => $proposal->introduction,
            'valid_until' => $proposal->valid_until?->toDateString(),
            'delivery_estimate' => $proposal->delivery_estimate,
            'status' => $proposal->status,
            'internal_notes' => $proposal->internal_notes,
            'manual_discount' => (float) $proposal->manual_discount,
            'items' => $proposal->items->map(fn ($item) => [
                'id' => $item->id,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ]),
        ];
    }

    private function proposalPayload(Proposal $proposal): array
    {
        $isExpired = $proposal->valid_until !== null
            && $proposal->valid_until->lt(today())
            && ! in_array($proposal->status, ['accepted', 'rejected', 'canceled'], true);

        return [
            'id' => $proposal->id,
            'client_id' => $proposal->client_id,
            'title' => $proposal->title,
            'introduction' => $proposal->introduction,
            'valid_until' => $proposal->valid_until?->toDateString(),
            'valid_until_display' => $proposal->valid_until?->format('d/m/Y'),
            'is_expired' => $isExpired,
            'delivery_estimate' => $proposal->delivery_estimate,
            'status' => $proposal->status,
            'sent_at' => $proposal->sent_at?->format('d/m/Y H:i'),
            'sent_channel' => $proposal->sent_channel,
            'accepted_at' => $proposal->accepted_at?->format('d/m/Y H:i'),
            'accepted_by_name' => $proposal->accepted_by_name,
            'accepted_by_email' => $proposal->accepted_by_email,
            'acceptance_notes' => $proposal->acceptance_notes,
            'rejected_at' => $proposal->rejected_at?->format('d/m/Y H:i'),
            'rejected_by_name' => $proposal->rejected_by_name,
            'rejected_by_email' => $proposal->rejected_by_email,
            'rejection_reason' => $proposal->rejection_reason,
            'canceled_at' => $proposal->canceled_at?->format('d/m/Y H:i'),
            'canceled_reason' => $proposal->canceled_reason,
            'internal_notes' => $proposal->internal_notes,
            'manual_discount' => (float) $proposal->manual_discount,
            'public_token' => $proposal->public_token,
            'public_url' => route('public.proposals.show', $proposal->public_token),
            'pdf_url' => route('proposals.pdf', $proposal),
            'send_url' => route('proposals.send', $proposal),
            'link_copied_url' => route('proposals.link-copied', $proposal),
            'duplicate_url' => route('proposals.duplicate', $proposal),
            'cancel_url' => route('proposals.cancel', $proposal),
            'client' => $proposal->client,
            'items' => $proposal->items->map(fn ($item) => [
                'id' => $item->id,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => $item->subtotal,
            ]),
            'subtotal' => $proposal->subtotal,
            'total' => $proposal->total,
            'created_at' => $proposal->created_at?->toDateString(),
            'events' => $proposal->events->map(fn ($event) => [
                'id' => $event->id,
                'type' => $event->type,
                'channel' => $event->channel,
                'metadata' => $event->metadata,
                'created_at' => $event->created_at?->format('d/m/Y H:i'),
            ]),
        ];
    }
}
