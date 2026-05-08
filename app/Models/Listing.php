<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
