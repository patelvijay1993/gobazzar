<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use App\Models\ActivityLog;
use App\Models\PageView;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 'events')->where('is_active', true)->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')->get();

        $events = Event::with('category')
            ->where('status', 'active')
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->search,   fn ($q) => $q->where('title', 'like', '%' . addcslashes($request->search, '%_\\') . '%'))
            ->when($request->city,     fn ($q) => $q->where('city', $request->city))
            ->when($request->province, fn ($q) => $q->where('province', $request->province))
            ->when($request->filter === 'upcoming', fn ($q) => $q->where('start_date', '>=', now()))
            ->when($request->filter === 'free',     fn ($q) => $q->where('price', 'Free'))
            ->orderBy('start_date')
            ->paginate(12)
            ->withQueryString();

        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities($request->province);
        $ads       = Advertisement::forPosition('sidebar', $request->city, $request->province, 'events')
            ->merge(Advertisement::forPosition('inline', $request->city, $request->province, 'events'))
            ->unique('id');

        SearchLog::record($request, 'events', $events->total());
        PageView::recordPage('events', $request);

        return view('events.index', compact('categories', 'events', 'cities', 'provinces', 'ads'));
    }

    public function show(Request $request, Event $event)
    {
        abort_if($event->status !== 'active', 404);
        $event->increment('views');
        PageView::record($event, $request);
        ActivityLog::log($request, 'viewed_event', [
            'subject_type'  => get_class($event),
            'subject_id'    => $event->id,
            'subject_label' => $event->title,
        ]);
        $related = Event::where('category_id', $event->category_id)
            ->where('id', '!=', $event->id)
            ->where('status', 'active')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit(4)->get();
        return view('events.show', compact('event', 'related'));
    }
}
