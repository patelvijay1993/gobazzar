<?php

namespace App\Filament\Pages;

use App\Models\AdStat;
use App\Models\Advertisement;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class AdAnalytics extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Ad Analytics';
    protected static ?string $navigationGroup = 'Advertising';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.pages.ad-analytics';

    public string $period = '30';
    public ?int   $adId   = null;

    public function mount(): void
    {
        $this->adId = request()->integer('ad') ?: null;
    }

    public function getPeriodOptions(): array
    {
        return ['7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days'];
    }

    public function getViewData(): array
    {
        $days  = (int) $this->period;
        $since = now()->subDays($days)->toDateString();

        // ── Single-ad mode ───────────────────────────────────────────────
        if ($this->adId) {
            $ad = Advertisement::findOrFail($this->adId);

            $stats = AdStat::where('advertisement_id', $this->adId)
                ->where('date', '>=', $since)
                ->select(
                    DB::raw('DATE_FORMAT(date, "%Y-%m-%d") as day'),
                    DB::raw('SUM(impressions) as impressions'),
                    DB::raw('SUM(clicks) as clicks')
                )
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            $impr = (int) $stats->sum('impressions');
            $clks = (int) $stats->sum('clicks');

            $singleAd = [
                'id'          => $ad->id,
                'title'       => $ad->title,
                'position'    => $ad->position,
                'is_active'   => $ad->is_active,
                'click_url'   => $ad->click_url,
                'starts_at'   => $ad->starts_at?->format('M d, Y'),
                'ends_at'     => $ad->ends_at?->format('M d, Y'),
                'impressions' => $impr,
                'clicks'      => $clks,
                'ctr'         => $impr > 0 ? round(($clks / $impr) * 100, 2) : 0,
                'daily'       => $stats->map(fn($s) => [
                    'day'         => $s->day,
                    'impressions' => (int) $s->impressions,
                    'clicks'      => (int) $s->clicks,
                ])->values()->toArray(),
            ];

            // Best day
            $bestDay = $stats->sortByDesc('impressions')->first();

            return compact('days', 'singleAd', 'bestDay') + [
                'mode'             => 'single',
                'totalImpressions' => $impr,
                'totalClicks'      => $clks,
                'overallCtr'       => $impr > 0 ? round(($clks / $impr) * 100, 2) : 0,
                'activeAds'        => 0,
                'dailyTotals'      => $stats,
                'ads'              => collect(),
                'byPosition'       => collect(),
            ];
        }

        // ── All-ads mode ─────────────────────────────────────────────────
        $totalImpressions = AdStat::where('date', '>=', $since)->sum('impressions');
        $totalClicks      = AdStat::where('date', '>=', $since)->sum('clicks');
        $overallCtr       = $totalImpressions > 0
            ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;
        $activeAds        = Advertisement::where('is_active', true)->count();

        $dailyTotals = AdStat::where('date', '>=', $since)
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m-%d") as day'),
                DB::raw('SUM(impressions) as impressions'),
                DB::raw('SUM(clicks) as clicks')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $ads = Advertisement::with(['stats' => function ($q) use ($since) {
            $q->where('date', '>=', $since)
              ->select(
                  'advertisement_id',
                  DB::raw('DATE_FORMAT(date, "%Y-%m-%d") as day'),
                  DB::raw('SUM(impressions) as impressions'),
                  DB::raw('SUM(clicks) as clicks')
              )
              ->groupBy('advertisement_id', 'day')
              ->orderBy('day');
        }])
        ->get()
        ->map(function ($ad) {
            $impr = $ad->stats->sum('impressions');
            $clks = $ad->stats->sum('clicks');
            return [
                'id'          => $ad->id,
                'title'       => $ad->title,
                'position'    => $ad->position,
                'is_active'   => $ad->is_active,
                'click_url'   => $ad->click_url,
                'impressions' => (int) $impr,
                'clicks'      => (int) $clks,
                'ctr'         => $impr > 0 ? round(($clks / $impr) * 100, 2) : 0,
                'daily'       => $ad->stats->map(fn($s) => [
                    'day'         => $s->day,
                    'impressions' => (int) $s->impressions,
                    'clicks'      => (int) $s->clicks,
                ])->values()->toArray(),
            ];
        })
        ->sortByDesc('impressions')
        ->values();

        $byPosition = AdStat::where('date', '>=', $since)
            ->join('advertisements', 'ad_stats.advertisement_id', '=', 'advertisements.id')
            ->select(
                'advertisements.position',
                DB::raw('SUM(ad_stats.impressions) as impressions'),
                DB::raw('SUM(ad_stats.clicks) as clicks')
            )
            ->groupBy('advertisements.position')
            ->get();

        return compact(
            'days', 'totalImpressions', 'totalClicks', 'overallCtr', 'activeAds',
            'dailyTotals', 'ads', 'byPosition'
        ) + ['mode' => 'all', 'singleAd' => null, 'bestDay' => null];
    }
}
