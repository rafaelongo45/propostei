<?php

namespace App\Providers;

use App\Mail\BrevoApiTransport;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('brevo', function () {
            return new BrevoApiTransport(
                new Client,
                (string) config('services.brevo.key'),
            );
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);
    }
}
