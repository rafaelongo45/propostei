<?php

namespace App\Services;

use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ProposalPdfService
{
    public function download(Proposal $proposal)
    {
        $proposal->loadMissing(['client', 'items']);

        return Pdf::loadView('pdfs.proposal', [
            'proposal' => $proposal,
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
}
