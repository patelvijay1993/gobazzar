<?php

namespace App\Filament\Resources\BusinessPostResource\Pages;

use App\Filament\Resources\BusinessPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessPosts extends ListRecords
{
    protected static string $resource = BusinessPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
