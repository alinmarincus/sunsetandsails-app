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
                            {--name= : Numele afisat}';

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

            if ($this->confirm('Schimbi parola?', false)) {
                $user->update(['password' => Hash::make($this->askForPassword())]);
                $this->info('Parola a fost schimbata.');
            }

            return self::SUCCESS;
        }

        $name     = $this->option('name') ?: $this->ask('Nume');
        $password = $this->askForPassword();

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

        return self::SUCCESS;
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
