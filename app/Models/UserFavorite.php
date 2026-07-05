<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserFavorite extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'favoriteable_type', 'favoriteable_id', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteable(): MorphTo
    {
        return $this->morphTo();
    }
}
