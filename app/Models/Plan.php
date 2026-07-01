<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'slug', 'name', 'icon', 'icon_bg', 'price', 'stripe_price_id', 'period', 'tagline',
        'is_popular', 'is_active', 'sort_order', 'features',
        'post_days', 'max_listings', 'max_images', 'biz_listings',
        'verified_badge', 'featured_placement', 'unlimited_posts',
        'priority_support', 'analytics', 'auto_renew',
        'favorites', 'featured_credits', 'bulk_upload',
    ];

    protected $casts = [
        'is_popular'          => 'boolean',
        'is_active'           => 'boolean',
        'verified_badge'      => 'boolean',
        'featured_placement'  => 'boolean',
        'unlimited_posts'     => 'boolean',
        'priority_support'    => 'boolean',
        'analytics'           => 'boolean',
        'auto_renew'          => 'boolean',
        'favorites'           => 'boolean',
        'bulk_upload'         => 'boolean',
        'features'            => 'array',
        'price'               => 'decimal:2',
    ];

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }
}
