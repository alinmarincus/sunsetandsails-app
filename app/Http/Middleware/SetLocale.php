<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED = ['ro', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);
        \Carbon\Carbon::setLocale($locale);

        // Limba aleasa ramane valabila si pe rutele fara prefix (login, register)
        session(['locale' => $locale]);

        return $next($request);
    }
}
