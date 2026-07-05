<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use App\Filament\Resources\ListingResource\Widgets\ListingViewsChart;
use App\Models\ListingView;
use Filament\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewListingAnalytics extends ViewRecord
{
    protected static string $resource = ListingResource::class;

    protected static ?string $title = 'Listing Analytics';

    protected static string $view = 'filament.pages.listing-analytics';

    public function getHeaderWidgets(): array
    {
        return [ListingViewsChart::class];
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Listings')
                ->url(ListingResource::getUrl())
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $listing = $this->record;

        $totalViews  = ListingView::where('listing_id', $listing->id)->count();
        $uniqueViews = ListingView::where('listing_id', $listing->id)->distinct('ip')->count('ip');
        $todayViews  = ListingView::where('listing_id', $listing->id)->where('viewed_at', '>=', now()->startOfDay())->count();
        $last7Views  = ListingView::where('listing_id', $listing->id)->where('viewed_at', '>=', now()->subDays(7))->count();

        $devices = ListingView::where('listing_id', $listing->id)
            ->selectRaw('device, COUNT(*) as cnt')
            ->groupBy('device')
            ->pluck('cnt', 'device');

        return $infolist->state([
            'total_views'   => number_format($totalViews),
            'unique_views'  => number_format($uniqueViews),
            'today_views'   => number_format($todayViews),
            'last7_views'   => number_format($last7Views),
            'desktop'       => $devices->get('desktop', 0),
            'mobile'        => $devices->get('mobile', 0),
            'tablet'        => $devices->get('tablet', 0),
            'listing_title' => $listing->title,
            'listing_status'=> ucfirst($listing->status),
            'posted_by'     => $listing->user?->name . ' (' . $listing->user?->email . ')',
        ])->schema([
            Section::make('KPI Summary')->schema([
                Grid::make(4)->schema([
                    TextEntry::make('total_views')->label('Total Views')->size('lg')->weight('bold')->color('primary'),
                    TextEntry::make('unique_views')->label('Unique Visitors')->size('lg')->weight('bold')->color('success'),
                    TextEntry::make('today_views')->label('Today')->size('lg')->weight('bold'),
                    TextEntry::make('last7_views')->label('Last 7 Days')->size('lg')->weight('bold')->color('warning'),
                ]),
            ]),
            Section::make('Listing Info')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('listing_title')->label('Title'),
                    TextEntry::make('listing_status')->label('Status'),
                    TextEntry::make('posted_by')->label('Posted By'),
                ]),
            ]),
            Section::make('Device Breakdown')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('desktop')->label('🖥️ Desktop'),
                    TextEntry::make('mobile')->label('📱 Mobile'),
                    TextEntry::make('tablet')->label('📟 Tablet'),
                ]),
            ]),
        ]);
    }
}
