<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt', 'body', 'type', 'status',
        'is_pinned', 'link_url', 'link_text', 'starts_at', 'ends_at', 'views',
    ];

    protected $casts = [
        'is_pinned'  => 'boolean',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getReadTimeAttribute(): string
    {
        $words = str_word_count(strip_tags($this->body ?? ''));
        $minutes = max(1, ceil($words / 200));
        return $minutes.' min read';
    }

    /** Published and within its active date window (if any). */
    public function scopeLive($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }
}
