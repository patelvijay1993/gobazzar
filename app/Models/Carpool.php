<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Carpool extends Model
{
    use Favoritable, SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'image',
        'ride_type', 'from_city', 'from_province', 'to_city', 'to_province',
        'travel_date', 'is_recurring', 'recurring_days',
        'seats_available', 'price', 'vehicle',
        'contact_name', 'contact_phone', 'contact_email',
        'tags', 'is_featured', 'chat_enabled', 'status',
        'expires_at', 'inactive_at', 'views',
    ];

    protected $casts = [
        'tags'            => 'array',
        'is_featured'     => 'boolean',
        'is_recurring'    => 'boolean',
        'chat_enabled'    => 'boolean',
        'travel_date'     => 'datetime',
        'expires_at'      => 'datetime',
        'inactive_at'     => 'datetime',
        'seats_available' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteAttribute(): string
    {
        return "{$this->from_city} → {$this->to_city}";
    }

    public function getIsPastAttribute(): bool
    {
        return $this->travel_date->isPast();
    }

    public function getFormattedPriceAttribute(): ?string
    {
        if (!$this->price) return null;
        $p = trim($this->price);
        if ($p === '' || strtolower($p) === 'free' || preg_match('/^[^\d]/', $p)) return $p;
        return '$' . $p;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (str_starts_with($this->image, 'http')) return $this->image;
        return Storage::disk(config('filesystems.default'))->url($this->image);
    }
}
