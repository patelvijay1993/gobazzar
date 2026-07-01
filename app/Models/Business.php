<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class Business extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'name', 'slug', 'description', 'image', 'images', 'logo',
        'address', 'city', 'province', 'phone', 'email', 'website',
        'tags', 'rating', 'review_count', 'is_verified', 'is_featured',
        'status', 'hours',
    ];

    protected $casts = [
        'tags'        => 'array',
        'images'      => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'rating'      => 'decimal:1',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
        return str_starts_with($img, 'http') ? $img : Storage::disk('s3')->url($img);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) return null;
        return str_starts_with($this->logo, 'http') ? $this->logo : Storage::disk('s3')->url($this->logo);
    }
}
