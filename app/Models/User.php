<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar', 'google_id',
        'city', 'province', 'bio', 'is_admin', 'is_active',
        'plan', 'plan_expires_at',
        'stripe_customer_id', 'stripe_subscription_id', 'subscription_status',
        'featured_credits_used', 'featured_credits_reset_at',
        'hide_phone', 'hide_email',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'           => 'datetime',
            'password'                    => 'hashed',
            'is_admin'                    => 'boolean',
            'is_active'                   => 'boolean',
            'hide_phone'                  => 'boolean',
            'hide_email'                  => 'boolean',
            'plan_expires_at'             => 'datetime',
            'featured_credits_reset_at'   => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true;
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

    /** Count of non-expired listings + active jobs + active events (shared plan budget). */
    public function activeListingCount(): int
    {
        $classifieds = $this->listings()
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();

        $jobs = $this->hasMany(Job::class)
            ->where('status', 'active')
            ->count();

        $events = $this->hasMany(Event::class)
            ->where('status', 'active')
            ->count();

        return $classifieds + $jobs + $events;
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

    /** Credits still available this billing cycle (auto-resets monthly). */
    public function featuredCreditsRemaining(): int
    {
        $this->maybeResetCredits();
        return max(0, $this->featuredCredits() - $this->featured_credits_used);
    }

    public function canFeatureListing(): bool
    {
        return $this->featuredCredits() > 0 && $this->featuredCreditsRemaining() > 0;
    }

    /** Reset used credits if a month has passed since last reset. */
    public function maybeResetCredits(): void
    {
        $resetAt = $this->featured_credits_reset_at;
        if (!$resetAt || $resetAt->isPast()) {
            $this->update([
                'featured_credits_used'     => 0,
                'featured_credits_reset_at' => now()->addMonth(),
            ]);
        }
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

    public function userFavorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function featuredCreditLogs(): HasMany
    {
        return $this->hasMany(FeaturedCreditLog::class);
    }

    // ── Custom verification email ─────────────────────────────────

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) return null;
        if (str_starts_with($this->avatar, 'http')) return $this->avatar;
        return Storage::disk(config('filesystems.default'))->url($this->avatar);
    }
}

