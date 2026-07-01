<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryField extends Model
{
    protected $fillable = [
        'category_id', 'label', 'key', 'type', 'options',
        'placeholder', 'is_required', 'sort_order',
    ];

    protected $casts = [
        'options'     => 'array',
        'is_required' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
