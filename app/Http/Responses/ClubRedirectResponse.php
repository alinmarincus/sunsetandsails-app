<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

/**
 * Dupa login sau inscriere, membrul ajunge in contul lui,
 * in limba in care naviga.
 */
class ClubRedirectResponse implements LoginResponse, RegisterResponse
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->intended(route('club.dashboard', app()->getLocale()));
    }
}
