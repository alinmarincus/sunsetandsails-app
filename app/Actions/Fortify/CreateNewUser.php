<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => $this->passwordRules(),
            'gdpr'     => ['accepted'],
        ], [
            'gdpr.accepted' => __('club.form.consent_gdpr'),
        ])->validate();

        // Acordul pentru fotografii NU se cere aici. Se cere mai tarziu,
        // cand membrul chiar are poze de vazut (vezi zona de galerie).
        return User::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'phone'    => $input['phone'] ?? null,
            'password' => Hash::make($input['password']),
            'locale'   => app()->getLocale(),
            'status'   => 'prospect',
        ]);
    }
}
