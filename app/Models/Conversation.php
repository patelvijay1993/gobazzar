<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\BusinessPost;

class Conversation extends Model
{
    protected $fillable = [
        'conversable_type', 'conversable_id',
        'buyer_id', 'seller_id',
        'buyer_last_read', 'seller_last_read',
    ];

    protected $casts = [
        'buyer_last_read'  => 'datetime',
        'seller_last_read' => 'datetime',
    ];

    public function conversable(): MorphTo
    {
        return $this->morphTo();
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    public function unreadCountFor(int $userId): int
    {
        $lastRead = (int)$this->buyer_id === $userId ? $this->buyer_last_read : $this->seller_last_read;

        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->when($lastRead, fn($q) => $q->where('created_at', '>', $lastRead))
            ->count();
    }

    public function markReadFor(int $userId): void
    {
        if ((int)$this->buyer_id === $userId) {
            $this->update(['buyer_last_read' => now()]);
        } else {
            $this->update(['seller_last_read' => now()]);
        }
    }

    // Title of the conversable item (listing title / event title / business name)
    public function getSubjectTitleAttribute(): string
    {
        $item = $this->conversable;
        if (!$item) return 'Deleted';
        return $item->title ?? $item->name ?? 'Item';
    }

    // URL to view the conversable item
    public function getSubjectUrlAttribute(): string
    {
        $item = $this->conversable;
        if (!$item) return '#';
        return match(get_class($item)) {
            Listing::class      => route('classifieds.show', $item),
            Event::class        => route('events.show', $item),
            Business::class     => route('directory.show', $item),
            BusinessPost::class => route('directory.post', [$item->business->slug, $item->slug]),
            default             => '#',
        };
    }
}
