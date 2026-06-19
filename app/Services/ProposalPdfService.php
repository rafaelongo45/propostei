<?php

namespace App\Services;

use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProposalPdfService
{
    public function __construct(private readonly ProposalSnapshotService $snapshots) {}

    public function download(Proposal $proposal)
    {
        $proposal->loadMissing(['client', 'items', 'user']);

        if ($proposal->status === 'accepted') {
            $proposal = $this->snapshots->ensureAcceptedSnapshot($proposal, late: true);
        }

        if ($proposal->accepted_snapshot !== null) {
            return Pdf::loadView('pdfs.proposal-accepted', [
                'snapshot' => $proposal->accepted_snapshot,
                'cancellation' => $proposal->status === 'canceled' ? [
                    'canceled_at' => $proposal->canceled_at?->toIso8601String(),
                    'canceled_reason' => $proposal->canceled_reason,
                ] : null,
                'companyLogoPath' => $this->snapshots->logoPath($proposal->accepted_snapshot),
            ])
                ->setPaper('a4')
                ->download($this->filename($proposal));
        }

        return Pdf::loadView('pdfs.proposal', [
            'proposal' => $proposal,
            'companyLogoPath' => $this->companyLogoPath($proposal),
            'statusLabels' => [
                'draft' => 'Rascunho',
                'sent' => 'Enviada',
                'accepted' => 'Aceita',
                'rejected' => 'Recusada',
                'canceled' => 'Cancelada',
            ],
        ])
            ->setPaper('a4')
            ->download($this->filename($proposal));
    }

    private function filename(Proposal $proposal): string
    {
        $client = Str::slug($proposal->client?->name ?: 'cliente');
        $title = Str::slug($proposal->title ?: 'proposta');

        return "proposta-{$title}-{$client}.pdf";
    }

    private function companyLogoPath(Proposal $proposal): ?string
    {
        $logoPath = $proposal->user?->company_logo_path;

        if (! $logoPath || ! Storage::disk('public')->exists($logoPath)) {
            return null;
        }

        return Storage::disk('public')->path($logoPath);
    }
}
