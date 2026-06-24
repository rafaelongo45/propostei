<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestProposalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_and_preview_one_proposal_from_its_session(): void
    {
        $response = $this->post(route('guest.proposals.store'), $this->payload());

        $proposal = Proposal::query()->sole();

        $response->assertRedirect(route('guest.proposals.show', $proposal->guest_token));
        $this->assertNull($proposal->user_id);
        $this->assertNull($proposal->client_id);
        $this->assertSame('Cliente Exemplo', $proposal->guest_client['name']);
        $this->assertCount(1, $proposal->items);

        $this->get(route('guest.proposals.show', $proposal->guest_token))->assertOk();
    }

    public function test_guest_proposal_cannot_be_opened_from_another_session_or_public_client_route(): void
    {
        $this->post(route('guest.proposals.store'), $this->payload());
        $proposal = Proposal::query()->sole();

        $this->withSession(['guest_proposal_token' => str_repeat('A', 64)])
            ->get(route('guest.proposals.show', $proposal->guest_token))
            ->assertNotFound();

        $this->get(route('public.proposals.show', $proposal->public_token))
            ->assertNotFound();
    }

    public function test_pending_guest_proposal_is_claimed_after_login(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $this->post(route('guest.proposals.store'), $this->payload());
        $proposal = Proposal::query()->sole();
        $token = $proposal->guest_token;

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $proposal->refresh();

        $response->assertRedirect(route('proposals.show', $proposal));
        $this->assertSame($user->id, $proposal->user_id);
        $this->assertNotNull($proposal->client_id);
        $this->assertNull($proposal->guest_token);
        $this->assertNull($proposal->guest_client);
        $this->assertDatabaseHas('clients', [
            'id' => $proposal->client_id,
            'user_id' => $user->id,
            'name' => 'Cliente Exemplo',
        ]);
        $this->assertNotSame($token, $proposal->guest_token);
    }

    private function payload(): array
    {
        return [
            'client' => [
                'name' => 'Cliente Exemplo',
                'email' => 'cliente@example.com',
                'phone' => '(11) 99999-9999',
                'company' => 'Empresa Exemplo',
            ],
            'title' => 'Proposta de serviço',
            'introduction' => 'Descrição da proposta de serviço.',
            'valid_until' => now()->addWeek()->toDateString(),
            'delivery_estimate' => '10 dias úteis',
            'manual_discount' => 0,
            'items' => [[
                'description' => 'Serviço principal',
                'quantity' => 1,
                'unit_price' => 500,
            ]],
        ];
    }
}
