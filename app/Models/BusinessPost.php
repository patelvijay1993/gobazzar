<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessPost extends Model
{
    protected $fillable = [
        'business_id', 'user_id', 'category_id', 'title', 'slug',
        'description', 'custom_fields', 'price', 'price_unit', 'image', 'images',
        'status', 'is_featured', 'views', 'expires_at',
    ];

    protected $casts = [
        'images'        => 'array',
        'custom_fields' => 'array',
        'is_featured'   => 'boolean',
        'expires_at'    => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (str_starts_with($this->image, 'http')) return $this->image;
        return Storage::disk(config('filesystems.default'))->url($this->image);
    }

    /** Active and not past expiry. */
    public function scopeLive($query)
    {
        return $query->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

