<?php

namespace App\Filament\Resources\BusinessCategoryResource\Pages;

use App\Filament\Resources\BusinessCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBusinessSubcategories extends ListRecords
{
    protected static string $resource = BusinessCategoryResource::class;

    public function getTitle(): string
    {
        return 'Business Subcategories';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add Subcategory'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->whereNotNull('parent_id');
    }
}
