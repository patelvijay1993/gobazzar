<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['province', 'city', 'is_active', 'sort_order', 'city_image'];

    protected $casts = ['is_active' => 'boolean'];

    public static function activeCities(?string $province = null): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)
            ->when($province, fn ($q) => $q->where('province', $province))
            ->orderBy('sort_order')
            ->orderBy('city')
            ->pluck('city');
    }

    public static function activeProvinces(): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)
            ->distinct()
            ->orderBy('province')
            ->pluck('province');
    }
}
