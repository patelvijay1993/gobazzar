<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingView extends Model
{
    public $timestamps = false;

    protected $fillable = ['listing_id', 'user_id', 'ip', 'referrer', 'device', 'viewed_at'];

    protected $casts = ['viewed_at' => 'datetime'];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(Listing $listing, \Illuminate\Http\Request $request): void
    {
        $ip = $request->ip();

        // One unique view per IP per listing per day
        $alreadySeen = static::where('listing_id', $listing->id)
            ->where('ip', $ip)
            ->where('viewed_at', '>=', now()->startOfDay())
            ->exists();

        if ($alreadySeen) return;

        $ua = $request->userAgent() ?? '';
        if (preg_match('/Mobile|Android|iPhone|iPad/i', $ua)) {
            $device = 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $ua)) {
            $device = 'tablet';
        } else {
            $device = 'desktop';
        }

        static::create([
            'listing_id' => $listing->id,
            'user_id'    => $request->user()?->id,
            'ip'         => $ip,
            'referrer'   => substr($request->headers->get('referer', ''), 0, 500) ?: null,
            'device'     => $device,
            'viewed_at'  => now(),
        ]);

        // Keep the raw views counter in sync too
        $listing->increment('views');
    }
}
