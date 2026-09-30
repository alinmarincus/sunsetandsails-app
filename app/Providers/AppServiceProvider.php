<?php

namespace App\Providers;

use App\Mail\BrevoTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // MAIL_MAILER=brevo trimite prin API-ul Brevo, cu aceeasi cheie
        // folosita si de configurator.
        Mail::extend('brevo', function (array $config) {
            return new BrevoTransport(
                (string) ($config['key'] ?? ''),
                (int) ($config['timeout'] ?? 15),
            );
        });
    }
}
