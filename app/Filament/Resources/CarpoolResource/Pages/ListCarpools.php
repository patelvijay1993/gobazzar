<?php

namespace App\Filament\Resources\CarpoolResource\Pages;

use App\Filament\Resources\CarpoolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarpools extends ListRecords
{
    protected static string $resource = CarpoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
