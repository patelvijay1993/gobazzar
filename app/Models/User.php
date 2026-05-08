<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar',
        'city', 'province', 'bio', 'is_admin', 'is_active',
        'plan', 'plan_expires_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'is_active'         => 'boolean',
            'plan_expires_at'   => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin || $this->id === 1;
    }

    /** free | basic | premium | business */
    public function activePlan(): string
    {
        if ($this->plan === 'free') return 'free';
        if ($this->plan_expires_at && $this->plan_expires_at->isPast()) return 'free';
        return $this->plan;
    }

    public function isSubscribed(): bool
    {
        return $this->activePlan() !== 'free';
    }

    /** Days a free post stays live */
    public const FREE_POST_DAYS = 7;

    /** Post duration by plan (null = permanent) */
    public function postDays(): ?int
    {
        return match ($this->activePlan()) {
            'free'     => self::FREE_POST_DAYS,
            'basic'    => 30,
            'premium'  => 90,
            'business' => null,
            default    => self::FREE_POST_DAYS,
        };
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }
}
