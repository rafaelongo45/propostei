<?php

namespace App\Http\Controllers;

use App\Models\PremiumInterest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PremiumInterestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'source' => ['nullable', 'string', 'max:80'],
        ]);

        $user = $request->user();

        PremiumInterest::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'email' => $user->email,
                'current_proposals_count' => $user->activeProposalsCount(),
                'source' => $data['source'] ?? 'plans',
                'status' => 'interested',
            ],
        );

        return back()->with('success', 'Recebemos seu interesse no Premium. Assim que o plano estiver disponível, avisaremos você.');
    }

    public function adminIndex(): Response
    {
        return Inertia::render('Admin/PremiumInterests/Index', [
            'interests' => PremiumInterest::query()
                ->latest()
                ->get()
                ->map(fn (PremiumInterest $interest) => [
                    'id' => $interest->id,
                    'name' => $interest->name,
                    'email' => $interest->email,
                    'current_proposals_count' => $interest->current_proposals_count,
                    'source' => $interest->source,
                    'status' => $interest->status,
                    'created_at' => $interest->created_at?->format('d/m/Y H:i'),
                ]),
        ]);
    }
}
