<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'reportable_type', 'reportable_id',
        'reason', 'details', 'status', 'reporter_ip',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function reasons(): array
    {
        return [
            'pornography' => '🔞 Pornography / Adult Content',
            'harmful'     => '⚠️ Harmful / Dangerous',
            'misleading'  => '❌ Misleading / False Info',
            'spam'        => '📢 Spam / Repetitive',
            'fake'        => '🎭 Fake / Scam',
            'other'       => '📝 Other',
        ];
    }
}
