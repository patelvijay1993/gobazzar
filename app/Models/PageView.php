<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = ['viewable_type', 'viewable_id', 'page', 'user_id', 'ip', 'device', 'referrer', 'viewed_at'];

    protected $casts = ['viewed_at' => 'datetime'];

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Track individual post views (Listing, Job, Event, Business)
    public static function record(Model $model, Request $request): void
    {
        // Skip admin users
        $user = $request->user();
        if ($user && $user->role === 'admin') return;

        $ip = $request->ip();

        $alreadySeen = static::where('viewable_type', get_class($model))
            ->where('viewable_id', $model->id)
            ->where('ip', $ip)
            ->where('viewed_at', '>=', now()->startOfDay())
            ->exists();

        if ($alreadySeen) return;

        static::create([
            'viewable_type' => get_class($model),
            'viewable_id'   => $model->id,
            'page'          => null,
            'user_id'       => $user?->id,
            'ip'            => $ip,
            'device'        => static::detectDevice($request),
            'referrer'      => substr($request->headers->get('referer', ''), 0, 500) ?: null,
            'viewed_at'     => now(),
        ]);
    }

    // Track section/page views (classifieds index, jobs index etc.)
    public static function recordPage(string $page, Request $request): void
    {
        // Skip admin users
        $user = $request->user();
        if ($user && $user->role === 'admin') return;

        $ip = $request->ip();

        $alreadySeen = static::whereNull('viewable_type')
            ->where('page', $page)
            ->where('ip', $ip)
            ->where('viewed_at', '>=', now()->startOfDay())
            ->exists();

        if ($alreadySeen) return;

        static::create([
            'viewable_type' => null,
            'viewable_id'   => null,
            'page'          => $page,
            'user_id'       => $user?->id,
            'ip'            => $ip,
            'device'        => static::detectDevice($request),
            'referrer'      => substr($request->headers->get('referer', ''), 0, 500) ?: null,
            'viewed_at'     => now(),
        ]);
    }

    private static function detectDevice(Request $request): string
    {
        $ua = $request->userAgent() ?? '';
        if (preg_match('/Tablet|iPad/i', $ua)) return 'tablet';
        if (preg_match('/Mobile|Android|iPhone/i', $ua)) return 'mobile';
        return 'desktop';
    }
}
