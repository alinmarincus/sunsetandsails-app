<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class TripDay extends Model
{
    use HasTranslations;

    public array $translatable = ['port', 'note'];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
