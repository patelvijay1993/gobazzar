<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Job extends Model
{
    protected $table = 'job_listings';

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'company', 'company_logo',
        'description', 'requirements', 'location', 'city', 'province',
        'job_type', 'work_mode', 'salary', 'experience', 'tags',
        'apply_email', 'apply_url', 'is_featured', 'status', 'expires_at', 'views',
    ];

    protected $casts = [
        'tags'        => 'array',
        'is_featured' => 'boolean',
        'expires_at'  => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getJobTypeLabelAttribute(): string
    {
        return match($this->job_type) {
            'full-time'  => 'Full Time',
            'part-time'  => 'Part Time',
            'contract'   => 'Contract',
            'freelance'  => 'Freelance',
            'internship' => 'Internship',
            default      => $this->job_type,
        };
    }

    public function getWorkModeLabelAttribute(): string
    {
        return match($this->work_mode) {
            'onsite' => 'On-site',
            'remote' => 'Remote',
            'hybrid' => 'Hybrid',
            default  => $this->work_mode,
        };
    }

    /** Active and not past expiry. */
    public function scopeLive($query)
    {
        return $query->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->company_logo) return null;
        return str_starts_with($this->company_logo, 'http') ? $this->company_logo : Storage::disk('s3')->url($this->company_logo);
    }
}
