<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function getLocationAttribute(): string
    {
        return collect([$this->city, $this->province])->filter()->implode(', ');
    }
}
