<?php

namespace Database\Seeders;

use App\Models\ContentItem;
use App\Models\ContentList;
use App\Models\ContentSection;
use Illuminate\Database\Seeder;

/**
 * Lista de bagaj standard, pe secțiuni. E o listă obișnuită, deci se poate
 * edita, duplica și atribui oricărei croaziere din panou.
 *
 *   php artisan db:seed --class=ChecklistTemplateSeeder --force
 */
class ChecklistTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $lista = ContentList::firstOrCreate(
            ['type' => 'packing', 'name->ro' => 'Necesar standard'],
            [
                'name'  => ['ro' => 'Necesar standard', 'en' => 'Standard packing list'],
                'intro' => [
                    'ro' => 'Lista de bază pentru o croazieră de o săptămână în Mediterana.',
                    'en' => 'The basic list for a week-long cruise in the Mediterranean.',
                ],
            ]
        );

        $structura = [
            [
                'ro' => 'Documente', 'en' => 'Documents',
                'elemente' => [
                    ['ro' => 'Pașaport sau carte de identitate', 'en' => 'Passport or ID card',
                     'detaliu_ro' => 'Valabil cel puțin 6 luni de la data întoarcerii.',
                     'detaliu_en' => 'Valid for at least 6 months after the return date.'],
                    ['ro' => 'Asigurare medicală de călătorie', 'en' => 'Travel health insurance'],
                ],
            ],
            [
                'ro' => 'Echipament', 'en' => 'Gear',
                'elemente' => [
                    ['ro' => 'Bluză cu protecție UV', 'en' => 'UV protection top'],
                    ['ro' => 'Jachetă de vânt și ploaie', 'en' => 'Wind and rain jacket',
                     'detaliu_ro' => 'Serile pe mare sunt mai reci decât pe uscat.',
                     'detaliu_en' => 'Evenings at sea are cooler than on land.'],
                    ['ro' => 'Bandană (tube scarf)', 'en' => 'Bandana (tube scarf)'],
                    ['ro' => 'Încălțăminte pentru drumeție și pentru apă', 'en' => 'Hiking and water shoes',
                     'detaliu_ro' => 'Opțional, ceva ușor de încălțat pentru barcă — șlapi sau sneakers.',
                     'detaliu_en' => 'Optionally something easy to slip on for the boat — flip-flops or sneakers.'],
                    ['ro' => 'Prosop de plajă subțire', 'en' => 'Thin beach towel',
                     'detaliu_ro' => 'Ușor de transportat și se usucă repede.',
                     'detaliu_en' => 'Easy to carry and quick to dry.'],
                ],
            ],
            [
                'ro' => 'Pentru soare', 'en' => 'Sun protection',
                'elemente' => [
                    ['ro' => 'Cremă de soare SPF 50', 'en' => 'Sunscreen SPF 50',
                     'detaliu_ro' => 'Pe mare soarele bate de două ori: direct și reflectat din apă.',
                     'detaliu_en' => 'At sea the sun hits twice: directly and reflected off the water.'],
                    ['ro' => 'Ochelari de soare cu șnur', 'en' => 'Sunglasses with a strap'],
                    ['ro' => 'Pălărie sau șapcă', 'en' => 'Hat or cap'],
                ],
            ],
            [
                'ro' => 'Bagaj și personale', 'en' => 'Luggage and personal',
                'elemente' => [
                    ['ro' => 'Bagaj moale, nu troler', 'en' => 'Soft bag, not a suitcase',
                     'detaliu_ro' => 'Se depozitează mult mai ușor la bord. Trolerele rigide nu au unde sta.',
                     'detaliu_en' => 'Much easier to stow on board. Hard suitcases have nowhere to go.'],
                    ['ro' => 'Medicamente personale', 'en' => 'Personal medication',
                     'detaliu_ro' => 'Plus ceva pentru rău de mare, dacă știi că ești sensibil.',
                     'detaliu_en' => 'Plus something for seasickness, if you know you are prone to it.'],
                    ['ro' => 'Încărcător și powerbank', 'en' => 'Charger and power bank',
                     'detaliu_ro' => 'Prizele de la bord sunt puține și nu merg mereu la ancoră.',
                     'detaliu_en' => 'Sockets on board are few and not always live at anchor.'],
                ],
            ],
        ];

        foreach ($structura as $i => $sectiune) {
            $s = ContentSection::firstOrCreate(
                ['content_list_id' => $lista->id, 'parent_id' => null, 'position' => $i],
                ['title' => ['ro' => $sectiune['ro'], 'en' => $sectiune['en']]]
            );

            foreach ($sectiune['elemente'] as $j => $el) {
                ContentItem::firstOrCreate(
                    ['content_section_id' => $s->id, 'position' => $j],
                    [
                        'title' => ['ro' => $el['ro'], 'en' => $el['en']],
                        'body'  => isset($el['detaliu_ro'])
                            ? ['ro' => $el['detaliu_ro'], 'en' => $el['detaliu_en']]
                            : null,
                    ]
                );
            }
        }

        $this->command?->info('Lista "Necesar standard": ' . count($structura) . ' secțiuni.');
    }
}
