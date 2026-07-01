<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PricingController extends Controller
{
    private static function validPlanSlugs(): array
    {
        return Plan::where('is_active', true)->where('slug', '!=', 'free')->pluck('slug')->toArray();
    }

    public function index()
    {
        $plans = Plan::active();
        return view('pricing', compact('plans'));
    }

    public function upgrade(string $plan)
    {
        $planModel = Plan::where('slug', $plan)->where('is_active', true)->firstOrFail();
        return view('pricing.upgrade', ['plan' => $plan, 'planModel' => $planModel]);
    }

    public function request(Request $request)
    {
        $data = $request->validate([
            'plan'    => ['required', 'in:'.implode(',', self::validPlanSlugs())],
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

        $planName = Plan::where('slug', $data['plan'])->value('name') ?? ucfirst($data['plan']);
        return back()->with('success', "Your upgrade request has been sent! We'll confirm your {$planName} plan within 24 hours.");
    }
}
