<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\PremiumInterest;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PremiumInterestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_register_premium_interest_once(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        Proposal::factory()
            ->count(2)
            ->for($user)
            ->for($client)
            ->create();

        $this
            ->actingAs($user)
            ->post(route('premium-interest.store'), ['source' => 'plans'])
            ->assertSessionHas('success');

        $this
            ->actingAs($user)
            ->post(route('premium-interest.store'), ['source' => 'proposals_index'])
            ->assertSessionHas('success');

        $this->assertSame(1, PremiumInterest::query()->where('user_id', $user->id)->count());
        $this->assertDatabaseHas('premium_interests', [
            'user_id' => $user->id,
            'email' => $user->email,
            'current_proposals_count' => 2,
            'source' => 'proposals_index',
            'status' => 'interested',
        ]);
    }

    public function test_regular_user_cannot_access_admin_premium_interests(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('admin.premium-interests.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_premium_interests(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        PremiumInterest::query()->create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'current_proposals_count' => 4,
            'source' => 'plans',
            'status' => 'interested',
        ]);

        $this
            ->actingAs($admin)
            ->get(route('admin.premium-interests.index'))
            ->assertOk();
    }
}
