<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Services\ProposalPdfService;
use App\Services\ProposalSnapshotService;
use App\Services\UserEventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicProposalController extends Controller
{
    public function show(string $token, UserEventService $events): Response
    {
        $proposal = Proposal::query()
            ->whereNotNull('user_id')
            ->where('public_token', $token)
            ->with(['client', 'items', 'user'])
            ->firstOrFail();

        $events->track('proposal_public_viewed', [
            'proposal_id' => $proposal->id,
            'token' => $proposal->public_token,
            'viewer' => 'public_client',
        ], $proposal->user);

        return Inertia::render('Public/Proposal', [
            'proposal' => [
                'title' => $proposal->title,
                'introduction' => $proposal->introduction,
                'valid_until' => $proposal->valid_until?->toDateString(),
                'delivery_estimate' => $proposal->delivery_estimate,
                'status' => $proposal->status,
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
                'client' => [
                    'name' => $proposal->client->name,
                    'company' => $proposal->client->company,
                    'email' => $proposal->client->email,
                ],
                'items' => $proposal->items->map(fn ($item) => [
                    'description' => $item->description,
                    'quantity' => (float) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]),
                'manual_discount' => (float) $proposal->manual_discount,
                'subtotal' => $proposal->subtotal,
                'total' => $proposal->total,
                'payment' => $proposal->user?->paymentDetails() ?: null,
                'accept_url' => route('public.proposals.accept', $proposal->public_token),
                'reject_url' => route('public.proposals.reject', $proposal->public_token),
                'pdf_url' => route('public.proposals.pdf', $proposal->public_token),
            ],
        ]);
    }

    public function downloadPdf(string $token, ProposalPdfService $pdfService, UserEventService $events)
    {
        $proposal = Proposal::query()
            ->whereNotNull('user_id')
            ->where('public_token', $token)
            ->firstOrFail();

        $proposal->recordEvent('pdf_downloaded');
        $events->track('proposal_pdf_downloaded', [
            'proposal_id' => $proposal->id,
            'viewer' => 'public_client',
        ], $proposal->user);

        return $pdfService->download($proposal);
    }

    public function accept(Request $request, string $token, UserEventService $events, ProposalSnapshotService $snapshots): RedirectResponse
    {
        $request->merge([
            'accepted_by_name' => $this->cleanText($request->input('accepted_by_name')),
            'accepted_by_email' => $this->cleanEmail($request->input('accepted_by_email')),
            'acceptance_notes' => $this->cleanText($request->input('acceptance_notes')),
        ]);

        $data = $request->validate([
            'accepted_by_name' => ['required', 'string', 'min:2', 'max:255'],
            'accepted_by_email' => ['nullable', 'email', 'max:255'],
            'acceptance_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $proposal = Proposal::query()
            ->whereNotNull('user_id')
            ->where('public_token', $token)
            ->firstOrFail();

        if (! in_array($proposal->status, ['accepted', 'rejected', 'canceled'], true)) {
            $proposal->update([
                'status' => 'accepted',
                'accepted_at' => now(),
                'accepted_by_name' => $data['accepted_by_name'],
                'accepted_by_email' => $data['accepted_by_email'] ?? null,
                'accepted_by_ip' => $request->ip(),
                'acceptance_notes' => $data['acceptance_notes'] ?? null,
            ]);

            $proposal = $snapshots->ensureAcceptedSnapshot($proposal->refresh()->load(['client', 'items', 'user']));

            $proposal->recordEvent('accepted', metadata: [
                'name' => $data['accepted_by_name'],
                'email' => $data['accepted_by_email'] ?? null,
            ]);
            $proposal->recordEvent('accepted_snapshot_created');
            $events->track('proposal_accepted', [
                'proposal_id' => $proposal->id,
                'client_id' => $proposal->client_id,
            ], $proposal->user);
            $events->track('proposal_accepted_snapshot_created', [
                'proposal_id' => $proposal->id,
            ], $proposal->user);
        }

        return back()->with('success', 'Proposta aceita com sucesso.');
    }

    public function reject(Request $request, string $token, UserEventService $events): RedirectResponse
    {
        $request->merge([
            'rejected_by_name' => $this->cleanText($request->input('rejected_by_name')),
            'rejected_by_email' => $this->cleanEmail($request->input('rejected_by_email')),
            'rejection_reason' => $this->cleanText($request->input('rejection_reason')),
        ]);

        $data = $request->validate([
            'rejected_by_name' => ['required', 'string', 'min:2', 'max:255'],
            'rejected_by_email' => ['nullable', 'email', 'max:255'],
            'rejection_reason' => ['required', 'string', 'min:5', 'max:4000'],
        ]);

        $proposal = Proposal::query()
            ->whereNotNull('user_id')
            ->where('public_token', $token)
            ->firstOrFail();

        if (! in_array($proposal->status, ['accepted', 'rejected', 'canceled'], true)) {
            $proposal->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by_name' => $data['rejected_by_name'],
                'rejected_by_email' => $data['rejected_by_email'] ?? null,
                'rejected_by_ip' => $request->ip(),
                'rejection_reason' => $data['rejection_reason'],
            ]);

            $proposal->recordEvent('rejected', metadata: [
                'name' => $data['rejected_by_name'],
                'email' => $data['rejected_by_email'] ?? null,
            ]);
            $events->track('proposal_rejected', [
                'proposal_id' => $proposal->id,
                'client_id' => $proposal->client_id,
            ], $proposal->user);
        }

        return back()->with('success', 'Proposta recusada com sucesso.');
    }

    private function cleanEmail(mixed $value): ?string
    {
        $value = $this->cleanText($value);

        return $value === null ? null : mb_strtolower($value);
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
