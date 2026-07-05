<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id',
        'subject_label', 'url', 'ip', 'device', 'meta', 'created_at',
    ];

    protected $casts = [
        'meta'       => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(Request $request, string $action, array $extra = []): void
    {
        // Never log admin users
        $user = $request->user();
        if ($user && $user->role === 'admin') return;

        $ua = $request->userAgent() ?? '';
        if (preg_match('/Tablet|iPad/i', $ua))          $device = 'tablet';
        elseif (preg_match('/Mobile|Android|iPhone/i', $ua)) $device = 'mobile';
        else                                              $device = 'desktop';

        static::create([
            'user_id'       => $user?->id,
            'action'        => $action,
            'subject_type'  => $extra['subject_type']  ?? null,
            'subject_id'    => $extra['subject_id']    ?? null,
            'subject_label' => $extra['subject_label'] ?? null,
            'url'           => substr($request->fullUrl(), 0, 500),
            'ip'            => $request->ip(),
            'device'        => $device,
            'meta'          => $extra['meta'] ?? null,
            'created_at'    => now(),
        ]);
    }

    // Action label for display
    public function actionLabel(): string
    {
        return match($this->action) {
            'viewed_listing'  => 'Viewed Classified',
            'viewed_job'      => 'Viewed Job',
            'viewed_event'    => 'Viewed Event',
            'viewed_business' => 'Viewed Business',
            'searched'        => 'Searched',
            'registered'      => 'Registered',
            'login'           => 'Logged In',
            'logout'          => 'Logged Out',
            'post_created'    => 'Created Post',
            'post_updated'    => 'Updated Post',
            'chat_started'    => 'Started Chat',
            default           => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}
