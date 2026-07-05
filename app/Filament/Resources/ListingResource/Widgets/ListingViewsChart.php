<?php

namespace App\Filament\Resources\ListingResource\Widgets;

use App\Models\ListingView;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class ListingViewsChart extends ChartWidget
{
    protected static ?string $heading = 'Views — Last 30 Days';
    protected static ?string $maxHeight = '260px';

    public ?Model $record = null;

    protected function getData(): array
    {
        $daily = ListingView::where('listing_id', $this->record->id)
            ->where('viewed_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as total, COUNT(DISTINCT ip) as uniq')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = $totals = $uniques = [];
        for ($i = 29; $i >= 0; $i--) {
            $date      = now()->subDays($i)->format('Y-m-d');
            $labels[]  = now()->subDays($i)->format('d M');
            $row       = $daily->get($date);
            $totals[]  = $row?->total ?? 0;
            $uniques[]  = $row?->uniq ?? 0;
        }

        return [
            'datasets' => [
                ['label' => 'Total Views',      'data' => $totals,  'borderColor' => '#1a3a8f', 'backgroundColor' => 'rgba(26,58,143,.1)', 'fill' => true, 'tension' => 0.3],
                ['label' => 'Unique Visitors',  'data' => $uniques, 'borderColor' => '#16a34a', 'backgroundColor' => 'rgba(22,163,74,.07)', 'fill' => true, 'tension' => 0.3],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
