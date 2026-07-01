<?php

namespace App\Http\Controllers;

use App\Models\AdvertiseRequest;
use Illuminate\Http\Request;

class AdvertiseController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:150'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'business_name' => ['nullable', 'string', 'max:150'],
            'website'       => ['nullable', 'url', 'max:200'],
            'ad_position'   => ['nullable', 'string', 'max:50'],
            'budget'        => ['nullable', 'string', 'max:50'],
            'message'       => ['nullable', 'string', 'max:1000'],
        ]);

        $data['user_id'] = auth()->id();

        AdvertiseRequest::create($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('advertise_success', 'Your advertising enquiry has been submitted! We will contact you within 24 hours.');
    }
}
