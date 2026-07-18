<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Advertisement extends Model
{
    protected $fillable = [
        'title', 'image', 'click_url', 'position', 'category_type',
        'scope', 'province', 'city',
        'is_active', 'starts_at', 'ends_at', 'sort_order', 'slide_duration',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'starts_at'  => 'date',
        'ends_at'    => 'date',
    ];

    // Fixed sizes per position
    public const SIZES = [
        'home-banner' => ['width' => 1200, 'height' => 120],
        'sidebar'     => ['width' => 300,  'height' => 250],
        'inline'      => ['width' => 800,  'height' => 120],
    ];

    public function stats(): HasMany
    {
        return $this->hasMany(AdStat::class);
    }

    public function getImageUrlAttribute(): string
    {
        // New uploads go to public disk. S3 uploads (legacy) are re-uploaded via admin.
        if (Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        // Fallback: try S3 signed URL for legacy images
        try {
            return Storage::disk('s3')->temporaryUrl($this->image, now()->addHours(6));
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Get active ads for a given position + location.
     * Priority: city → province → canada
     */
    public static function forPosition(
        string $position,
        ?string $city = null,
        ?string $province = null,
        ?string $categoryType = null  // classifieds|jobs|events|directory|null=all
    ) {
        $today = now()->toDateString();

        $base = static::where('position', $position)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $today))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $today))
            ->where(fn ($q) => $q->where('category_type', 'all')
                ->when($categoryType, fn ($q2) => $q2->orWhere('category_type', $categoryType)))
            ->orderBy('sort_order');

        $ads = collect();

        if ($city && $province) {
            $ads = $ads->merge(
                (clone $base)->where('scope', 'city')
                    ->where('province', $province)->where('city', $city)->get()
            );
        }

        if ($province) {
            $ads = $ads->merge(
                (clone $base)->where('scope', 'province')->where('province', $province)->get()
            );
        }

        $ads = $ads->merge((clone $base)->where('scope', 'canada')->get());

        return $ads->unique('id');
    }
}

