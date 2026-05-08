<?php

namespace App\Filament\Resources\MatrimonialResource\Pages;

use App\Filament\Resources\MatrimonialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMatrimonial extends CreateRecord
{
    protected static string $resource = MatrimonialResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
