<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

/**
 * Creeaza sau promoveaza un administrator al clubului.
 *
 *   php artisan club:admin
 */
class MakeAdmin extends Command
{
    protected $signature = 'club:admin
                            {--email= : Adresa de email}
                            {--name= : Numele afisat}
                            {--password= : Parola, cand promptul ascuns nu e de incredere (ramane in istoricul shell-ului)}';

    protected $description = 'Creeaza un administrator al clubului (sau ii da drepturi unuia existent)';

    public function handle(): int
    {
        $email = $this->option('email') ?: $this->ask('Email');
        $user  = User::where('email', $email)->first();

        if ($user) {
            $this->info("Contul {$email} exista deja.");

            if (! $user->is_admin) {
                $user->update(['is_admin' => true]);
                $this->info('I-am dat drepturi de administrator.');
            } else {
                $this->line('Are deja drepturi de administrator.');
            }

            if ($this->option('password') || $this->confirm('Schimbi parola?', false)) {
                $parola = $this->obtinParola();
                $user->update(['password' => Hash::make($parola)]);

                // Verificam ce s-a salvat: promptul ascuns nu citeste corect
                // pe toate terminalele si raporta succes pe o parola gresita.
                $this->confirmaParola($user->fresh(), $parola);
            }

            return self::SUCCESS;
        }

        $name     = $this->option('name') ?: $this->ask('Nume');
        $password = $this->obtinParola();

        $validator = Validator::make(
            compact('name', 'email'),
            [
                'name'  => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'status'   => 'member',
        ]);

        $this->info("Administrator creat: {$email}");
        $this->line('Te poti autentifica la /admin');

        $this->confirmaParola(User::where('email', $email)->first(), $password);

        return self::SUCCESS;
    }

    /** Parola din optiune, daca e data, altfel de la prompt. */
    private function obtinParola(): string
    {
        $dinOptiune = (string) $this->option('password');

        if ($dinOptiune === '') {
            return $this->askForPassword();
        }

        $check = Validator::make(['password' => $dinOptiune], [
            'password' => ['required', Password::min(8)],
        ]);

        if ($check->fails()) {
            $this->error($check->errors()->first());
            exit(self::FAILURE);
        }

        return $dinOptiune;
    }

    /** Se autentifica parola salvata chiar cu parola ceruta? */
    private function confirmaParola(User $user, string $password): void
    {
        if (Hash::check($password, $user->password)) {
            $this->info('Verificat: parola salvata este cea ceruta.');

            return;
        }

        $this->error('Parola salvata NU se potriveste cu ce s-a citit de la prompt.');
        $this->line('Reia cu --password="parola", ca sa ocolesti promptul ascuns.');
    }

    private function askForPassword(): string
    {
        do {
            $password = $this->secret('Parola (minimum 8 caractere)');
            $check    = Validator::make(['password' => $password], [
                'password' => ['required', Password::min(8)],
            ]);

            if ($check->fails()) {
                $this->error($check->errors()->first());
            }
        } while ($check->fails());

        return $password;
    }
}
