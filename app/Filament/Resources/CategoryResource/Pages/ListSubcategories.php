<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSubcategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    public function getTitle(): string
    {
        return 'Subcategories';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Subcategory'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->whereNotNull('parent_id');
    }
}
