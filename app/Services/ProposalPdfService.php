<?php

namespace App\Services;

use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProposalPdfService
{
    public function download(Proposal $proposal)
    {
        $proposal->loadMissing(['client', 'items', 'user']);

        return Pdf::loadView('pdfs.proposal', [
            'proposal' => $proposal,
            'companyLogoPath' => $this->companyLogoPath($proposal),
            'statusLabels' => [
                'draft' => 'Rascunho',
                'sent' => 'Enviada',
                'accepted' => 'Aceita',
                'rejected' => 'Recusada',
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
