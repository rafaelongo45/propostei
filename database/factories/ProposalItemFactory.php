<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\ProposalItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProposalItem>
 */
class ProposalItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 3);
        $unitPrice = fake()->numberBetween(300, 2500);

        return [
            'proposal_id' => Proposal::factory(),
            'description' => fake()->randomElement([
                'Diagnóstico e planejamento',
                'Design das telas principais',
                'Implementação e ajustes finais',
                'Reunião de alinhamento',
            ]),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'sort_order' => fake()->numberBetween(1, 5),
        ];
    }
}
