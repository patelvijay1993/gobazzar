<?php

namespace App\Filament\Widgets;

use App\Models\Business;
use App\Models\Event;
use App\Models\Job;
use App\Models\Listing;
use App\Models\PaymentHistory;
use App\Models\Report;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue    = PaymentHistory::where('status', 'paid')->sum('amount');
        $paidUsers       = User::whereNotIn('plan', ['free', ''])->whereNotNull('plan')->count();
        $newUsersWeek    = User::where('created_at', '>=', now()->subDays(7))->count();
        $totalUsers      = User::count();
        $pendingReports  = Report::where('status', 'pending')->count();
        $todayReports    = Report::where('status', 'pending')->whereDate('created_at', today())->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description("+{$newUsersWeek} this week")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([
                    User::whereDate('created_at', now()->subDays(6))->count(),
                    User::whereDate('created_at', now()->subDays(5))->count(),
                    User::whereDate('created_at', now()->subDays(4))->count(),
                    User::whereDate('created_at', now()->subDays(3))->count(),
                    User::whereDate('created_at', now()->subDays(2))->count(),
                    User::whereDate('created_at', now()->subDays(1))->count(),
                    User::whereDate('created_at', now())->count(),
                ]),

            Stat::make('Paid Subscribers', $paidUsers)
                ->description('Active paid plans')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Total Revenue', '$'.number_format($totalRevenue, 2))
                ->description('All time from Stripe')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Active Listings', Listing::count())
                ->description(
                    'Jobs: '.Job::count().' · Events: '.Event::count().' · Biz: '.Business::count()
                )
                ->descriptionIcon('heroicon-m-tag')
                ->color('gray'),

            Stat::make('Pending Reports', $pendingReports)
                ->description($todayReports > 0 ? "+{$todayReports} today" : 'No new today')
                ->descriptionIcon('heroicon-m-flag')
                ->color($pendingReports > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.reports.index')),
        ];
    }
}
