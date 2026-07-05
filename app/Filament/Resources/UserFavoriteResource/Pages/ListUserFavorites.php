<?php

namespace App\Filament\Resources\UserFavoriteResource\Pages;

use App\Filament\Resources\UserFavoriteResource;
use Filament\Resources\Pages\ListRecords;

class ListUserFavorites extends ListRecords
{
    protected static string $resource = UserFavoriteResource::class;
}
