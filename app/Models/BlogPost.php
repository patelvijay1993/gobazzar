<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt', 'body', 'image',
        'category', 'tags', 'status', 'is_featured', 'views', 'published_at',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getReadTimeAttribute(): string
    {
        $words = str_word_count(strip_tags($this->body));
        $minutes = max(1, ceil($words / 200));
        return $minutes.' min read';
    }
}
