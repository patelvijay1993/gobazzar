<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListing extends EditRecord
{
    protected static string $resource = ListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Handle new_photos upload on save
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newPhotos = array_values(array_filter((array) ($data['new_photos'] ?? [])));

        if (!empty($newPhotos)) {
            $data['image']  = $newPhotos[0];
            $data['images'] = count($newPhotos) > 1 ? array_slice($newPhotos, 1) : null;
        }

        unset($data['new_photos']);
        return $data;
    }

    // Handle new_photos on create too
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateFormDataBeforeSave($data);
    }
}
