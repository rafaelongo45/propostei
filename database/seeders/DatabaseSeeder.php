<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->create([
            'name' => 'Usuário Teste',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'Propostei Demo',
        ]);

        $clients = collect([
            Client::query()->create([
                'user_id' => $user->id,
                'name' => 'Mariana Costa',
                'company' => 'Costa Arquitetura',
                'email' => 'mariana@example.com',
                'phone' => '(11) 99999-0001',
                'notes' => 'Cliente interessado em presença digital institucional.',
            ]),
            Client::query()->create([
                'user_id' => $user->id,
                'name' => 'Bruno Almeida',
                'company' => 'Almeida Studio',
                'email' => 'bruno@example.com',
                'phone' => '(21) 98888-0002',
                'notes' => 'Cliente para projeto de captação.',
            ]),
        ]);

        $sentProposal = Proposal::query()->create([
            'user_id' => $user->id,
            'client_id' => $clients[0]->id,
            'title' => 'Site institucional Costa Arquitetura',
            'introduction' => 'Proposta para criação de site institucional responsivo, com páginas essenciais e otimização inicial para mecanismos de busca.',
            'valid_until' => now()->addDays(15)->toDateString(),
            'delivery_estimate' => '30 dias após aprovação',
            'status' => 'sent',
            'sent_at' => now(),
            'sent_channel' => 'email',
            'public_token' => Str::random(32),
            'manual_discount' => 250,
        ]);

        $sentProposal->items()->createMany([
            ['description' => 'Planejamento e arquitetura das páginas', 'quantity' => 1, 'unit_price' => 1200, 'sort_order' => 1],
            ['description' => 'Design visual responsivo', 'quantity' => 1, 'unit_price' => 2500, 'sort_order' => 2],
            ['description' => 'Desenvolvimento e publicação', 'quantity' => 1, 'unit_price' => 3800, 'sort_order' => 3],
        ]);
        $sentProposal->recordEvent('created');
        $sentProposal->recordEvent('sent_email', 'email');

        $draftProposal = Proposal::query()->create([
            'user_id' => $user->id,
            'client_id' => $clients[1]->id,
            'title' => 'Landing page Almeida Studio',
            'introduction' => 'Proposta para landing page com foco em conversão e apresentação de serviços.',
            'valid_until' => now()->addDays(10)->toDateString(),
            'delivery_estimate' => '15 dias após aprovação',
            'status' => 'draft',
            'public_token' => Str::random(32),
        ]);

        $draftProposal->items()->createMany([
            ['description' => 'Layout da landing page', 'quantity' => 1, 'unit_price' => 1800, 'sort_order' => 1],
            ['description' => 'Implementação responsiva', 'quantity' => 1, 'unit_price' => 2200, 'sort_order' => 2],
        ]);
        $draftProposal->recordEvent('created');
    }
}
