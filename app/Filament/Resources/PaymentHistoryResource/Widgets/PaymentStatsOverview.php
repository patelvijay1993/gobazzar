<?php

namespace App\Filament\Resources\PaymentHistoryResource\Widgets;

use App\Models\PaymentHistory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue  = PaymentHistory::where('status', 'paid')->sum('amount');
        $thisMonth     = PaymentHistory::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
        $totalPayments = PaymentHistory::where('status', 'paid')->count();
        $failedCount   = PaymentHistory::where('status', 'failed')->count();

        return [
            Stat::make('Total Revenue', '$'.number_format($totalRevenue, 2))
                ->description('All time paid revenue')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('This Month', '$'.number_format($thisMonth, 2))
                ->description('Revenue in '.now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Successful Payments', $totalPayments)
                ->description('Total paid transactions')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Failed Payments', $failedCount)
                ->description('Total failed transactions')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($failedCount > 0 ? 'danger' : 'gray'),
        ];
    }
}
