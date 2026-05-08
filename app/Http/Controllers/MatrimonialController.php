<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Matrimonial;
use Illuminate\Http\Request;

class MatrimonialController extends Controller
{
    public function index(Request $request)
    {
        $profiles = Matrimonial::where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->when($request->gender,   fn ($q) => $q->where('gender', $request->gender))
            ->when($request->religion, fn ($q) => $q->where('religion', 'like', '%' . $request->religion . '%'))
            ->when($request->city,     fn ($q) => $q->where('city', $request->city))
            ->when($request->province, fn ($q) => $q->where('province', $request->province))
            ->when($request->min_age,  fn ($q) => $q->where('age', '>=', $request->min_age))
            ->when($request->max_age,  fn ($q) => $q->where('age', '<=', $request->max_age))
            ->when($request->search,   fn ($q) => $q->where(fn ($q2) =>
                $q2->where('name', 'like', '%' . $request->search . '%')
                   ->orWhere('occupation', 'like', '%' . $request->search . '%')
                   ->orWhere('religion', 'like', '%' . $request->search . '%')))
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities($request->province);
        $religions = Matrimonial::where('status', 'active')->whereNotNull('religion')->distinct()->orderBy('religion')->pluck('religion');

        return view('matrimonial.index', compact('profiles', 'cities', 'provinces', 'religions'));
    }

    public function show(Matrimonial $profile)
    {
        abort_if($profile->status !== 'active', 404);
        $profile->increment('views');

        $related = Matrimonial::where('status', 'active')
            ->where('gender', $profile->gender)
            ->where('id', '!=', $profile->id)
            ->latest()->limit(4)->get();

        return view('matrimonial.show', compact('profile', 'related'));
    }
}
