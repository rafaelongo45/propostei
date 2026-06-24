<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Services\GuestProposalService;
use App\Services\UserEventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuestProposalController extends Controller
{
    public function create(Request $request, GuestProposalService $guests): Response|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('proposals.create');
        }

        $proposal = $guests->pending();

        return Inertia::render('GuestProposals/Form', [
            'proposal' => $proposal ? $this->formPayload($proposal) : null,
        ]);
    }

    public function store(Request $request, GuestProposalService $guests, UserEventService $events): RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('proposals.create');
        }

        $data = $this->validatedData($request);
        $proposal = $guests->save(
            proposalData: collect($data)->except(['client', 'items'])->all(),
            clientData: $data['client'],
            items: $data['items'],
        );

        $events->track('guest_proposal_created', [
            'proposal_id' => $proposal->id,
            'total' => $proposal->total,
        ]);

        return redirect()->route('guest.proposals.show', $proposal->guest_token);
    }

    public function show(Request $request, string $token, GuestProposalService $guests): Response|RedirectResponse
    {
        if ($request->user()) {
            $proposal = $guests->claimPending($request->user());

            return $proposal
                ? redirect()->route('proposals.show', $proposal)
                : redirect()->route('proposals.index');
        }

        return Inertia::render('GuestProposals/Show', [
            'proposal' => $this->previewPayload($guests->findOwned($token)),
        ]);
    }

    public function edit(Request $request, string $token, GuestProposalService $guests): Response|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('proposals.create');
        }

        return Inertia::render('GuestProposals/Form', [
            'proposal' => $this->formPayload($guests->findOwned($token)),
        ]);
    }

    public function update(Request $request, string $token, GuestProposalService $guests, UserEventService $events): RedirectResponse
    {
        if ($request->user()) {
            $proposal = $guests->claimPending($request->user());

            return $proposal
                ? redirect()->route('proposals.edit', $proposal)
                : redirect()->route('proposals.index');
        }

        $guests->findOwned($token);
        $data = $this->validatedData($request);
        $proposal = $guests->save(
            proposalData: collect($data)->except(['client', 'items'])->all(),
            clientData: $data['client'],
            items: $data['items'],
        );

        $events->track('guest_proposal_updated', [
            'proposal_id' => $proposal->id,
            'total' => $proposal->total,
        ]);

        return redirect()->route('guest.proposals.show', $proposal->guest_token)
            ->with('success', 'Proposta atualizada. Confira o resultado antes de criar sua conta.');
    }

    private function validatedData(Request $request): array
    {
        $request->merge([
            'title' => $this->cleanText($request->input('title')),
            'introduction' => $this->cleanText($request->input('introduction')),
            'valid_until' => $this->cleanText($request->input('valid_until')),
            'delivery_estimate' => $this->cleanText($request->input('delivery_estimate')),
            'manual_discount' => $this->cleanDecimal($request->input('manual_discount')),
            'client' => [
                'name' => $this->cleanText($request->input('client.name')),
                'email' => $this->cleanEmail($request->input('client.email')),
                'phone' => $this->cleanText($request->input('client.phone')),
                'company' => $this->cleanText($request->input('client.company')),
                'notes' => null,
            ],
            'items' => collect($request->input('items', []))->map(fn ($item) => is_array($item) ? [
                ...$item,
                'description' => $this->cleanText($item['description'] ?? null),
                'quantity' => $this->cleanDecimal($item['quantity'] ?? null),
                'unit_price' => $this->cleanDecimal($item['unit_price'] ?? null),
            ] : $item)->all(),
        ]);

        $data = $request->validate([
            'client.name' => ['required', 'string', 'min:2', 'max:255'],
            'client.email' => ['nullable', 'email', 'max:255'],
            'client.phone' => ['nullable', 'string', 'max:15', 'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'],
            'client.company' => ['nullable', 'string', 'max:255'],
            'client.notes' => ['nullable'],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'introduction' => ['nullable', 'string', 'max:4000'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'delivery_estimate' => ['nullable', 'string', 'max:255'],
            'manual_discount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.description' => ['required', 'string', 'min:2', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ], [
            'client.phone.regex' => 'Informe o telefone no formato (11) 99999-9999.',
            'items.max' => 'A proposta guest pode ter no máximo 20 itens.',
        ]);

        $data['manual_discount'] ??= 0;

        return $data;
    }

    private function formPayload(Proposal $proposal): array
    {
        return [
            'token' => $proposal->guest_token,
            'client' => $proposal->guest_client,
            'title' => $proposal->title,
            'introduction' => $proposal->introduction,
            'valid_until' => $proposal->valid_until?->toDateString(),
            'delivery_estimate' => $proposal->delivery_estimate,
            'manual_discount' => (float) $proposal->manual_discount,
            'items' => $proposal->items->map(fn ($item) => [
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ])->values(),
            'update_url' => route('guest.proposals.update', $proposal->guest_token),
        ];
    }

    private function previewPayload(Proposal $proposal): array
    {
        return [
            ...$this->formPayload($proposal),
            'subtotal' => $proposal->subtotal,
            'total' => $proposal->total,
            'edit_url' => route('guest.proposals.edit', $proposal->guest_token),
            'register_url' => route('register'),
            'login_url' => route('login'),
        ];
    }

    private function cleanEmail(mixed $value): ?string
    {
        $value = $this->cleanText($value);

        return $value === null ? null : mb_strtolower($value);
    }

    private function cleanDecimal(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_string($value) ? str_replace(',', '.', trim($value)) : $value;
    }

    private function cleanText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
