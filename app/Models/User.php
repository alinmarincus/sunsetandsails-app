<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

// Atentie: 'is_admin' se scrie doar din panoul de admin si din comanda
// club:admin. Formularele publice construiesc explicit campurile permise,
// deci nu poate fi setat dintr-o cerere HTTP.
#[Fillable([
    'name', 'email', 'phone', 'password', 'avatar_path', 'locale',
    'status', 'wall_public', 'photo_consent_at', 'is_admin',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'photo_consent_at'  => 'datetime',
            'joined_club_at'    => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'wall_public'       => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->wall_slug ??= Str::lower(Str::random(16));
            $user->joined_club_at ??= now();
        });
    }

    /* ── Acces in panoul de admin ───────────────────────────────────────── */

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    /* ── Relatii ────────────────────────────────────────────────────────── */

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class)
            ->withPivot(['status', 'notes'])
            ->withTimestamps();
    }

    public function ticks(): HasMany
    {
        return $this->hasMany(ChecklistTick::class);
    }

    /* ── Croaziere, dupa data ───────────────────────────────────────────── */

    /** Croaziera in desfasurare azi, daca exista. */
    public function currentTrip(): ?Trip
    {
        return $this->trips()
            ->wherePivot('status', 'confirmed')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->orderBy('start_date')
            ->first();
    }

    /** Croazierele viitoare, cea mai apropiata prima. */
    public function upcomingTrips()
    {
        return $this->trips()
            ->wherePivot('status', 'confirmed')
            ->whereDate('start_date', '>', today())
            ->orderBy('start_date')
            ->get();
    }

    /** Croazierele incheiate, cea mai recenta prima. */
    public function pastTrips()
    {
        return $this->trips()
            ->wherePivot('status', 'confirmed')
            ->whereDate('end_date', '<', today())
            ->orderByDesc('end_date')
            ->get();
    }

    /* ── Helpers ────────────────────────────────────────────────────────── */

    public function avatarUrl(): string
    {
        return $this->avatar_path
            ? asset('storage/' . $this->avatar_path)
            : asset('images/avatar-placeholder.svg');
    }

    public function firstName(): string
    {
        return Str::before(trim($this->name), ' ') ?: $this->name;
    }

    public function isMember(): bool
    {
        return $this->status === 'member';
    }
}
