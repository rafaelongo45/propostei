<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Proposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $clients = $request->user()->clients()
            ->withCount('proposals')
            ->latest()
            ->get();

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Clients/Form', [
            'client' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->user()->clients()->create($this->validatedData($request));

        return redirect()->route('clients.index')->with('success', 'Cliente criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): Response
    {
        $this->authorizeClient($client);
        $client->load(['proposals' => fn ($query) => $query->with('items')->latest()]);

        return Inertia::render('Clients/Show', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'company' => $client->company,
                'notes' => $client->notes,
                'created_at' => $client->created_at?->format('d/m/Y'),
            ],
            'proposals' => $client->proposals->map(function (Proposal $proposal) {
                $isExpired = $proposal->valid_until !== null
                    && $proposal->valid_until->lt(today())
                    && ! in_array($proposal->status, ['accepted', 'rejected'], true);

                return [
                    'id' => $proposal->id,
                    'title' => $proposal->title,
                    'status' => $proposal->status,
                    'valid_until' => $proposal->valid_until?->format('d/m/Y'),
                    'is_expired' => $isExpired,
                    'delivery_estimate' => $proposal->delivery_estimate,
                    'subtotal' => $proposal->subtotal,
                    'total' => $proposal->total,
                    'public_url' => route('public.proposals.show', $proposal->public_token),
                    'created_at' => $proposal->created_at?->format('d/m/Y'),
                ];
            }),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): Response
    {
        $this->authorizeClient($client);

        return Inertia::render('Clients/Form', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        $this->authorizeClient($client);

        $client->update($this->validatedData($request));

        return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $this->authorizeClient($client);

        if ($client->proposals()->exists()) {
            return back()->with('error', 'Este cliente possui propostas vinculadas.');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente removido com sucesso.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'],
            'company' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function authorizeClient(Client $client): void
    {
        abort_unless($client->user_id === auth()->id(), 403);
    }
}
