<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code', 'plan_slug', 'duration_months', 'max_uses',
        'used_count', 'expires_at', 'is_active', 'description',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function isUsable(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses > 0 && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    public static function findValid(string $code): ?self
    {
        $promo = static::whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();
        return $promo?->isUsable() ? $promo : null;
    }

    public function apply(User $user): void
    {
        $expiresAt = now()->addMonths($this->duration_months);
        $user->update([
            'plan'            => $this->plan_slug,
            'plan_expires_at' => $expiresAt,
        ]);
        $this->increment('used_count');
    }
}
