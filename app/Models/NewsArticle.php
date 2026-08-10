<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    protected $fillable = [
        'article_id', 'title', 'slug', 'description', 'content', 'link',
        'image_url', 'video_url', 'category', 'keywords', 'language', 'country',
        'source_id', 'source_name', 'source_icon', 'source_url',
        'pub_date', 'status', 'is_featured', 'views',
    ];

    protected $casts = [
        'category'     => 'array',
        'keywords'     => 'array',
        'country'      => 'array',
        'is_featured'  => 'boolean',
        'pub_date'     => 'datetime',
    ];

    public function getReadTimeAttribute(): string
    {
        $words = str_word_count(strip_tags($this->description ?? ''));
        $minutes = max(1, ceil($words / 200));
        return $minutes.' min read';
    }
}
