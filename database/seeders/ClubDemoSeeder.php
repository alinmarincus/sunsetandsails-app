<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\ChecklistItem;
use App\Models\Trip;
use App\Models\TripDay;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClubDemoSeeder extends Seeder
{
    public function run(): void
    {
        /* ── Administrator ──────────────────────────────────────────────── */
        $admin = User::updateOrCreate(
            ['email' => 'alin@sunsetandsails.com'],
            [
                'name'     => 'Alin Marincus',
                'password' => Hash::make('sunset2026'),
                'is_admin' => true,
                'status'   => 'member',
                'phone'    => '+40740048505',
            ]
        );

        /* ── Membru demo ────────────────────────────────────────────────── */
        $member = User::updateOrCreate(
            ['email' => 'membru@example.com'],
            [
                'name'             => 'George Popescu',
                'password'         => Hash::make('parola123'),
                'status'           => 'member',
                'phone'            => '+40722333444',
                'photo_consent_at' => now(),
            ]
        );

        /* ── Sablon global pentru lista de bagaj ────────────────────────── */
        $template = [
            ['ro' => 'Pașaport sau carte de identitate', 'en' => 'Passport or ID card',
             'hint_ro' => 'Valabil cel puțin 6 luni.', 'hint_en' => 'Valid for at least 6 months.'],
            ['ro' => 'Cremă de soare SPF 50', 'en' => 'Sunscreen SPF 50', 'hint_ro' => null, 'hint_en' => null],
            ['ro' => 'Ochelari de soare cu șnur', 'en' => 'Sunglasses with a strap', 'hint_ro' => null, 'hint_en' => null],
            ['ro' => 'Încălțăminte cu talpă albă', 'en' => 'Shoes with white soles',
             'hint_ro' => 'Tălpile negre lasă urme pe punte.', 'hint_en' => 'Dark soles leave marks on deck.'],
            ['ro' => 'Geacă subțire de vânt', 'en' => 'Light windbreaker', 'hint_ro' => null, 'hint_en' => null],
            ['ro' => 'Bagaj moale, nu troler', 'en' => 'Soft bag, not a suitcase',
             'hint_ro' => 'Se depozitează mult mai ușor la bord.', 'hint_en' => 'Much easier to stow on board.'],
            ['ro' => 'Medicamente personale', 'en' => 'Personal medication', 'hint_ro' => null, 'hint_en' => null],
        ];

        foreach ($template as $i => $row) {
            ChecklistItem::updateOrCreate(
                ['trip_id' => null, 'position' => $i],
                [
                    'label' => ['ro' => $row['ro'], 'en' => $row['en']],
                    'hint'  => $row['hint_ro'] ? ['ro' => $row['hint_ro'], 'en' => $row['hint_en']] : null,
                ]
            );
        }

        /* ── Croaziere ──────────────────────────────────────────────────── */
        $next = Trip::updateOrCreate(
            ['slug' => 'ciclade-octombrie-2026'],
            [
                'title'       => ['ro' => 'Ciclade', 'en' => 'Cyclades'],
                'destination' => ['ro' => 'Grecia', 'en' => 'Greece'],
                'summary'     => [
                    'ro' => 'Șapte zile printre insulele albe ale Cicladelor.',
                    'en' => 'Seven days among the white islands of the Cyclades.',
                ],
                'cover_image' => null,
                'start_date'  => now()->addDays(12)->toDateString(),
                'end_date'    => now()->addDays(19)->toDateString(),
                'capacity'    => 8,
                'boat'        => 'Bavaria 46',
                'status'      => 'published',
            ]
        );

        $past = Trip::updateOrCreate(
            ['slug' => 'ionice-mai-2026'],
            [
                'title'       => ['ro' => 'Insulele Ionice', 'en' => 'The Ionian Islands'],
                'destination' => ['ro' => 'Grecia', 'en' => 'Greece'],
                'summary'     => [
                    'ro' => 'Golfuri turcoaz și taverne de port, la pas de velier.',
                    'en' => 'Turquoise bays and harbour tavernas, at sailing pace.',
                ],
                'start_date'  => '2026-05-23',
                'end_date'    => '2026-05-30',
                'capacity'    => 8,
                'status'      => 'published',
            ]
        );

        /* ── Itinerar pentru croaziera urmatoare ────────────────────────── */
        $itinerary = [
            ['Paros — îmbarcare', 'Paros — embarkation'],
            ['Naxos', 'Naxos'],
            ['Koufonisia', 'Koufonisia'],
            ['Ios', 'Ios'],
            ['Santorini', 'Santorini'],
            ['Sifnos', 'Sifnos'],
            ['Paros — debarcare', 'Paros — disembarkation'],
        ];

        foreach ($itinerary as $i => [$ro, $en]) {
            TripDay::updateOrCreate(
                ['trip_id' => $next->id, 'day_number' => $i + 1],
                [
                    'date' => $next->start_date->copy()->addDays($i)->toDateString(),
                    'port' => ['ro' => $ro, 'en' => $en],
                ]
            );
        }

        /* ── Participari ────────────────────────────────────────────────── */
        $member->trips()->syncWithoutDetaching([
            $next->id => ['status' => 'confirmed'],
            $past->id => ['status' => 'confirmed'],
        ]);

        /* ── Anunturi ───────────────────────────────────────────────────── */
        Announcement::updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'ro' => 'Calendarul 2027 e aproape gata',
                    'en' => 'The 2027 calendar is almost ready',
                ],
                'body' => [
                    'ro' => 'Pregătim ieșirile de anul viitor. Membrii clubului au prioritate la rezervare, înainte ca datele să fie publice.',
                    'en' => 'We are preparing next year’s voyages. Club members get priority booking, before the dates go public.',
                ],
                'pinned'       => true,
                'published_at' => now()->subDays(2),
            ]
        );

        Announcement::updateOrCreate(
            ['id' => 2],
            [
                'title' => ['ro' => 'Întâlnire înainte de plecare', 'en' => 'Pre-departure meeting'],
                'body'  => [
                    'ro' => 'Cu o săptămână înainte de îmbarcare facem un apel video cu tot echipajul. Trimitem linkul pe email.',
                    'en' => 'A week before boarding we hold a video call with the whole crew. We will email you the link.',
                ],
                'trip_id'      => $next->id,
                'published_at' => now()->subDays(5),
            ]
        );
    }
}
