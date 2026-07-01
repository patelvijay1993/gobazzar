<?php

namespace App\Http\Controllers;

use App\Models\PaymentHistory;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ── Create Stripe Checkout Session ───────────────────────────
    public function checkout(Request $request, string $planSlug)
    {
        $plan = Plan::where('slug', $planSlug)->where('is_active', true)->firstOrFail();

        if ($plan->slug === 'free') {
            return back()->with('error', 'Free plan needs no payment.');
        }

        if (!$plan->stripe_price_id) {
            return back()->with('error', 'This plan is not yet configured for online payment. Please contact us.');
        }

        $user = Auth::user();

        // Create or retrieve Stripe customer
        if (!$user->stripe_customer_id) {
            $customer = \Stripe\Customer::create([
                'email' => $user->email,
                'name'  => $user->name,
                'metadata' => ['user_id' => $user->id],
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        // Create Checkout Session
        $session = \Stripe\Checkout\Session::create([
            'customer'            => $user->stripe_customer_id,
            'payment_method_types' => ['card'],
            'mode'                => 'subscription',
            'line_items'          => [[
                'price'    => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('pricing'),
            'metadata'    => [
                'user_id'   => $user->id,
                'plan_slug' => $plan->slug,
            ],
            'subscription_data' => [
                'metadata' => [
                    'user_id'   => $user->id,
                    'plan_slug' => $plan->slug,
                ],
            ],
        ]);

        return redirect($session->url);
    }

    // ── Payment Success (redirect back from Stripe) ───────────────
    public function success(Request $request)
    {
        if (!$request->session_id) {
            return redirect()->route('account');
        }

        $session = \Stripe\Checkout\Session::retrieve([
            'id'     => $request->session_id,
            'expand' => ['subscription'],
        ]);

        if ($session->payment_status === 'paid' || $session->status === 'complete') {
            $user     = Auth::user();
            $planSlug = $session->metadata->plan_slug ?? null;

            if ($planSlug && $user) {
                $plan = Plan::findBySlug($planSlug);
                $sub  = $session->subscription;

                $user->update([
                    'plan'                    => $planSlug,
                    'plan_expires_at'         => $sub ? now()->addDays(30) : null,
                    'stripe_subscription_id'  => $sub->id ?? null,
                    'subscription_status'     => 'active',
                ]);

                // Record payment history
                PaymentHistory::create([
                    'user_id'                  => $user->id,
                    'stripe_subscription_id'   => $sub->id ?? null,
                    'stripe_invoice_id'        => $sub->latest_invoice ?? null,
                    'plan_slug'                => $planSlug,
                    'plan_name'                => $plan->name ?? ucfirst($planSlug),
                    'amount'                   => $plan->price ?? 0,
                    'currency'                 => 'usd',
                    'status'                   => 'paid',
                    'description'              => ($plan->name ?? ucfirst($planSlug)).' Plan — Monthly Subscription',
                    'paid_at'                  => now(),
                ]);
            }

            return redirect()->route('account')
                ->with('success', 'Payment successful! Your plan has been upgraded. Welcome to '.($plan->name ?? ucfirst($planSlug)).'!');
        }

        return redirect()->route('pricing')->with('error', 'Payment was not completed. Please try again.');
    }

    // ── Cancel Confirmation Page ──────────────────────────────────
    public function cancelConfirm(Request $request)
    {
        $user = Auth::user();
        $plan = Plan::findBySlug($user->plan ?? 'free');

        if (!$user->isSubscribed()) {
            return redirect()->route('account')->with('error', 'No active paid subscription found.');
        }

        // If no Stripe subscription ID (manually assigned plan), use plan_expires_at or end of month
        if (!$user->stripe_subscription_id) {
            $endDate = $user->plan_expires_at ?? now()->endOfMonth();
            return view('pricing.cancel-confirm', compact('user', 'plan', 'endDate'));
        }

        try {
            $sub     = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
            $endDate = \Carbon\Carbon::createFromTimestamp($sub->current_period_end);
            return view('pricing.cancel-confirm', compact('user', 'plan', 'endDate'));
        } catch (\Throwable $e) {
            Log::error('Stripe cancel confirm error: '.$e->getMessage());
            // Fallback — show page with available date info
            $endDate = $user->plan_expires_at ?? now()->endOfMonth();
            return view('pricing.cancel-confirm', compact('user', 'plan', 'endDate'));
        }
    }

    // ── Cancel Subscription (at period end) ──────────────────────
    public function cancel(Request $request)
    {
        $user    = Auth::user();
        $endDate = $user->plan_expires_at ?? now()->endOfMonth();

        if (!$user->isSubscribed()) {
            return redirect()->route('account')->with('error', 'No active subscription found.');
        }

        // If Stripe subscription exists — cancel via API
        if ($user->stripe_subscription_id) {
            try {
                \Stripe\Subscription::update($user->stripe_subscription_id, [
                    'cancel_at_period_end' => true,
                ]);
                $sub     = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                $endDate = \Carbon\Carbon::createFromTimestamp($sub->current_period_end);
            } catch (\Throwable $e) {
                Log::error('Stripe cancel error: '.$e->getMessage());
            }
        }

        $user->update(['subscription_status' => 'canceling']);

        return redirect()->route('account')
            ->with('success', 'Subscription canceled. You will have full access to your '.$user->planName().' plan until '.$endDate->format('M d, Y').'. No further charges will be made.');
    }

    // ── Resume Subscription (undo cancel) ────────────────────────
    public function resume(Request $request)
    {
        $user = Auth::user();

        if (!$user->stripe_subscription_id) {
            return redirect()->route('account')->with('error', 'No subscription found.');
        }

        try {
            \Stripe\Subscription::update($user->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            $user->update(['subscription_status' => 'active']);

            return redirect()->route('account')
                ->with('success', 'Subscription resumed! Your '.$user->planName().' plan will continue as normal.');
        } catch (\Throwable $e) {
            Log::error('Stripe resume error: '.$e->getMessage());
            return redirect()->route('account')->with('error', 'Could not resume subscription. Please contact support.');
        }
    }

    // ── Stripe Webhook ────────────────────────────────────────────
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature failed: '.$e->getMessage());
            return response('Signature mismatch', 400);
        }

        match ($event->type) {
            'invoice.payment_succeeded'    => $this->handlePaymentSucceeded($event->data->object),
            'invoice.payment_failed'       => $this->handlePaymentFailed($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            default => null,
        };

        return response('OK', 200);
    }

    private function handlePaymentSucceeded(object $invoice): void
    {
        $sub  = \Stripe\Subscription::retrieve($invoice->subscription);
        $user = User::where('stripe_customer_id', $invoice->customer)->first();
        if (!$user) return;

        $planSlug = $sub->metadata->plan_slug ?? $user->plan ?? 'verified';
        $plan     = Plan::findBySlug($planSlug);

        $user->update([
            'plan'                   => $planSlug,
            'plan_expires_at'        => now()->addDays(30),
            'stripe_subscription_id' => $sub->id,
            'subscription_status'    => 'active',
        ]);

        // Avoid duplicate on first payment (already recorded in success())
        $alreadyRecorded = PaymentHistory::where('user_id', $user->id)
            ->where('stripe_invoice_id', $invoice->id)
            ->exists();

        if (!$alreadyRecorded) {
            PaymentHistory::create([
                'user_id'                => $user->id,
                'stripe_invoice_id'      => $invoice->id,
                'stripe_subscription_id' => $sub->id,
                'plan_slug'              => $planSlug,
                'plan_name'              => $plan->name ?? ucfirst($planSlug),
                'amount'                 => ($invoice->amount_paid ?? 0) / 100,
                'currency'               => $invoice->currency ?? 'usd',
                'status'                 => 'paid',
                'description'            => ($plan->name ?? ucfirst($planSlug)).' Plan — Monthly Renewal',
                'paid_at'                => now(),
            ]);
        }

        Log::info("Subscription renewed for user {$user->id} — plan: {$planSlug}");
    }

    private function handlePaymentFailed(object $invoice): void
    {
        $user = User::where('stripe_customer_id', $invoice->customer)->first();
        if (!$user) return;

        $user->update(['subscription_status' => 'past_due']);

        $planSlug = $user->plan ?? 'verified';
        $plan     = Plan::findBySlug($planSlug);

        PaymentHistory::create([
            'user_id'           => $user->id,
            'stripe_invoice_id' => $invoice->id,
            'plan_slug'         => $planSlug,
            'plan_name'         => $plan->name ?? ucfirst($planSlug),
            'amount'            => ($invoice->amount_due ?? 0) / 100,
            'currency'          => $invoice->currency ?? 'usd',
            'status'            => 'failed',
            'description'       => 'Payment failed — '.($plan->name ?? ucfirst($planSlug)).' Plan',
            'paid_at'           => now(),
        ]);

        Log::warning("Payment failed for user {$user->id}");
    }

    private function handleSubscriptionDeleted(object $subscription): void
    {
        $user = User::where('stripe_subscription_id', $subscription->id)->first();
        if (!$user) return;

        $user->update([
            'plan'                   => 'free',
            'plan_expires_at'        => null,
            'stripe_subscription_id' => null,
            'subscription_status'    => 'canceled',
        ]);

        Log::info("Subscription canceled for user {$user->id} — downgraded to free");
    }

    private function handleSubscriptionUpdated(object $subscription): void
    {
        $user = User::where('stripe_subscription_id', $subscription->id)->first();
        if (!$user) return;

        $status = $subscription->cancel_at_period_end ? 'canceling' : $subscription->status;
        $endDate = $subscription->current_period_end
            ? \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)
            : null;

        $user->update([
            'subscription_status' => $status,
            'plan_expires_at'     => $endDate ?? $user->plan_expires_at,
        ]);
    }
}
