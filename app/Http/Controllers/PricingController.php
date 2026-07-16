<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PromoCode;
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

        // Log the upgrade request without PII (contact details stored in advertise_requests)
        \Log::info('Upgrade request submitted', [
            'user_id' => auth()->id(),
            'plan'    => $data['plan'],
        ]);

        $planName = Plan::where('slug', $data['plan'])->value('name') ?? ucfirst($data['plan']);
        return back()->with('success', "Your upgrade request has been sent! We'll confirm your {$planName} plan within 24 hours.");
    }

    public function applyPromo(Request $request)
    {
        $request->validate(['code' => 'required|string|max:32']);

        $promo = PromoCode::findValid($request->code);

        if (!$promo) {
            return redirect()->route('pricing')
                ->with('promo_error', 'Invalid or expired promo code. Please check and try again.');
        }

        $user = $request->user();

        // Don't downgrade an active paid subscription
        if ($user->isSubscribed() && $user->plan_expires_at && $user->plan_expires_at->isFuture()) {
            $existingExpiry = $user->plan_expires_at->format('M d, Y');
            return redirect()->route('pricing')
                ->with('promo_error', "You already have an active plan until {$existingExpiry}. Promo codes can only be applied when your plan has expired.");
        }

        $promo->apply($user);

        $planName = Plan::where('slug', $promo->plan_slug)->value('name') ?? ucfirst($promo->plan_slug);
        $until    = now()->addMonths($promo->duration_months)->format('M d, Y');

        return redirect()->route('pricing')
            ->with('promo_success', "Promo code applied! You now have the {$planName} plan until {$until}. Enjoy!");
    }
}
