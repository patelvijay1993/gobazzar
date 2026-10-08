<?php

namespace App\Filament\Resources\BusinessPostResource\Pages;

use App\Filament\Resources\BusinessPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBusinessPost extends CreateRecord
{
    protected static string $resource = BusinessPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }
        return $data;
    }
}
