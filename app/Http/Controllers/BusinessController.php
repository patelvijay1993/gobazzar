<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Business;
use App\Models\BusinessPost;
use App\Models\Category;
use App\Models\Location;
use App\Models\ActivityLog;
use App\Models\PageView;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        // Top-level directory categories (with sub-categories eager-loaded)
        $categories = Category::where('type', 'directory')->where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')->get();

        $businesses = Business::with(['category', 'subcategory'])
            ->where('status', 'active')
            ->when($request->category, function ($q) use ($request) {
                // Match the category itself OR any of its sub-categories
                $ids = Category::where('id', $request->category)
                    ->orWhere('parent_id', $request->category)
                    ->pluck('id');
                $q->whereIn('category_id', $ids);
            })
            ->when($request->search,   fn ($q) => $q->where(fn ($q2) => $q2
                ->where('name',        'like', '%' . addcslashes($request->search, '%_\\') . '%')
                ->orWhere('description', 'like', '%' . addcslashes($request->search, '%_\\') . '%')))
            ->when($request->city,     fn ($q) => $q->where('city', $request->city))
            ->when($request->province, fn ($q) => $q->where('province', $request->province))
            ->orderByDesc('is_featured')
            ->orderByDesc('is_verified')   // verified (paid plan) businesses rank higher
            ->orderByDesc('rating')
            ->paginate(12)
            ->withQueryString();

        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities($request->province);
        $ads       = Advertisement::forPosition('sidebar', $request->city, $request->province, 'directory')
            ->merge(Advertisement::forPosition('inline', $request->city, $request->province, 'directory'))
            ->unique('id');

        SearchLog::record($request, 'businesses', $businesses->total());
        PageView::recordPage('directory', $request);

        return view('directory.index', compact('categories', 'businesses', 'cities', 'provinces', 'ads'));
    }

    public function show(Request $request, Business $business)
    {
        abort_if($business->status !== 'active', 404);
        PageView::record($business, $request);
        ActivityLog::log($request, 'viewed_business', [
            'subject_type'  => get_class($business),
            'subject_id'    => $business->id,
            'subject_label' => $business->name,
        ]);

        $posts = $business->posts()
            ->live()
            ->latest()
            ->get();

        $related = Business::where('category_id', $business->category_id)
            ->where('id', '!=', $business->id)
            ->where('status', 'active')
            ->limit(4)->get();

        return view('directory.show', compact('business', 'related', 'posts'));
    }

    /** Drill-down: a category (parent) → its sub-categories + businesses */
    public function category(Category $category)
    {
        abort_if($category->type !== 'directory' || !$category->is_active, 404);

        $subCategories = $category->children()->where('is_active', true)->get();

        $catIds = Category::where('id', $category->id)
            ->orWhere('parent_id', $category->id)
            ->pluck('id');

        $businesses = Business::with(['category', 'subcategory'])
            ->where('status', 'active')
            ->whereIn('category_id', $catIds)
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->paginate(12);

        return view('directory.category', compact('category', 'subCategories', 'businesses'));
    }

    /** Single business post page: Home › Category › Sub › Business › Post */
    public function showPost(Business $business, BusinessPost $post)
    {
        abort_if($business->status !== 'active', 404);
        abort_if($post->business_id !== $business->id, 404);
        if ($post->status !== 'active') abort(404);
        if ($post->isExpired()) {
            return response(view('errors.expired', [
                'type'      => 'post',
                'title'     => $post->title,
                'expiredAt' => $post->expires_at,
                'browseUrl' => route('directory.show', $business->slug),
            ]), 410);
        }

        $post->increment('views');

        // Map custom field keys → labels for display (in defined order)
        $fieldLabels = collect();
        if ($post->category) {
            $fieldLabels = $post->category->applicableFields()
                ->mapWithKeys(fn ($f) => [$f->key => $f->label]);
        }

        $morePosts = $business->posts()
            ->live()
            ->where('id', '!=', $post->id)
            ->latest()->limit(4)->get();

        return view('directory.post', compact('business', 'post', 'morePosts', 'fieldLabels'));
    }
}
