<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdStat extends Model
{
    public $timestamps = false;

    protected $fillable = ['advertisement_id', 'date', 'impressions', 'clicks'];

    protected $casts = ['date' => 'date'];

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    public static function recordImpression(int $adId): void
    {
        static::firstOrCreate(
            ['advertisement_id' => $adId, 'date' => today()->toDateString()],
            ['impressions' => 0, 'clicks' => 0]
        )->increment('impressions');
    }

    public static function recordClick(int $adId): void
    {
        static::firstOrCreate(
            ['advertisement_id' => $adId, 'date' => today()->toDateString()],
            ['impressions' => 0, 'clicks' => 0]
        )->increment('clicks');
    }
}
