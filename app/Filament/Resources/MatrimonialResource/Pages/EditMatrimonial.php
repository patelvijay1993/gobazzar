<?php

namespace App\Filament\Resources\MatrimonialResource\Pages;

use App\Filament\Resources\MatrimonialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMatrimonial extends EditRecord
{
    protected static string $resource = MatrimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
