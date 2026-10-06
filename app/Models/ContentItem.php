<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * Un element dintr-o listă.
 *
 * Aceleași câmpuri servesc toate tipurile, fiecare folosind ce are nevoie:
 * la necesar — titlu, imagine și link către magazin; la meniu — preparat cu
 * poză și descriere; la informații utile — text și link video.
 */
class ContentItem extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'body', 'link_label'];

    protected $guarded = [];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ContentSection::class, 'content_section_id');
    }

    public function ticks(): HasMany
    {
        return $this->hasMany(ChecklistTick::class);
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    /** A fost bifat de membrul ăsta, la croaziera asta? */
    public function isTickedBy(int $userId, int $tripId): bool
    {
        return $this->ticks()
            ->where('user_id', $userId)
            ->where('trip_id', $tripId)
            ->whereNotNull('checked_at')
            ->exists();
    }
}
