<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 'classifieds')->where('is_active', true)->orderBy('sort_order')->get();

        $listings = Listing::with('category')
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->search,   fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->city,     fn ($q) => $q->where('city', $request->city))
            ->when($request->province, fn ($q) => $q->where('province', $request->province))
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities($request->province);

        return view('classifieds.index', compact('categories', 'listings', 'cities', 'provinces'));
    }

    public function show(Listing $listing)
    {
        abort_if($listing->status !== 'active', 404);
        $listing->increment('views');
        $related = Listing::where('category_id', $listing->category_id)
            ->where('id', '!=', $listing->id)
            ->where('status', 'active')
            ->limit(4)->get();
        return view('classifieds.show', compact('listing', 'related'));
    }
}
