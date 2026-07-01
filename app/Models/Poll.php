<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = ['question', 'is_active', 'expires_at', 'sort_order', 'scope', 'province', 'city'];

    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('sort_order');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function getTotalVotesAttribute(): int
    {
        return $this->options->sum('votes');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /** Best matching active poll for the given location (city > province > canada) */
    public static function current(?string $city = null, ?string $province = null): ?self
    {
        $base = static::with('options')
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));

        // City-specific poll
        if ($city && $province) {
            $poll = (clone $base)->where('scope', 'city')
                ->where('province', $province)
                ->where('city', $city)
                ->orderBy('sort_order')->latest()->first();
            if ($poll) return $poll;
        }

        // Province-wide poll
        if ($province) {
            $poll = (clone $base)->where('scope', 'province')
                ->where('province', $province)
                ->orderBy('sort_order')->latest()->first();
            if ($poll) return $poll;
        }

        // Canada-wide poll
        return (clone $base)->where('scope', 'canada')
            ->orderBy('sort_order')->latest()->first();
    }
}
