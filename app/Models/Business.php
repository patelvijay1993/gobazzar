<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class Business extends Model
{
    use Favoritable;
    protected $fillable = [
        'user_id', 'category_id', 'subcategory_id', 'name', 'slug', 'description', 'image', 'images', 'logo',
        'address', 'city', 'province', 'postal_code', 'phone', 'email', 'website', 'map_url', 'lat', 'lon',
        'tags', 'social', 'rating', 'review_count', 'is_verified', 'is_featured',
        'status', 'hours', 'chat_enabled',
    ];

    protected $casts = [
        'tags'         => 'array',
        'images'       => 'array',
        'social'       => 'array',
        'hours'        => 'array',
        'is_verified'  => 'boolean',
        'is_featured'  => 'boolean',
        'chat_enabled' => 'boolean',
        'rating'       => 'decimal:1',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BusinessPost::class);
    }

    public function getLocationAttribute(): string
    {
        return collect([$this->city, $this->province])->filter()->implode(', ');
    }

    public function getImageUrlAttribute(): ?string
    {
        $img = $this->image ?: $this->logo;
        if (!$img) return null;
        if (str_starts_with($img, 'http')) return $img;
        return Storage::disk(config('filesystems.default'))->url($img);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) return null;
        if (str_starts_with($this->logo, 'http')) return $this->logo;
        return Storage::disk(config('filesystems.default'))->url($this->logo);
    }
}

