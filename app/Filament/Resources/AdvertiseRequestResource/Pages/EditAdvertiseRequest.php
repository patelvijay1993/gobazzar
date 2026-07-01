<?php

namespace App\Filament\Resources\AdvertiseRequestResource\Pages;

use App\Filament\Resources\AdvertiseRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdvertiseRequest extends EditRecord
{
    protected static string $resource = AdvertiseRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
