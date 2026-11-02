<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    // With 160k+ rows, pulling/scanning the whole published set on every single page
    // view (for the category/language filter lists and the featured pick) is what was
    // taking the site down under bot/crawler load — these barely change, so they're
    // cached instead of recomputed from scratch on every request.
    private const FILTERS_CACHE_TTL = 3600; // seconds

    public function index(Request $request)
    {
        // A bot/crawler hammering absurdly deep pages (page=14571 on a ~18k-page list)
        // was a big part of the load that took the site down — these can never return
        // real results, so reject them before they ever reach the database.
        $page = (int) $request->input('page', 1);
        if ($page > 2000) {
            abort(404);
        }

        $articles = NewsArticle::where('status', 'published')
            ->when($request->category, fn ($q) => $q->whereJsonContains('category', $request->category))
            ->when($request->language, fn ($q) => $q->where('language', $request->language))
            ->when($request->search, fn ($q) => $q->where(fn ($q2) => $q2
                ->where('title', 'like', '%'.addcslashes($request->search, '%_\\').'%')
                ->orWhere('description', 'like', '%'.addcslashes($request->search, '%_\\').'%')))
            ->orderByDesc('is_featured')
            ->orderByDesc('pub_date')
            ->paginate(9);

        $categories = Cache::remember('news.filters.categories', self::FILTERS_CACHE_TTL, function () {
            return NewsArticle::where('status', 'published')
                ->whereNotNull('category')
                ->pluck('category')
                ->flatten()
                ->unique()
                ->sort()
                ->values();
        });

        $languages = Cache::remember('news.filters.languages', self::FILTERS_CACHE_TTL, function () {
            return NewsArticle::where('status', 'published')
                ->whereNotNull('language')
                ->distinct()
                ->pluck('language')
                ->sort()
                ->values();
        });

        $featured = Cache::remember('news.filters.featured', self::FILTERS_CACHE_TTL, function () {
            return NewsArticle::where('status', 'published')
                ->where('is_featured', true)
                ->latest('pub_date')
                ->first();
        });

        return view('news.index', compact('articles', 'categories', 'languages', 'featured'));
    }

    public function show(NewsArticle $article)
    {
        abort_if($article->status !== 'published', 404);
        $article->increment('views');

        $related = NewsArticle::where('status', 'published')
            ->where('id', '!=', $article->id)
            ->when($article->category, fn ($q) => $q->whereJsonContains('category', $article->category[0] ?? null))
            ->latest('pub_date')
            ->limit(3)
            ->get();

        return view('news.show', compact('article', 'related'));
    }
}
