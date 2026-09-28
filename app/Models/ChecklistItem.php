<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class ChecklistItem extends Model
{
    use HasTranslations;

    public array $translatable = ['label', 'hint'];

    protected $guarded = [];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'checklist_user')
            ->withPivot('checked_at')
            ->withTimestamps();
    }

    /** Sablon global, care se copiaza la fiecare croaziera noua. */
    public function scopeTemplate($query)
    {
        return $query->whereNull('trip_id');
    }

    public function isCheckedBy(User $user): bool
    {
        return $this->users()
            ->where('users.id', $user->id)
            ->whereNotNull('checklist_user.checked_at')
            ->exists();
    }
}
