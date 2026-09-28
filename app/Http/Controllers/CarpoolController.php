<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Advertisement;
use App\Models\Carpool;
use App\Models\Location;
use App\Models\PageView;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class CarpoolController extends Controller
{
    public function index(Request $request)
    {
        $carpools = Carpool::query()
            ->where('status', 'active')
            ->when($request->from, fn ($q) => $q->where('from_city', $request->from))
            ->when($request->to,   fn ($q) => $q->where('to_city', $request->to))
            ->when($request->ride_type, fn ($q) => $q->where('ride_type', $request->ride_type))
            ->when($request->date, fn ($q) => $q->whereDate('travel_date', $request->date))
            ->when($request->search, fn ($q) => $q->where(fn ($q2) => $q2
                ->where('title', 'like', '%'.addcslashes($request->search, '%_\\').'%')
                ->orWhere('from_city', 'like', '%'.addcslashes($request->search, '%_\\').'%')
                ->orWhere('to_city', 'like', '%'.addcslashes($request->search, '%_\\').'%')))
            ->when($request->filter === 'upcoming', fn ($q) => $q->where('travel_date', '>=', now()))
            ->orderByDesc('is_featured')
            ->orderBy('travel_date')
            ->paginate(12)
            ->withQueryString();

        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities();
        $ads       = Advertisement::forPosition('sidebar', $request->from, null, 'carpooling')
            ->merge(Advertisement::forPosition('inline', $request->from, null, 'carpooling'))
            ->unique('id');

        SearchLog::record($request, 'carpooling', $carpools->total());
        PageView::recordPage('carpooling', $request);

        return view('carpooling.index', compact('carpools', 'cities', 'provinces', 'ads'));
    }

    public function show(Request $request, Carpool $carpool)
    {
        abort_if($carpool->status !== 'active', 404);
        $carpool->increment('views');
        PageView::record($carpool, $request);
        ActivityLog::log($request, 'viewed_carpool', [
            'subject_type'  => get_class($carpool),
            'subject_id'    => $carpool->id,
            'subject_label' => $carpool->title,
        ]);

        $related = Carpool::where('id', '!=', $carpool->id)
            ->where('status', 'active')
            ->where('from_city', $carpool->from_city)
            ->where('to_city', $carpool->to_city)
            ->where('travel_date', '>=', now())
            ->orderBy('travel_date')
            ->limit(4)->get();

        return view('carpooling.show', compact('carpool', 'related'));
    }

    public function toggleChat(Request $request, Carpool $carpool)
    {
        abort_if($request->user()->id !== $carpool->user_id, 403);
        $carpool->update(['chat_enabled' => !$carpool->chat_enabled]);
        return back()->with('success', $carpool->chat_enabled ? 'Chat enabled for your ride.' : 'Chat disabled for your ride.');
    }
}
