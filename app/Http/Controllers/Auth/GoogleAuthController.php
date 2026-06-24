<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GuestProposalService;
use App\Services\UserEventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(UserEventService $events): RedirectResponse
    {
        $events->track('google_login_started', [
            'provider' => 'google',
        ]);

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(UserEventService $events, GuestProposalService $guests): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = mb_strtolower(trim((string) $googleUser->getEmail()));

            if ($email === '') {
                return $this->failedLogin('Google não retornou um e-mail para autenticação.');
            }

            $wasCreated = false;
            $user = User::query()
                ->where('google_id', $googleUser->getId())
                ->first();

            if (! $user) {
                $user = User::query()->where('email', $email)->first();

                if ($user) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'auth_provider' => $user->auth_provider ?: 'google',
                        'email_verified_at' => $user->email_verified_at ?: now(),
                    ]);
                } else {
                    $wasCreated = true;
                    $user = User::query()->create([
                        'name' => $googleUser->getName() ?: Str::before($email, '@'),
                        'email' => $email,
                        'email_verified_at' => now(),
                        'password' => Hash::make(Str::random(32)),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'auth_provider' => 'google',
                    ]);
                }
            } else {
                $user->update([
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ]);
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            $metadata = [
                'provider' => 'google',
                'email' => $email,
            ];

            $events->track('google_login_completed', $metadata, $user);

            if ($wasCreated) {
                $events->track('google_user_registered', $metadata, $user);
                $events->track('user_registered', [
                    ...$metadata,
                    'auth_provider' => 'google',
                ], $user);
            }

            if ($proposal = $guests->claimPending($user)) {
                $events->track('guest_proposal_claimed', [
                    'proposal_id' => $proposal->id,
                    'auth_provider' => 'google',
                ], $user);

                return redirect()->route('proposals.show', $proposal)
                    ->with('success', 'Sua proposta foi salva na sua conta.');
            }

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (Throwable $exception) {
            return $this->failedLogin($exception->getMessage(), $exception);
        }
    }

    private function failedLogin(string $message, ?Throwable $exception = null): RedirectResponse
    {
        Log::warning('Falha no login com Google.', [
            'message' => $message,
            'exception' => $exception?->getMessage(),
        ]);

        return redirect()
            ->route('login')
            ->with('error', 'Não foi possível entrar com Google. Tente novamente ou use e-mail e senha.');
    }
}
