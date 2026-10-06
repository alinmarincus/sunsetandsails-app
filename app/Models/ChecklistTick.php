<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bifa unui membru pe un element, la o anumită croazieră.
 * Legătura cu croaziera e esențială: aceeași listă poate fi folosită de
 * mai multe ieșiri, iar bifele nu trebuie să se amestece între ele.
 */
class ChecklistTick extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['checked_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class, 'content_item_id');
    }
}
