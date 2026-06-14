<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Services\ProposalPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicProposalController extends Controller
{
    public function show(string $token): Response
    {
        $proposal = Proposal::query()
            ->where('public_token', $token)
            ->with(['client', 'items'])
            ->firstOrFail();

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
                'accept_url' => route('public.proposals.accept', $proposal->public_token),
                'reject_url' => route('public.proposals.reject', $proposal->public_token),
                'pdf_url' => route('public.proposals.pdf', $proposal->public_token),
            ],
        ]);
    }

    public function downloadPdf(string $token, ProposalPdfService $pdfService)
    {
        $proposal = Proposal::query()
            ->where('public_token', $token)
            ->firstOrFail();

        $proposal->recordEvent('pdf_downloaded');

        return $pdfService->download($proposal);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $data = $request->validate([
            'accepted_by_name' => ['required', 'string', 'min:2', 'max:255'],
            'accepted_by_email' => ['nullable', 'email', 'max:255'],
            'acceptance_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $proposal = Proposal::query()
            ->where('public_token', $token)
            ->firstOrFail();

        if (! in_array($proposal->status, ['accepted', 'rejected'], true)) {
            $proposal->update([
                'status' => 'accepted',
                'accepted_at' => now(),
                'accepted_by_name' => $data['accepted_by_name'],
                'accepted_by_email' => $data['accepted_by_email'] ?? null,
                'accepted_by_ip' => $request->ip(),
                'acceptance_notes' => $data['acceptance_notes'] ?? null,
            ]);

            $proposal->recordEvent('accepted', metadata: [
                'name' => $data['accepted_by_name'],
                'email' => $data['accepted_by_email'] ?? null,
            ]);
        }

        return back()->with('success', 'Proposta aceita com sucesso.');
    }

    public function reject(Request $request, string $token): RedirectResponse
    {
        $data = $request->validate([
            'rejected_by_name' => ['required', 'string', 'min:2', 'max:255'],
            'rejected_by_email' => ['nullable', 'email', 'max:255'],
            'rejection_reason' => ['required', 'string', 'min:5', 'max:4000'],
        ]);

        $proposal = Proposal::query()
            ->where('public_token', $token)
            ->firstOrFail();

        if (! in_array($proposal->status, ['accepted', 'rejected'], true)) {
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
        }

        return back()->with('success', 'Proposta recusada com sucesso.');
    }
}
