<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * Secțiune dintr-o listă. O secțiune cu părinte e, practic, o subsecțiune.
 */
class ContentSection extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'note'];

    protected $guarded = [];

    /**
     * O subsecțiune se salvează prin relația `children`, care completează doar
     * `parent_id`. Lista o moștenește de la părinte, ca să nu rămână orfană.
     */
    protected static function booted(): void
    {
        static::saving(function (self $sectiune) {
            if (blank($sectiune->content_list_id) && $sectiune->parent_id) {
                $sectiune->content_list_id = self::whereKey($sectiune->parent_id)
                    ->value('content_list_id');
            }
        });
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(ContentList::class, 'content_list_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContentItem::class)->orderBy('position');
    }

    public function isSubsection(): bool
    {
        return $this->parent_id !== null;
    }
}
