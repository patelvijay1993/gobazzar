<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PricingController extends Controller
{
    private const VALID_PLANS = ['basic', 'premium', 'business'];

    public function index()
    {
        return view('pricing');
    }

    public function upgrade(string $plan)
    {
        if (!in_array($plan, self::VALID_PLANS)) {
            abort(404);
        }

        return view('pricing.upgrade', compact('plan'));
    }

    public function request(Request $request)
    {
        $data = $request->validate([
            'plan'    => ['required', 'in:basic,premium,business'],
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        // Log the upgrade request (email sending can be wired up later)
        \Log::info('Upgrade request', [
            'user_id' => auth()->id(),
            'plan'    => $data['plan'],
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? '',
        ]);

        return back()->with('success', 'Your upgrade request has been sent! We\'ll confirm your ' . ucfirst($data['plan']) . ' plan within 24 hours.');
    }
}
