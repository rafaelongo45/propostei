<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalPlanLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_user_cannot_open_create_form_after_five_active_proposals(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        Proposal::factory()
            ->count(5)
            ->for($user)
            ->for($client)
            ->create();

        $this
            ->actingAs($user)
            ->get(route('proposals.create'))
            ->assertRedirect(route('plans.index'))
            ->assertSessionHas('error');
    }

    public function test_free_user_cannot_store_sixth_active_proposal(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        Proposal::factory()
            ->count(5)
            ->for($user)
            ->for($client)
            ->create();

        $this
            ->actingAs($user)
            ->post(route('proposals.store'), $this->proposalPayload($client->id))
            ->assertRedirect(route('plans.index'))
            ->assertSessionHas('error');

        $this->assertSame(5, $user->proposals()->count());
    }

    public function test_soft_deleted_proposals_do_not_count_for_free_limit(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        Proposal::factory()
            ->count(5)
            ->for($user)
            ->for($client)
            ->create();

        $user->proposals()->first()->delete();

        $this
            ->actingAs($user)
            ->post(route('proposals.store'), $this->proposalPayload($client->id))
            ->assertRedirect();

        $this->assertSame(5, $user->proposals()->count());
    }

    public function test_premium_user_can_store_more_than_five_proposals(): void
    {
        $user = User::factory()->create([
            'plan' => 'premium',
            'subscription_status' => 'active',
        ]);
        $client = Client::factory()->for($user)->create();

        Proposal::factory()
            ->count(5)
            ->for($user)
            ->for($client)
            ->create();

        $this
            ->actingAs($user)
            ->post(route('proposals.store'), $this->proposalPayload($client->id))
            ->assertRedirect();

        $this->assertSame(6, $user->proposals()->count());
    }

    private function proposalPayload(int $clientId): array
    {
        return [
            'client_id' => $clientId,
            'title' => 'Nova proposta premium',
            'introduction' => 'Escopo inicial da proposta.',
            'valid_until' => now()->addDays(10)->toDateString(),
            'delivery_estimate' => '10 dias úteis',
            'status' => 'draft',
            'internal_notes' => null,
            'manual_discount' => 0,
            'items' => [
                [
                    'description' => 'Serviço principal',
                    'quantity' => 1,
                    'unit_price' => 1000,
                ],
            ],
        ];
    }
}
