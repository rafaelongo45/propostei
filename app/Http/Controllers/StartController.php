<?php

namespace App\Http\Controllers;

use App\Services\UserEventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StartController extends Controller
{
    public function __invoke(Request $request, UserEventService $events): RedirectResponse
    {
        $events->track('register_clicked', [
            'source' => $request->query('source', 'start'),
        ]);

        return redirect()->route('register');
    }
}
