<?php

namespace App\Filament\Pages;

use App\Models\PageView;
use App\Models\SearchLog;
use App\Models\User;
use App\Models\Listing;
use App\Models\Job;
use App\Models\Event;
use App\Models\Business;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Analytics extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Analytics';
    protected static ?string $navigationGroup = 'System';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.analytics';

    public string $period = '30';

    public function getPeriodOptions(): array
    {
        return [
            '7'  => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
        ];
    }

    public function getViewData(): array
    {
        $days  = (int) $this->period;
        $since = now()->subDays($days);

        // ── Page Views by type (individual posts) ──────────────────────
        $viewsByType = PageView::where('viewed_at', '>=', $since)
            ->whereNotNull('viewable_type')
            ->select('viewable_type', DB::raw('COUNT(*) as total'))
            ->groupBy('viewable_type')
            ->pluck('total', 'viewable_type')
            ->mapWithKeys(fn ($v, $k) => [class_basename($k) => $v]);

        // ── Page views by section (index pages) ─────────────────────────
        $viewsBySection = PageView::where('viewed_at', '>=', $since)
            ->whereNotNull('page')
            ->select('page', DB::raw('COUNT(*) as total'))
            ->groupBy('page')
            ->orderByDesc('total')
            ->pluck('total', 'page');

        // ── Total page views per day (last N days) ──────────────────────
        $dailyViews = PageView::where('viewed_at', '>=', $since)
            ->select(DB::raw('DATE(viewed_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // ── Top viewed posts ────────────────────────────────────────────
        $topPosts = PageView::where('viewed_at', '>=', $since)
            ->select('viewable_type', 'viewable_id', DB::raw('COUNT(*) as total'))
            ->groupBy('viewable_type', 'viewable_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $type  = class_basename($row->viewable_type);
                $model = match ($type) {
                    'Listing'  => Listing::find($row->viewable_id),
                    'Job'      => Job::find($row->viewable_id),
                    'Event'    => Event::find($row->viewable_id),
                    'Business' => Business::find($row->viewable_id),
                    default    => null,
                };
                return [
                    'type'  => $type,
                    'title' => $model?->title ?? $model?->name ?? '(deleted)',
                    'views' => $row->total,
                    'url'   => match ($type) {
                        'Listing'  => $model ? route('classifieds.show', $model) : null,
                        'Job'      => $model ? route('jobs.show', $model) : null,
                        'Event'    => $model ? route('events.show', $model) : null,
                        'Business' => $model ? route('directory.show', $model) : null,
                        default    => null,
                    },
                ];
            });

        // ── Device breakdown ────────────────────────────────────────────
        $devices = PageView::where('viewed_at', '>=', $since)
            ->select('device', DB::raw('COUNT(*) as total'))
            ->groupBy('device')
            ->pluck('total', 'device');

        // ── Top search keywords ─────────────────────────────────────────
        $topKeywords = SearchLog::where('searched_at', '>=', $since)
            ->whereNotNull('keyword')
            ->select('keyword', DB::raw('COUNT(*) as total'), DB::raw('AVG(results_count) as avg_results'))
            ->groupBy('keyword')
            ->orderByDesc('total')
            ->limit(15)
            ->get();

        // ── Search by section ───────────────────────────────────────────
        $searchBySection = SearchLog::where('searched_at', '>=', $since)
            ->select('section', DB::raw('COUNT(*) as total'))
            ->groupBy('section')
            ->pluck('total', 'section');

        // ── Zero-results searches ───────────────────────────────────────
        $zeroResults = SearchLog::where('searched_at', '>=', $since)
            ->where('results_count', 0)
            ->whereNotNull('keyword')
            ->select('keyword', DB::raw('COUNT(*) as total'))
            ->groupBy('keyword')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ── Top provinces searched ──────────────────────────────────────
        $topProvinces = SearchLog::where('searched_at', '>=', $since)
            ->whereNotNull('province')
            ->select('province', DB::raw('COUNT(*) as total'))
            ->groupBy('province')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ── New users per day ───────────────────────────────────────────
        $newUsersDaily = User::where('created_at', '>=', $since)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // ── Summary KPIs ────────────────────────────────────────────────
        $totalPageViews   = PageView::where('viewed_at', '>=', $since)->count();
        $uniqueVisitors   = PageView::where('viewed_at', '>=', $since)->distinct('ip')->count('ip');
        $totalSearches    = SearchLog::where('searched_at', '>=', $since)->count();
        $newUsers         = User::where('created_at', '>=', $since)->count();

        return compact(
            'viewsByType', 'viewsBySection', 'dailyViews', 'topPosts', 'devices',
            'topKeywords', 'searchBySection', 'zeroResults', 'topProvinces',
            'newUsersDaily', 'totalPageViews', 'uniqueVisitors', 'totalSearches', 'newUsers',
            'days'
        );
    }
}
