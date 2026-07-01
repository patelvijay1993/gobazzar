<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollOption extends Model
{
    protected $fillable = ['poll_id', 'label', 'votes', 'sort_order'];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function getPercentageAttribute(): int
    {
        $total = $this->poll->total_votes;
        if ($total === 0) return 0;
        return (int) round(($this->votes / $total) * 100);
    }
}
