<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rutele Fortify (login, register, reset) nu au prefix de limba.
 * Aici reluam limba din sesiune, sau o deducem din browser.
 */
class ApplySessionLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route('locale')) {
            return $next($request);   // rutele cu prefix se ocupa singure
        }

        $locale = session('locale');

        if (! in_array($locale, SetLocale::SUPPORTED, true)) {
            $browser = substr((string) $request->getPreferredLanguage(['ro', 'en']), 0, 2);
            $locale  = in_array($browser, SetLocale::SUPPORTED, true) ? $browser : config('app.locale');
        }

        app()->setLocale($locale);
        \Carbon\Carbon::setLocale($locale);

        return $next($request);
    }
}
