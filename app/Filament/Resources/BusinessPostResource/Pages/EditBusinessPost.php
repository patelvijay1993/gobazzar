<?php

namespace App\Filament\Resources\BusinessPostResource\Pages;

use App\Filament\Resources\BusinessPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessPost extends EditRecord
{
    protected static string $resource = BusinessPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
