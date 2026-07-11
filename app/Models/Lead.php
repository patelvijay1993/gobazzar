<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'category', 'city', 'province', 'address',
        'phone', 'email', 'website', 'rating', 'review_count',
        'google_place_id', 'google_maps_url',
        'status', 'contact_method', 'notes', 'last_contacted_at', 'source',
    ];

    protected $casts = [
        'last_contacted_at' => 'datetime',
        'rating'            => 'decimal:1',
    ];

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new'            => 'gray',
            'contacted'      => 'info',
            'interested'     => 'success',
            'not_interested' => 'danger',
            'converted'      => 'warning',
            default          => 'gray',
        };
    }
}
