<?php

namespace App\Filament\Resources\CarpoolResource\Pages;

use App\Filament\Resources\CarpoolResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCarpool extends CreateRecord
{
    protected static string $resource = CarpoolResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }
        return $data;
    }
}
