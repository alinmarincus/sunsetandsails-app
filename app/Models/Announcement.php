<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Announcement extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'body'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'pinned'       => 'boolean',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Anunturile pe care le vede un membru: cele generale, plus cele
     * legate de croazierele la care participa.
     */
    public function scopeVisibleTo($query, User $user)
    {
        $tripIds = $user->trips()->pluck('trips.id');

        return $query->published()
            ->where(function ($q) use ($tripIds) {
                $q->whereNull('trip_id')->orWhereIn('trip_id', $tripIds);
            })
            ->orderByDesc('pinned')
            ->orderByDesc('published_at');
    }
}
