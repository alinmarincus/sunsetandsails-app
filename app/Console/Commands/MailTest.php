<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Verifica rapid daca emailurile chiar pleaca.
 *
 *   php artisan club:mail-test alin@sunsetandsails.com
 */
class MailTest extends Command
{
    protected $signature = 'club:mail-test {email : Adresa pe care trimitem testul}';

    protected $description = 'Trimite un email de test, ca sa verifici configurarea';

    public function handle(): int
    {
        $to     = $this->argument('email');
        $mailer = config('mail.default');

        $this->line("Trimitem prin: <options=bold>{$mailer}</>");
        $this->line('Expeditor: ' . config('mail.from.address'));

        if ($mailer === 'log') {
            $this->warn('MAIL_MAILER=log — emailul se scrie doar in storage/logs, nu pleaca nicaieri.');
        }

        try {
            Mail::raw(
                "Acesta e un email de test de la Clubul Sunset & Sails.\n\n" .
                'Trimis la ' . now()->format('d.m.Y H:i') . '.',
                fn ($m) => $m->to($to)->subject('Test — Clubul Sunset & Sails')
            );
        } catch (\Throwable $e) {
            $this->error('A esuat: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info("Trimis catre {$to}. Verifica inboxul, inclusiv spam.");

        return self::SUCCESS;
    }
}
