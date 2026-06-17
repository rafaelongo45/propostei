<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserEventService
{
    private const MARKETING_KEYS = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'gclid',
    ];

    public function track(string $event, array $metadata = [], ?User $user = null): void
    {
        try {
            $request = request();
            $user ??= Auth::user();

            $this->captureMarketingParams();

            $marketing = $this->marketingMetadata();
            if ($marketing !== []) {
                $metadata['marketing'] = [
                    ...($metadata['marketing'] ?? []),
                    ...$marketing,
                ];
            }

            UserEvent::query()->create([
                'user_id' => $user?->id,
                'event' => $event,
                'metadata' => $metadata === [] ? null : $metadata,
                'ip' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
            ]);
        } catch (Throwable $exception) {
            Log::debug('Falha ao registrar evento interno.', [
                'event' => $event,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function captureMarketingParams(): void
    {
        try {
            $request = request();
            $params = collect(self::MARKETING_KEYS)
                ->mapWithKeys(fn (string $key) => [$key => $request?->query($key)])
                ->filter(fn ($value) => filled($value))
                ->map(fn ($value) => trim((string) $value))
                ->all();

            if ($params !== []) {
                session(['marketing_attribution' => [
                    ...session('marketing_attribution', []),
                    ...$params,
                ]]);
            }
        } catch (Throwable $exception) {
            Log::debug('Falha ao capturar UTMs.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function marketingMetadata(): array
    {
        return collect(session('marketing_attribution', []))
            ->only(self::MARKETING_KEYS)
            ->filter(fn ($value) => filled($value))
            ->all();
    }
}
