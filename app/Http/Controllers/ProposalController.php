<?php

namespace App\Http\Controllers;

use App\Jobs\SendProposalEmailJob;
use App\Models\Proposal;
use App\Services\ProposalPdfService;
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
            'stats' => [
                'total' => $proposals->count(),
                'accepted' => $proposals->where('status', 'accepted')->count(),
                'sent' => $proposals->where('status', 'sent')->count(),
                'draft' => $proposals->where('status', 'draft')->count(),
                'expired' => $proposals->where('is_expired', true)->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        $clients = $this->clientOptions($request);

        if ($clients->isEmpty()) {
            return redirect()->route('clients.create')->with('error', 'Cadastre um cliente antes de criar uma proposta.');
        }

        return Inertia::render('Proposals/Form', [
            'proposal' => null,
            'clients' => $clients,
            'statuses' => Proposal::STATUSES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
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

    public function downloadPdf(Proposal $proposal, ProposalPdfService $pdfService)
    {
        $this->authorizeProposal($proposal);
        $proposal->recordEvent('pdf_downloaded');

        return $pdfService->download($proposal);
    }

    public function send(Request $request, Proposal $proposal): RedirectResponse
    {
        $this->authorizeProposal($proposal);

        $data = $request->validate([
            'channel' => ['required', Rule::in(['whatsapp', 'email'])],
        ]);

        if ($data['channel'] === 'email') {
            if (! $proposal->client?->email) {
                return back()->with('error', 'Cadastre um e-mail para o cliente antes de enviar a proposta.');
            }

            if (in_array($proposal->status, ['accepted', 'rejected'], true)) {
                return back()->with('error', 'Propostas aceitas ou recusadas não podem ser reenviadas por este fluxo.');
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
    public function edit(Request $request, Proposal $proposal): Response
    {
        $this->authorizeProposal($proposal);
        $proposal->load('items');

        return Inertia::render('Proposals/Form', [
            'proposal' => $this->proposalFormPayload($proposal),
            'clients' => $this->clientOptions($request),
            'statuses' => Proposal::STATUSES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proposal $proposal): RedirectResponse
    {
        $this->authorizeProposal($proposal);
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

        return redirect()->route('proposals.show', $proposal)->with('success', 'Proposta atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal): RedirectResponse
    {
        $this->authorizeProposal($proposal);
        $proposal->delete();

        return redirect()->route('proposals.index')->with('success', 'Proposta removida com sucesso.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('user_id', $request->user()->id),
            ],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'introduction' => ['nullable', 'string', 'max:4000'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'delivery_estimate' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(Proposal::STATUSES)],
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
            && ! in_array($proposal->status, ['accepted', 'rejected'], true);

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
            'internal_notes' => $proposal->internal_notes,
            'manual_discount' => (float) $proposal->manual_discount,
            'public_token' => $proposal->public_token,
            'public_url' => route('public.proposals.show', $proposal->public_token),
            'pdf_url' => route('proposals.pdf', $proposal),
            'send_url' => route('proposals.send', $proposal),
            'link_copied_url' => route('proposals.link-copied', $proposal),
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
