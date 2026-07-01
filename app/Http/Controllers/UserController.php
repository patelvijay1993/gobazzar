<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Event;
use App\Models\Job;
use App\Models\Listing;
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
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
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
