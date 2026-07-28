<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use App\Models\Category;
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

    // Pre-populate parent_category_id virtual field when editing
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $cat = Category::find($data['category_id'] ?? null);
        if ($cat) {
            if ($cat->parent_id) {
                // category_id is a subcategory — set parent for the top dropdown
                $data['parent_category_id'] = $cat->parent_id;
                // category_id stays as subcategory id (correct)
            } else {
                // category_id is a parent — no subcategory selected
                $data['parent_category_id'] = $cat->id;
                $data['category_id'] = null;
            }
        }
        return $data;
    }

    // Handle new_photos upload on save
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If no subcategory selected, fall back to parent category
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

    // Handle new_photos on create too
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateFormDataBeforeSave($data);
    }
}
