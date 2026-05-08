<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 'events')->where('is_active', true)->orderBy('sort_order')->get();

        $events = Event::with('category')
            ->where('status', 'active')
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->search,   fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->city,     fn ($q) => $q->where('city', $request->city))
            ->when($request->province, fn ($q) => $q->where('province', $request->province))
            ->when($request->filter === 'upcoming', fn ($q) => $q->where('start_date', '>=', now()))
            ->when($request->filter === 'free',     fn ($q) => $q->where('price', 'Free'))
            ->orderBy('start_date')
            ->paginate(12)
            ->withQueryString();

        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities($request->province);

        return view('events.index', compact('categories', 'events', 'cities', 'provinces'));
    }

    public function show(Event $event)
    {
        abort_if($event->status !== 'active', 404);
        $event->increment('views');
        $related = Event::where('category_id', $event->category_id)
            ->where('id', '!=', $event->id)
            ->where('status', 'active')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit(4)->get();
        return view('events.show', compact('event', 'related'));
    }
}
