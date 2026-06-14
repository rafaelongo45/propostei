<?php

namespace App\Mail;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class BrevoApiTransport extends AbstractTransport
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly string $apiKey,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (! $email instanceof Email) {
            throw new TransportException('O transport da Brevo só suporta mensagens do tipo Email.');
        }

        try {
            $response = $this->client->request('POST', 'https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'accept' => 'application/json',
                    'api-key' => $this->apiKey,
                    'content-type' => 'application/json',
                ],
                'json' => $this->payload($email),
                'http_errors' => false,
                'timeout' => 15,
            ]);
        } catch (GuzzleException $exception) {
            throw new TransportException('Falha ao enviar e-mail pela API da Brevo: '.$exception->getMessage(), 0, $exception);
        }

        if ($response->getStatusCode() >= 400) {
            throw new TransportException(sprintf(
                'A API da Brevo recusou o e-mail com status %s: %s',
                $response->getStatusCode(),
                (string) $response->getBody(),
            ));
        }
    }

    public function __toString(): string
    {
        return 'brevo+api';
    }

    private function payload(Email $email): array
    {
        $payload = [
            'sender' => $this->address($email->getFrom()[0] ?? null),
            'to' => $this->addresses($email->getTo()),
            'subject' => $email->getSubject() ?? '(sem assunto)',
        ];

        if ($email->getHtmlBody()) {
            $payload['htmlContent'] = $this->body($email->getHtmlBody());
        }

        if ($email->getTextBody()) {
            $payload['textContent'] = $this->body($email->getTextBody());
        }

        if ($cc = $this->addresses($email->getCc())) {
            $payload['cc'] = $cc;
        }

        if ($bcc = $this->addresses($email->getBcc())) {
            $payload['bcc'] = $bcc;
        }

        if ($replyTo = $email->getReplyTo()[0] ?? null) {
            $payload['replyTo'] = $this->address($replyTo);
        }

        if ($attachments = $this->attachments($email->getAttachments())) {
            $payload['attachment'] = $attachments;
        }

        return $payload;
    }

    private function address(?Address $address): array
    {
        if (! $address) {
            throw new TransportException('O e-mail precisa ter um remetente configurado.');
        }

        $payload = ['email' => $address->getAddress()];

        if ($address->getName() !== '') {
            $payload['name'] = $address->getName();
        }

        return $payload;
    }

    /**
     * @param  Address[]  $addresses
     */
    private function addresses(array $addresses): array
    {
        return array_map(fn (Address $address) => $this->address($address), $addresses);
    }

    /**
     * @param  DataPart[]  $attachments
     */
    private function attachments(array $attachments): array
    {
        return collect($attachments)
            ->reject(fn (DataPart $attachment) => str_starts_with($attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'disposition') ?? '', 'inline'))
            ->map(fn (DataPart $attachment) => [
                'name' => $attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename') ?? 'anexo',
                'content' => base64_encode($attachment->bodyToString()),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  resource|string  $body
     */
    private function body(mixed $body): string
    {
        if (is_resource($body)) {
            return stream_get_contents($body) ?: '';
        }

        return (string) $body;
    }
}
