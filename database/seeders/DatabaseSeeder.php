<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Usuário Teste',
            'email' => 'test@example.com',
        ]);

        $clients = Client::factory()
            ->count(2)
            ->for($user)
            ->sequence(
                ['name' => 'Mariana Costa', 'company' => 'Costa Arquitetura', 'email' => 'mariana@example.com'],
                ['name' => 'Bruno Almeida', 'company' => 'Almeida Studio', 'email' => 'bruno@example.com'],
            )
            ->create();

        Proposal::factory()
            ->for($user)
            ->for($clients[0])
            ->hasItems(3)
            ->create([
                'title' => 'Site institucional Costa Arquitetura',
                'status' => 'sent',
                'manual_discount' => 250,
            ]);

        Proposal::factory()
            ->for($user)
            ->for($clients[1])
            ->hasItems(2)
            ->create([
                'title' => 'Landing page Almeida Studio',
                'status' => 'draft',
            ]);
    }
}
