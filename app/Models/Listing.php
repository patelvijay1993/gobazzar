<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Listing extends Model
{
    use Favoritable;
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

    public function listingViews(): HasMany
    {
        return $this->hasMany(ListingView::class);
    }

    public function uniqueViewsCount(): int
    {
        return $this->listingViews()->distinct('ip')->count('ip');
    }

    public function viewsLast30Days(): int
    {
        return $this->listingViews()->where('viewed_at', '>=', now()->subDays(30))->count();
    }

    public function getFormattedPriceAttribute(): ?string
    {
        if (!$this->price) return null;
        $p = trim($this->price);
        if ($p === '' || preg_match('/^[^\d]/', $p)) return $p; // already has $ or is text
        return '$' . $p;
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

