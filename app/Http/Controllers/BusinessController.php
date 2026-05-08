<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 'directory')->where('is_active', true)->orderBy('sort_order')->get();

        $businesses = Business::with('category')
            ->where('status', 'active')
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->search,   fn ($q) => $q->where(fn ($q2) =>
                $q2->where('name', 'like', '%' . $request->search . '%')
                   ->orWhere('description', 'like', '%' . $request->search . '%')))
            ->when($request->city,     fn ($q) => $q->where('city', $request->city))
            ->when($request->province, fn ($q) => $q->where('province', $request->province))
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->paginate(12)
            ->withQueryString();

        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities($request->province);

        return view('directory.index', compact('categories', 'businesses', 'cities', 'provinces'));
    }

    public function show(Business $business)
    {
        abort_if($business->status !== 'active', 404);
        $related = Business::where('category_id', $business->category_id)
            ->where('id', '!=', $business->id)
            ->where('status', 'active')
            ->limit(4)->get();
        return view('directory.show', compact('business', 'related'));
    }
}
