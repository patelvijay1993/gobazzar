<?php

namespace App\Traits;

use App\Models\UserFavorite;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Favoritable
{
    public function favorites(): MorphMany
    {
        return $this->morphMany(UserFavorite::class, 'favoriteable');
    }

    public function favoritesCount(): int
    {
        return $this->favorites()->count();
    }

    public function isFavoritedBy(int $userId): bool
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}
