<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedCreditLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'listing_id', 'featured_at', 'unfeatured_at'];

    protected $casts = [
        'featured_at'   => 'datetime',
        'unfeatured_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
