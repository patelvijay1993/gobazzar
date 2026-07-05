<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class Event extends Model
{
    use Favoritable;
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description', 'image',
        'start_date', 'end_date', 'venue', 'city', 'province',
        'price', 'organizer', 'organizer_phone', 'organizer_email',
        'website', 'tags', 'is_featured', 'status', 'views',
    ];

    protected $casts = [
        'tags'        => 'array',
        'is_featured' => 'boolean',
        'start_date'  => 'datetime',
        'end_date'    => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLocationAttribute(): string
    {
        return collect([$this->venue, $this->city, $this->province])->filter()->implode(', ');
    }

    public function getIsPastAttribute(): bool
    {
        return $this->start_date->isPast();
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

