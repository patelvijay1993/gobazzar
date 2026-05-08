<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'type', 'is_active', 'sort_order'];

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
