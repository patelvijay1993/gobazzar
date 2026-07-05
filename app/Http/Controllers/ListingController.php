<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingView;
use App\Models\Location;
use App\Models\ActivityLog;
use App\Models\PageView;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 'classifieds')->where('is_active', true)->orderBy('sort_order')->get();

        // Support single category or multiple (comma-separated via 'categories' param)
        $filterCategoryIds = null;
        if ($request->filled('categories')) {
            $filterCategoryIds = array_filter(array_map('intval', explode(',', $request->categories)));
        } elseif ($request->filled('category')) {
            $filterCategoryIds = [(int) $request->category];
        }

        $listings = Listing::with(['category', 'user'])
            ->live()
            ->when($filterCategoryIds, fn ($q) => $q->whereIn('category_id', $filterCategoryIds))
            ->when($request->search,   fn ($q) => $q->where('title', 'like', '%' . addcslashes($request->search, '%_\\') . '%'))
            ->when($request->city,     fn ($q) => $q->where('city', $request->city))
            ->when($request->province, fn ($q) => $q->where('province', $request->province))
            ->orderByDesc('is_featured')   // featured listings first
            ->orderByDesc('is_verified')   // verified (paid plan) listings second
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities($request->province);
        $ads       = Advertisement::forPosition('sidebar', $request->city, $request->province, 'classifieds')
            ->merge(Advertisement::forPosition('inline', $request->city, $request->province, 'classifieds'))
            ->unique('id');

        SearchLog::record($request, 'classifieds', $listings->total());
        PageView::recordPage('classifieds', $request);

        return view('classifieds.index', compact('categories', 'listings', 'cities', 'provinces', 'ads'));
    }

    public function show(Request $request, Listing $listing)
    {
        if ($listing->status !== 'active') abort(404);
        if ($listing->isExpired()) {
            return response(view('errors.expired', [
                'type'      => 'listing',
                'title'     => $listing->title,
                'expiredAt' => $listing->expires_at,
                'browseUrl' => route('classifieds.index'),
            ]), 410);
        }
        ListingView::record($listing, $request);
        PageView::record($listing, $request);
        ActivityLog::log($request, 'viewed_listing', [
            'subject_type'  => get_class($listing),
            'subject_id'    => $listing->id,
            'subject_label' => $listing->title,
        ]);
        $related = Listing::where('category_id', $listing->category_id)
            ->where('id', '!=', $listing->id)
            ->where('status', 'active')
            ->limit(4)->get();
        return view('classifieds.show', compact('listing', 'related'));
    }
}
