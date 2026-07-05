<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlaggedPost extends Model
{
    protected $fillable = [
        'user_id', 'post_type', 'title', 'description',
        'flag_reason', 'flag_field', 'flag_message',
        'raw_data', 'ip', 'status',
    ];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
