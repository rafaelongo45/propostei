<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserEventService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, UserEventService $events): RedirectResponse
    {
        $request->merge([
            'name' => $this->cleanText($request->input('name')),
            'email' => $this->cleanEmail($request->input('email')),
        ]);

        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $events->track('user_registered', user: $user);

        return redirect(route('dashboard', absolute: false));
    }

    private function cleanEmail(mixed $value): ?string
    {
        $value = $this->cleanText($value);

        return $value === null ? null : mb_strtolower($value);
    }

    private function cleanText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
