<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Matrimonial extends Model
{
    protected $fillable = [
        'user_id', 'profile_for', 'name', 'slug', 'gender', 'age', 'height',
        'religion', 'caste', 'mother_tongue', 'education', 'occupation', 'income',
        'marital_status', 'diet', 'city', 'province', 'country', 'about',
        'partner_preference', 'photo', 'photos', 'contact_name', 'contact_phone',
        'contact_email', 'hide_contact', 'status', 'is_featured', 'views', 'expires_at',
    ];

    protected $casts = [
        'photos'       => 'array',
        'is_featured'  => 'boolean',
        'hide_contact' => 'boolean',
        'expires_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getGenderLabelAttribute(): string
    {
        return ucfirst($this->gender);
    }

    public function getMaritalStatusLabelAttribute(): string
    {
        return match($this->marital_status) {
            'never_married' => 'Never Married',
            'divorced'      => 'Divorced',
            'widowed'       => 'Widowed',
            default         => ucfirst($this->marital_status),
        };
    }
}
