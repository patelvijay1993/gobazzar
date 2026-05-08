<?php

namespace App\Filament\Resources\BusinessCategoryResource\Pages;

use App\Filament\Resources\BusinessCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBusinessCategory extends CreateRecord
{
    protected static string $resource = BusinessCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
