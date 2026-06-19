<?php

namespace App\Support;

use App\Models\Client;
use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Models\User;
use Illuminate\Support\Collection;

class DemoProposalData
{
    public static function payload(): array
    {
        $items = self::items();
        $subtotal = collect($items)->sum('subtotal');

        return [
            'title' => 'Pintura residencial de apartamento',
            'introduction' => 'Esta proposta apresenta o escopo, prazo e investimento para pintura interna de um apartamento residencial.',
            'status' => 'Em análise',
            'valid_until' => now()->addDays(7)->toDateString(),
            'validity' => now()->addDays(7)->format('d/m/Y'),
            'delivery_estimate' => '5 dias úteis',
            'client' => [
                'name' => 'Mariana Oliveira',
                'company' => 'Apartamento 1204',
                'email' => 'mariana.exemplo@email.com',
                'phone' => '(11) 99999-0000',
            ],
            'items' => $items,
            'manual_discount' => 0,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'payment' => [
                'recipient_name' => 'Carlos Souza Pinturas',
                'pix_key' => 'contato@carlossouzapinturas.com.br',
                'pix_key_type_label' => 'E-mail',
                'link' => null,
                'instructions' => 'Para iniciar o serviço, combine o pagamento de entrada após o aceite da proposta.',
            ],
            'pdf_url' => asset('demo-proposta.pdf'),
        ];
    }

    public static function proposalModel(): Proposal
    {
        $payload = self::payload();

        $user = new User([
            'name' => 'Carlos Souza Pinturas',
            'company_name' => 'Carlos Souza Pinturas',
            'payment_pix_key' => $payload['payment']['pix_key'],
            'payment_pix_key_type' => 'email',
            'payment_recipient_name' => $payload['payment']['recipient_name'],
            'payment_instructions' => $payload['payment']['instructions'],
        ]);

        $client = new Client([
            'name' => $payload['client']['name'],
            'company' => $payload['client']['company'],
            'email' => $payload['client']['email'],
            'phone' => $payload['client']['phone'],
        ]);

        $items = new Collection(collect($payload['items'])->map(fn (array $item, int $index) => new ProposalItem([
            'description' => $item['description'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'sort_order' => $index + 1,
        ])));

        $proposal = new Proposal([
            'title' => $payload['title'],
            'introduction' => $payload['introduction'],
            'valid_until' => now()->addDays(7),
            'delivery_estimate' => $payload['delivery_estimate'],
            'status' => 'sent',
            'manual_discount' => 0,
        ]);

        $proposal->setRelation('user', $user);
        $proposal->setRelation('client', $client);
        $proposal->setRelation('items', $items);

        return $proposal;
    }

    private static function items(): array
    {
        return [
            [
                'description' => 'Preparação e proteção dos ambientes',
                'quantity' => 1,
                'unit_price' => 350,
                'subtotal' => 350,
            ],
            [
                'description' => 'Correção simples de imperfeições nas paredes',
                'quantity' => 1,
                'unit_price' => 450,
                'subtotal' => 450,
            ],
            [
                'description' => 'Pintura de paredes internas',
                'quantity' => 1,
                'unit_price' => 1600,
                'subtotal' => 1600,
            ],
            [
                'description' => 'Limpeza final e revisão do serviço',
                'quantity' => 1,
                'unit_price' => 300,
                'subtotal' => 300,
            ],
        ];
    }
}
