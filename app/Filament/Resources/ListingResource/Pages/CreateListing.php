<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateListing extends CreateRecord
{
    protected static string $resource = ListingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['category_id']) && !empty($data['parent_category_id'])) {
            $data['category_id'] = $data['parent_category_id'];
        }
        unset($data['parent_category_id']);

        $newPhotos = array_values(array_filter((array) ($data['new_photos'] ?? [])));
        if (!empty($newPhotos)) {
            $data['image']  = $newPhotos[0];
            $data['images'] = count($newPhotos) > 1 ? array_slice($newPhotos, 1) : null;
        }
        unset($data['new_photos']);
        return $data;
    }
}
