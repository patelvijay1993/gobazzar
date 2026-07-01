<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar',
        'city', 'province', 'bio', 'is_admin', 'is_active',
        'plan', 'plan_expires_at',
        'stripe_customer_id', 'stripe_subscription_id', 'subscription_status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'is_active'         => 'boolean',
            'plan_expires_at'   => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin || $this->id === 1;
    }

    // ── Plan resolution ───────────────────────────────────────────

    /** free | verified | power_seller */
    public function activePlan(): string
    {
        if (!$this->plan || $this->plan === 'free') return 'free';
        if ($this->plan_expires_at && $this->plan_expires_at->isPast()) return 'free';
        return $this->plan;
    }

    public function isSubscribed(): bool
    {
        return $this->activePlan() !== 'free';
    }

    public function planModel(): ?Plan
    {
        return Plan::findBySlug($this->activePlan());
    }

    public function planName(): string
    {
        return $this->planModel()?->name ?? 'Free';
    }

    // ── Classifieds (Listings) ────────────────────────────────────

    /** Days a classified stays live. Null = permanent (Power Seller auto-renew). */
    public function postDays(): ?int
    {
        $plan = $this->planModel();
        if (!$plan) return 3;
        return $plan->post_days === 0 ? null : $plan->post_days;
    }

    /** Max simultaneous active listings allowed by plan. */
    public function maxListings(): int
    {
        return $this->planModel()?->max_listings ?? 3;
    }

    /** Max images per listing/post allowed by plan. */
    public function maxImages(): int
    {
        return $this->planModel()?->max_images ?? 3;
    }

    /** Count of currently active (non-expired) listings. */
    public function activeListingCount(): int
    {
        return $this->listings()
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();
    }

    public function canPostListing(): bool
    {
        return $this->activeListingCount() < $this->maxListings();
    }

    // ── Business Directory ────────────────────────────────────────

    /** Max business listings allowed by plan. */
    public function maxBusinessListings(): int
    {
        return $this->planModel()?->biz_listings ?? 0;
    }

    /** Count of currently active businesses. */
    public function activeBusinessCount(): int
    {
        return $this->hasMany(Business::class)->where('status', 'active')->count();
    }

    public function canPostBusiness(): bool
    {
        return $this->maxBusinessListings() > 0
            && $this->activeBusinessCount() < $this->maxBusinessListings();
    }

    // ── Events ───────────────────────────────────────────────────

    /** All plans can post events (free = 3-day expiry, paid = 30-day or permanent). */
    public function canPostEvent(): bool
    {
        return true;
    }

    // ── Business Posts ────────────────────────────────────────────

    /** Business posts require having at least one active business. */
    public function canPostBusinessPost(): bool
    {
        return $this->hasMany(Business::class)->where('status', 'active')->exists();
    }

    // ── Plan features ─────────────────────────────────────────────

    public function hasVerifiedBadge(): bool
    {
        return (bool) ($this->planModel()?->verified_badge ?? false);
    }

    public function hasAutoRenew(): bool
    {
        return (bool) ($this->planModel()?->auto_renew ?? false);
    }

    public function hasPrioritySearch(): bool
    {
        return (bool) ($this->planModel()?->featured_placement ?? false);
    }

    public function hasAnalytics(): bool
    {
        return (bool) ($this->planModel()?->analytics ?? false);
    }

    public function hasFavorites(): bool
    {
        return (bool) ($this->planModel()?->favorites ?? false);
    }

    public function featuredCredits(): int
    {
        return $this->planModel()?->featured_credits ?? 0;
    }

    public function hasBulkUpload(): bool
    {
        return (bool) ($this->planModel()?->bulk_upload ?? false);
    }

    // ── Relationships ─────────────────────────────────────────────

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    // ── Custom verification email ─────────────────────────────────

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }
}
