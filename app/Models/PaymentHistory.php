<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentHistory extends Model
{
    protected $table = 'payment_history';

    protected $fillable = [
        'user_id', 'stripe_payment_intent_id', 'stripe_invoice_id',
        'stripe_subscription_id', 'plan_slug', 'plan_name',
        'amount', 'currency', 'status', 'description', 'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAmountFormattedAttribute(): string
    {
        return '$'.number_format($this->amount, 2).' '.strtoupper($this->currency);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'paid'     => ['label' => 'Paid',     'color' => '#15803d', 'bg' => '#dcfce7'],
            'failed'   => ['label' => 'Failed',   'color' => '#b91c1c', 'bg' => '#fee2e2'],
            'refunded' => ['label' => 'Refunded', 'color' => '#92400e', 'bg' => '#fef9c3'],
            default    => ['label' => ucfirst($this->status), 'color' => '#555', 'bg' => '#f3f4f6'],
        };
    }
}
