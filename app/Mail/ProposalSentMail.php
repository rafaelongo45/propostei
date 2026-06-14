<?php

namespace App\Mail;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Proposal $proposal,
    ) {}

    public function envelope(): Envelope
    {
        $user = $this->proposal->user;
        $senderName = $user?->company_name ?: $user?->name;

        return new Envelope(
            replyTo: $user?->email ? [
                new Address($user->email, $senderName ?: $user->email),
            ] : [],
            subject: 'Proposta comercial: '.$this->proposal->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.proposals.sent',
            with: [
                'proposal' => $this->proposal,
                'publicUrl' => route('public.proposals.show', $this->proposal->public_token),
                'senderName' => $this->proposal->user?->company_name ?: $this->proposal->user?->name,
            ],
        );
    }
}
