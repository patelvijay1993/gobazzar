<?php

namespace App\Filament\Resources\FlaggedPostResource\Pages;

use App\Filament\Resources\FlaggedPostResource;
use App\Models\FlaggedPost;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFlaggedPosts extends ListRecords
{
    protected static string $resource = FlaggedPostResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-m-exclamation-triangle')
                ->badge(FlaggedPost::where('status', 'pending')->count()),

            'classified' => Tab::make('Classifieds')
                ->icon('heroicon-m-tag')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('post_type', 'classified'))
                ->badge(FlaggedPost::where('status', 'pending')->where('post_type', 'classified')->count()),

            'job' => Tab::make('Jobs')
                ->icon('heroicon-m-briefcase')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('post_type', 'job'))
                ->badge(FlaggedPost::where('status', 'pending')->where('post_type', 'job')->count()),

            'event' => Tab::make('Events')
                ->icon('heroicon-m-calendar-days')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('post_type', 'event'))
                ->badge(FlaggedPost::where('status', 'pending')->where('post_type', 'event')->count()),

            'business' => Tab::make('Businesses')
                ->icon('heroicon-m-building-storefront')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('post_type', 'like', 'business%'))
                ->badge(FlaggedPost::where('status', 'pending')->where('post_type', 'like', 'business%')->count()),
        ];
    }
}
