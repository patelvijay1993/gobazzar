<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::with('author')
            ->where('status', 'published')
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->when($request->search, fn ($q) => $q->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('excerpt', 'like', '%'.$request->search.'%'))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate(9);

        $categories = BlogPost::where('status', 'published')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $featured = BlogPost::where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->first();

        return view('blog.index', compact('posts', 'categories', 'featured'));
    }

    public function show(BlogPost $post)
    {
        abort_if($post->status !== 'published', 404);
        $post->increment('views');

        $related = BlogPost::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where('category', $post->category)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
