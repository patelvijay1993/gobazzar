<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Event;
use App\Models\Job;
use App\Models\Listing;
use App\Models\ListingView;
use App\Models\Matrimonial;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function account()
    {
        $user          = Auth::user();
        $listings      = Listing::where('user_id', $user->id)->latest()->get();
        $jobs          = Job::where('user_id', $user->id)->latest()->get();
        $events        = Event::where('user_id', $user->id)->latest()->get();
        $businesses    = Business::where('user_id', $user->id)->latest()->get();
        $matrimonials  = Matrimonial::where('user_id', $user->id)->latest()->get();
        $businessPosts  = \App\Models\BusinessPost::with('business')
            ->where('user_id', $user->id)->latest()->get();
        $paymentHistory = PaymentHistory::where('user_id', $user->id)
            ->latest('paid_at')->limit(20)->get();

        return view('user.account', compact('user', 'listings', 'jobs', 'events', 'businesses', 'matrimonials', 'businessPosts', 'paymentHistory'));
    }

    public function analytics(Listing $listing)
    {
        $user = Auth::user();

        abort_if($listing->user_id !== $user->id, 403);
        abort_unless($user->hasAnalytics(), 403);

        // Daily views for last 30 days
        $daily = ListingView::where('listing_id', $listing->id)
            ->where('viewed_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as total, COUNT(DISTINCT ip) as unique_views')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing days with zeros
        $labels = [];
        $totals = [];
        $uniques = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[]  = now()->subDays($i)->format('d M');
            $row = $daily->get($date);
            $totals[]  = $row?->total ?? 0;
            $uniques[] = $row?->unique_views ?? 0;
        }

        // Device breakdown
        $devices = ListingView::where('listing_id', $listing->id)
            ->selectRaw('device, COUNT(*) as cnt')
            ->groupBy('device')
            ->pluck('cnt', 'device');

        // Referrers
        $referrers = ListingView::where('listing_id', $listing->id)
            ->whereNotNull('referrer')
            ->selectRaw('referrer, COUNT(*) as cnt')
            ->groupBy('referrer')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        $totalViews  = ListingView::where('listing_id', $listing->id)->count();
        $uniqueViews = ListingView::where('listing_id', $listing->id)->distinct('ip')->count('ip');
        $todayViews  = ListingView::where('listing_id', $listing->id)->where('viewed_at', '>=', now()->startOfDay())->count();
        $last7Views  = ListingView::where('listing_id', $listing->id)->where('viewed_at', '>=', now()->subDays(7))->count();

        return view('user.analytics', compact(
            'listing', 'labels', 'totals', 'uniques',
            'devices', 'referrers', 'totalViews', 'uniqueViews', 'todayViews', 'last7Views'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:20',
            'city'   => 'nullable|string|max:100',
            'bio'    => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'phone', 'city', 'province', 'bio');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', config('filesystems.default'));
        }

        $user->update($data);
        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password changed successfully!');
    }
}

