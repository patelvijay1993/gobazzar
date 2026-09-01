<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announcement::live()
            ->when($request->search, fn ($q) => $q->where(fn ($q2) => $q2
                ->where('title', 'like', '%'.addcslashes($request->search, '%_\\').'%')
                ->orWhere('excerpt', 'like', '%'.addcslashes($request->search, '%_\\').'%')))
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(10);

        $pinned = Announcement::live()
            ->where('is_pinned', true)
            ->latest('created_at')
            ->first();

        return view('announcements.index', compact('announcements', 'pinned'));
    }

    public function show(Announcement $announcement)
    {
        abort_if($announcement->status !== 'published', 404);
        $announcement->increment('views');

        $latest = Announcement::live()
            ->where('id', '!=', $announcement->id)
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('announcements.show', compact('announcement', 'latest'));
    }
}
