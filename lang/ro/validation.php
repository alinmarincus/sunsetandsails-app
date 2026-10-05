<?php

/**
 * Mesajele de validare, în română.
 *
 * E un fișier parțial, cu regulile pe care le folosim. Pentru orice cheie
 * lipsă, Laravel cade înapoi pe engleză (fallback_locale), deci nu riscăm
 * să apară texte rupte.
 */
return [

    'accepted'  => 'Trebuie să bifezi :attribute.',
    'boolean'   => 'Câmpul :attribute poate fi doar da sau nu.',
    'confirmed' => 'Cele două parole nu coincid.',
    'current_password' => 'Parola actuală nu este corectă.',
    'email'     => 'Adresa de email nu pare validă.',
    'exists'    => ':attribute selectat nu există.',
    'image'     => 'Fișierul trebuie să fie o imagine.',
    'integer'   => 'Câmpul :attribute trebuie să fie un număr.',
    'mimes'     => 'Fișierul trebuie să fie de tip: :values.',
    'required'  => 'Câmpul :attribute este obligatoriu.',
    'string'    => 'Câmpul :attribute trebuie să fie text.',
    'unique'    => 'Există deja un cont cu :attribute.',

    'max' => [
        'string' => 'Câmpul :attribute nu poate avea mai mult de :max caractere.',
        'file'   => 'Fișierul nu poate depăși :max kilobytes.',
    ],

    'min' => [
        'string' => 'Câmpul :attribute trebuie să aibă cel puțin :min caractere.',
    ],

    /* Numele câmpurilor, ca mesajele să sune natural */
    'attributes' => [
        'name'                  => 'numele',
        'email'                 => 'adresa de email',
        'phone'                 => 'telefonul',
        'password'              => 'parola',
        'password_confirmation' => 'confirmarea parolei',
        'current_password'      => 'parola actuală',
        'avatar'                => 'poza de profil',
        'gdpr'                  => 'acordul privind datele personale',
    ],

];
