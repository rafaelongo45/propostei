<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'title' => fake()->randomElement([
                'Desenvolvimento de site institucional',
                'Gestão de campanhas digitais',
                'Identidade visual para lançamento',
            ]),
            'introduction' => fake()->paragraph(),
            'valid_until' => now()->addDays(15),
            'delivery_estimate' => fake()->randomElement(['10 dias úteis', '15 dias úteis', '30 dias']),
            'status' => fake()->randomElement(Proposal::STATUSES),
            'internal_notes' => fake()->optional()->sentence(),
            'public_token' => Str::random(32),
            'manual_discount' => 0,
        ];
    }
}
