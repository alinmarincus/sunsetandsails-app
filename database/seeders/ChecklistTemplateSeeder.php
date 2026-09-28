<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use Illuminate\Database\Seeder;

/**
 * Lista de bagaj standard, folosita la orice croaziera care nu are
 * una proprie. Se poate edita oricand din panoul de admin.
 *
 *   php artisan db:seed --class=ChecklistTemplateSeeder --force
 */
class ChecklistTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'ro' => 'Pașaport sau carte de identitate',
                'en' => 'Passport or ID card',
                'hint_ro' => 'Valabil cel puțin 6 luni de la data întoarcerii.',
                'hint_en' => 'Valid for at least 6 months after the return date.',
            ],
            [
                'ro' => 'Cremă de soare SPF 50',
                'en' => 'Sunscreen SPF 50',
                'hint_ro' => 'Pe mare soarele bate de două ori: direct și reflectat din apă.',
                'hint_en' => 'At sea the sun hits twice: directly and reflected off the water.',
            ],
            [
                'ro' => 'Ochelari de soare cu șnur',
                'en' => 'Sunglasses with a strap',
                'hint_ro' => null, 'hint_en' => null,
            ],
            [
                'ro' => 'Pălărie sau șapcă',
                'en' => 'Hat or cap',
                'hint_ro' => null, 'hint_en' => null,
            ],
            [
                'ro' => 'Încălțăminte cu talpă albă',
                'en' => 'Shoes with white soles',
                'hint_ro' => 'Tălpile închise la culoare lasă urme pe punte.',
                'hint_en' => 'Dark soles leave marks on the deck.',
            ],
            [
                'ro' => 'Geacă subțire de vânt',
                'en' => 'Light windbreaker',
                'hint_ro' => 'Serile pe mare sunt mai reci decât pe uscat.',
                'hint_en' => 'Evenings at sea are cooler than on land.',
            ],
            [
                'ro' => 'Costume de baie (două)',
                'en' => 'Swimsuits (two)',
                'hint_ro' => 'Unul nu apucă să se usuce.',
                'hint_en' => 'One never has time to dry.',
            ],
            [
                'ro' => 'Prosop de plajă',
                'en' => 'Beach towel',
                'hint_ro' => null, 'hint_en' => null,
            ],
            [
                'ro' => 'Bagaj moale, nu troler',
                'en' => 'Soft bag, not a suitcase',
                'hint_ro' => 'Se depozitează mult mai ușor la bord. Trolerele rigide nu au unde sta.',
                'hint_en' => 'Much easier to stow on board. Hard suitcases have nowhere to go.',
            ],
            [
                'ro' => 'Medicamente personale',
                'en' => 'Personal medication',
                'hint_ro' => 'Plus ceva pentru rău de mare, dacă știi că ești sensibil.',
                'hint_en' => 'Plus something for seasickness, if you know you are prone to it.',
            ],
            [
                'ro' => 'Încărcător și powerbank',
                'en' => 'Charger and power bank',
                'hint_ro' => 'Prizele de la bord sunt puține și nu merg mereu la ancoră.',
                'hint_en' => 'Sockets on board are few and not always live at anchor.',
            ],
        ];

        foreach ($items as $i => $row) {
            ChecklistItem::updateOrCreate(
                ['trip_id' => null, 'position' => $i],
                [
                    'label' => ['ro' => $row['ro'], 'en' => $row['en']],
                    'hint'  => $row['hint_ro']
                        ? ['ro' => $row['hint_ro'], 'en' => $row['hint_en']]
                        : null,
                ]
            );
        }

        $this->command?->info('Lista de bagaj standard: ' . count($items) . ' elemente.');
    }
}
