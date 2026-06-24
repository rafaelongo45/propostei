<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestProposalService
{
    private const SESSION_KEY = 'guest_proposal_token';

    public function pending(): ?Proposal
    {
        $token = session(self::SESSION_KEY);

        if (! is_string($token) || $token === '') {
            return null;
        }

        $proposal = Proposal::query()
            ->whereNull('user_id')
            ->where('guest_token', $token)
            ->where('guest_expires_at', '>', now())
            ->with('items')
            ->first();

        if (! $proposal) {
            session()->forget(self::SESSION_KEY);
        }

        return $proposal;
    }

    public function findOwned(string $token): Proposal
    {
        $sessionToken = session(self::SESSION_KEY);

        abort_unless(
            is_string($sessionToken) && hash_equals($sessionToken, $token),
            404,
        );

        return Proposal::query()
            ->whereNull('user_id')
            ->where('guest_token', $token)
            ->where('guest_expires_at', '>', now())
            ->with('items')
            ->firstOrFail();
    }

    public function save(array $proposalData, array $clientData, array $items): Proposal
    {
        return DB::transaction(function () use ($proposalData, $clientData, $items): Proposal {
            $proposal = $this->pending();

            if (! $proposal) {
                $token = Str::random(64);
                $proposal = Proposal::query()->create([
                    ...$proposalData,
                    'user_id' => null,
                    'client_id' => null,
                    'status' => 'draft',
                    'guest_token' => $token,
                    'guest_client' => $clientData,
                    'guest_expires_at' => now()->addDay(),
                ]);
                session([self::SESSION_KEY => $token]);
                $proposal->recordEvent('guest_created');
            } else {
                $proposal->update([
                    ...$proposalData,
                    'guest_client' => $clientData,
                    'guest_expires_at' => now()->addDay(),
                ]);
                $proposal->items()->delete();
                $proposal->recordEvent('guest_updated');
            }

            foreach ($items as $index => $item) {
                $proposal->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'sort_order' => $index + 1,
                ]);
            }

            return $proposal->refresh()->load('items');
        });
    }

    public function claimPending(User $user): ?Proposal
    {
        $token = session(self::SESSION_KEY);

        if (! is_string($token) || $token === '') {
            return null;
        }

        $proposal = DB::transaction(function () use ($token, $user): ?Proposal {
            $proposal = Proposal::query()
                ->whereNull('user_id')
                ->where('guest_token', $token)
                ->where('guest_expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (! $proposal) {
                return null;
            }

            $client = $user->clients()->create($proposal->guest_client);

            $proposal->update([
                'user_id' => $user->id,
                'client_id' => $client->id,
                'guest_token' => null,
                'guest_client' => null,
                'guest_expires_at' => null,
            ]);
            $proposal->recordEvent('guest_claimed');

            return $proposal;
        });

        session()->forget(self::SESSION_KEY);

        return $proposal;
    }
}
