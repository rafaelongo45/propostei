<?php

namespace App\Http\Controllers;

use App\Services\UserEventService;
use App\Support\DemoProposalData;
use Inertia\Inertia;
use Inertia\Response;

class DemoProposalController extends Controller
{
    public function __invoke(UserEventService $events): Response
    {
        $events->track('demo_proposal_viewed');

        return Inertia::render('Demo/Proposal', [
            'proposal' => DemoProposalData::payload(),
        ]);
    }
}
