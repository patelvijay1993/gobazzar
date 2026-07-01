<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Listing extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description',
        'price', 'price_unit', 'location', 'city', 'province', 'image', 'images', 'tags', 'badges',
        'status', 'is_featured', 'is_verified', 'expires_at', 'views',
        'contact_name', 'contact_email', 'contact_phone',
    ];

    protected $casts = [
        'tags'       => 'array',
        'badges'     => 'array',
        'images'     => 'array',
        'is_featured'=> 'boolean',
        'is_verified'=> 'boolean',
        'expires_at' => 'datetime',
    ];

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
        return str_starts_with($this->image, 'http') ? $this->image : Storage::disk('s3')->url($this->image);
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
