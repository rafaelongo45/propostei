<?php

namespace App\Services;

use App\Models\Proposal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProposalSnapshotService
{
    public function ensureAcceptedSnapshot(Proposal $proposal, bool $late = false): Proposal
    {
        if ($proposal->status !== 'accepted' || $proposal->accepted_snapshot !== null) {
            return $proposal;
        }

        if ($late) {
            Log::warning('Snapshot de proposta aceita criado tardiamente.', [
                'proposal_id' => $proposal->id,
            ]);
        }

        $snapshot = $this->makeAcceptedSnapshot($proposal);

        $proposal->forceFill([
            'accepted_snapshot' => $snapshot,
            'accepted_snapshot_hash' => $this->hash($snapshot),
        ])->save();

        return $proposal->refresh();
    }

    public function makeAcceptedSnapshot(Proposal $proposal): array
    {
        $proposal->loadMissing(['client', 'items', 'user']);
        $user = $proposal->user;
        $client = $proposal->client;
        $payment = $user?->paymentDetails() ?: null;

        return [
            'version' => 1,
            'generated_at' => now()->toIso8601String(),
            'proposal' => [
                'id' => $proposal->id,
                'title' => $proposal->title,
                'introduction' => $proposal->introduction,
                'valid_until' => $proposal->valid_until?->toDateString(),
                'delivery_estimate' => $proposal->delivery_estimate,
                'status' => $proposal->status,
                'manual_discount' => (float) $proposal->manual_discount,
                'subtotal' => $proposal->subtotal,
                'total' => $proposal->total,
                'public_token' => $proposal->public_token,
            ],
            'seller' => [
                'id' => $user?->id,
                'name' => $user?->name,
                'email' => $user?->email,
                'company_name' => $user?->company_name,
                'company_logo_path' => $user?->company_logo_path,
            ],
            'client' => [
                'id' => $client?->id,
                'name' => $client?->name,
                'company' => $client?->company,
                'email' => $client?->email,
                'phone' => $client?->phone,
            ],
            'items' => $proposal->items->map(fn ($item) => [
                'id' => $item->id,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => $item->subtotal,
                'sort_order' => $item->sort_order,
            ])->values()->all(),
            'payment' => $payment,
            'acceptance' => [
                'accepted_at' => $proposal->accepted_at?->toIso8601String(),
                'accepted_by_name' => $proposal->accepted_by_name,
                'accepted_by_email' => $proposal->accepted_by_email,
                'acceptance_notes' => $proposal->acceptance_notes,
                'user_agent' => request()?->userAgent(),
            ],
        ];
    }

    public function hash(array $snapshot): string
    {
        return hash('sha256', json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function logoPath(?array $snapshot): ?string
    {
        $logoPath = data_get($snapshot, 'seller.company_logo_path');

        if (! $logoPath || ! Storage::disk('public')->exists($logoPath)) {
            return null;
        }

        return Storage::disk('public')->path($logoPath);
    }
}
