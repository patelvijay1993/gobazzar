<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $articles = NewsArticle::where('status', 'published')
            ->when($request->category, fn ($q) => $q->whereJsonContains('category', $request->category))
            ->when($request->search, fn ($q) => $q->where(fn ($q2) => $q2
                ->where('title', 'like', '%'.addcslashes($request->search, '%_\\').'%')
                ->orWhere('description', 'like', '%'.addcslashes($request->search, '%_\\').'%')))
            ->orderByDesc('is_featured')
            ->orderByDesc('pub_date')
            ->paginate(9);

        $categories = NewsArticle::where('status', 'published')
            ->whereNotNull('category')
            ->pluck('category')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        $featured = NewsArticle::where('status', 'published')
            ->where('is_featured', true)
            ->latest('pub_date')
            ->first();

        return view('news.index', compact('articles', 'categories', 'featured'));
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
