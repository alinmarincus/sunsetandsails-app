<?php

use App\Http\Controllers\ClubController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/* Fara limba in adresa → mergem pe limba din browser */
Route::get('/', function () {
    $browser = substr((string) request()->getPreferredLanguage(['ro', 'en']), 0, 2);
    $locale  = in_array($browser, SetLocale::SUPPORTED, true) ? $browser : config('app.locale');

    return redirect("/$locale");
});

/* Schimbarea limbii pe paginile fara prefix (login, resetare parola) */
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, SetLocale::SUPPORTED, true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('lang.switch');

Route::prefix('{locale}')
    ->whereIn('locale', SetLocale::SUPPORTED)
    ->middleware(SetLocale::class)
    ->group(function () {

        /* Public */
        Route::get('/', [ClubController::class, 'join'])->name('club.join');

        /* Membri */
        Route::middleware('auth')->group(function () {
            Route::get('/cont', [ClubController::class, 'dashboard'])->name('club.dashboard');
            Route::get('/profil', [ClubController::class, 'profile'])->name('club.profile');
            Route::put('/profil', [ClubController::class, 'updateProfile'])->name('club.profile.update');
            Route::post('/bagaj', [ClubController::class, 'toggleChecklist'])->name('club.checklist.toggle');
        });
    });
