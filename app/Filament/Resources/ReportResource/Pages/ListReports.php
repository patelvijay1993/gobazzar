<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Models\Report;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    public function getTabs(): array
    {
        $types = [
            'App\\Models\\Listing'  => ['label' => 'Classifieds', 'icon' => 'heroicon-m-tag'],
            'App\\Models\\Job'      => ['label' => 'Jobs',        'icon' => 'heroicon-m-briefcase'],
            'App\\Models\\Event'    => ['label' => 'Events',      'icon' => 'heroicon-m-calendar-days'],
            'App\\Models\\Business' => ['label' => 'Businesses',  'icon' => 'heroicon-m-building-storefront'],
            'App\\Models\\BlogPost' => ['label' => 'Blog',        'icon' => 'heroicon-m-document-text'],
        ];

        $tabs = [
            'all' => Tab::make('All Reports')
                ->icon('heroicon-m-flag'),
        ];

        foreach ($types as $class => $cfg) {
            $tabs[strtolower(class_basename($class))] = Tab::make($cfg['label'])
                ->icon($cfg['icon'])
                ->modifyQueryUsing(fn (Builder $q) => $q->where('reportable_type', $class));
        }

        return $tabs;
    }
}
