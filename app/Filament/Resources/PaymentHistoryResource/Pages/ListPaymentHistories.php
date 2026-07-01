<?php

namespace App\Filament\Resources\PaymentHistoryResource\Pages;

use App\Filament\Resources\PaymentHistoryResource;
use App\Models\PaymentHistory;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPaymentHistories extends ListRecords
{
    protected static string $resource = PaymentHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentHistoryResource\Widgets\PaymentStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(PaymentHistory::count()),

            'paid' => Tab::make('Paid')
                ->badge(PaymentHistory::where('status', 'paid')->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'paid')),

            'failed' => Tab::make('Failed')
                ->badge(PaymentHistory::where('status', 'failed')->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'failed')),

            'refunded' => Tab::make('Refunded')
                ->badge(PaymentHistory::where('status', 'refunded')->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'refunded')),
        ];
    }
}
