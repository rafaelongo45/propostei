<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public ?string $subject,
        public string $messageText,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address($this->email, $this->name),
            ],
            subject: 'Contato pelo site: '.($this->subject ?: 'Nova mensagem'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.message',
        );
    }
}
