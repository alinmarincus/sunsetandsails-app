<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Trip extends Model
{
    use HasTranslations;

    /** Campuri cu versiune RO si EN */
    public array $translatable = ['title', 'destination', 'summary', 'description'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    /* ── Relatii ────────────────────────────────────────────────────────── */

    public function days(): HasMany
    {
        return $this->hasMany(TripDay::class)->orderBy('day_number');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['status', 'notes'])
            ->withTimestamps();
    }

    /* Listele atribuite croazierei. Aceeași listă poate fi folosită și de
       alte croaziere — de aceea legătura stă aici, nu în listă. */

    public function packingList(): BelongsTo
    {
        return $this->belongsTo(ContentList::class, 'packing_list_id');
    }

    public function menuList(): BelongsTo
    {
        return $this->belongsTo(ContentList::class, 'menu_list_id');
    }

    public function infoList(): BelongsTo
    {
        return $this->belongsTo(ContentList::class, 'info_list_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    /* ── Scopes ─────────────────────────────────────────────────────────── */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('start_date', '>', today())->orderBy('start_date');
    }

    /* ── Stare, dupa data ───────────────────────────────────────────────── */

    public function isPast(): bool
    {
        return $this->end_date->isBefore(today());
    }

    public function isCurrent(): bool
    {
        return ! $this->start_date->isAfter(today()) && ! $this->end_date->isBefore(today());
    }

    public function isUpcoming(): bool
    {
        return $this->start_date->isAfter(today());
    }

    public function nights(): int
    {
        return (int) $this->start_date->diffInDays($this->end_date);
    }

    /** Cate zile mai sunt pana la plecare (0 daca a inceput deja). */
    public function daysUntilStart(): int
    {
        return $this->start_date->isFuture()
            ? (int) today()->diffInDays($this->start_date)
            : 0;
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }
}
