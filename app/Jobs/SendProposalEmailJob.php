<?php

namespace App\Jobs;

use App\Mail\ProposalSentMail;
use App\Models\Proposal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendProposalEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public int $proposalId,
    ) {}

    public function handle(): void
    {
        $proposal = Proposal::query()
            ->with(['client', 'items', 'user'])
            ->findOrFail($this->proposalId);

        if (! $proposal->client?->email || in_array($proposal->status, ['accepted', 'rejected', 'canceled'], true)) {
            return;
        }

        if ($this->usesNonDeliveringMailer()) {
            $proposal->recordEvent('email_failed', 'email', [
                'message' => 'O mailer atual não envia e-mails de verdade. Configure MAIL_MAILER=smtp ou outro provedor transacional.',
                'mailer' => config('mail.default'),
            ]);

            return;
        }

        Mail::to($proposal->client->email, $proposal->client->name)
            ->send(new ProposalSentMail($proposal));

        $proposal->markAsSent('email');
        $proposal->recordEvent('sent_email', 'email', [
            'to' => $proposal->client->email,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $proposal = Proposal::query()->find($this->proposalId);

        $proposal?->recordEvent('email_failed', 'email', [
            'message' => $exception->getMessage(),
        ]);
    }

    private function usesNonDeliveringMailer(): bool
    {
        $mailer = config('mail.default');
        $transport = config("mail.mailers.{$mailer}.transport");

        return in_array($transport, ['log', 'array'], true);
    }
}
