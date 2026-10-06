<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * O listă refolosibilă: necesar de bagaj, meniu sau informații utile.
 * Se atribuie uneia sau mai multor croaziere și se poate duplica.
 */
class ContentList extends Model
{
    use HasTranslations;

    public array $translatable = ['name', 'intro'];

    protected $guarded = [];

    public const TIPURI = [
        'packing' => 'Necesar de bagaj',
        'menu'    => 'Meniu',
        'info'    => 'Informații utile',
    ];

    /* ── Relații ────────────────────────────────────────────────────────── */

    /** Doar secțiunile de nivel întâi; subsecțiunile atârnă de ele. */
    public function sections(): HasMany
    {
        return $this->hasMany(ContentSection::class)
            ->whereNull('parent_id')
            ->orderBy('position');
    }

    public function allSections(): HasMany
    {
        return $this->hasMany(ContentSection::class)->orderBy('position');
    }

    /** Croazierele care folosesc lista asta, indiferent pe ce poziție. */
    public function trips()
    {
        return Trip::query()
            ->where('packing_list_id', $this->id)
            ->orWhere('menu_list_id', $this->id)
            ->orWhere('info_list_id', $this->id);
    }

    /* ── Helpers ────────────────────────────────────────────────────────── */

    /** Listele de un anumit tip, pentru selectoarele din panou. */
    public static function optiuni(string $tip): array
    {
        return self::where('type', $tip)
            ->get()
            ->mapWithKeys(fn (self $lista) => [
                $lista->id => $lista->getTranslation('name', 'ro'),
            ])
            ->all();
    }

    public function tipCitibil(): string
    {
        return self::TIPURI[$this->type] ?? $this->type;
    }

    /** Toate elementele listei, în ordine, indiferent de secțiune. */
    public function items()
    {
        return ContentItem::query()
            ->whereIn('content_section_id', $this->allSections()->pluck('id'))
            ->orderBy('position');
    }

    /**
     * Copie completă a listei, cu secțiuni, subsecțiuni și elemente.
     * Imaginile nu se dublează pe disc — copiile arată spre aceleași fișiere.
     */
    public function duplicate(?string $numeNou = null): self
    {
        $copie = self::create([
            'type'  => $this->type,
            'name'  => $this->getTranslations('name'),
            'intro' => $this->getTranslations('intro'),
        ]);

        if ($numeNou) {
            $copie->setTranslation('name', app()->getLocale(), $numeNou)->save();
        }

        // Întâi secțiunile de nivel întâi, apoi subsecțiunile lor
        $harta = [];   // id vechi => id nou

        foreach ($this->allSections()->orderBy('parent_id')->get() as $sectiune) {
            $copiaSectiunii = ContentSection::create([
                'content_list_id' => $copie->id,
                'parent_id'       => $sectiune->parent_id ? ($harta[$sectiune->parent_id] ?? null) : null,
                'title'           => $sectiune->getTranslations('title'),
                'note'            => $sectiune->getTranslations('note'),
                'position'        => $sectiune->position,
            ]);

            $harta[$sectiune->id] = $copiaSectiunii->id;

            foreach ($sectiune->items as $element) {
                ContentItem::create([
                    'content_section_id' => $copiaSectiunii->id,
                    'title'      => $element->getTranslations('title'),
                    'body'       => $element->getTranslations('body'),
                    'image_path' => $element->image_path,
                    'link_url'   => $element->link_url,
                    'link_label' => $element->getTranslations('link_label'),
                    'product_id' => $element->product_id,
                    'video_url'  => $element->video_url,
                    'position'   => $element->position,
                ]);
            }
        }

        return $copie;
    }
}
