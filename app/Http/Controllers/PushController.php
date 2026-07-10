<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushController extends Controller
{
    // Save browser push subscription
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint'   => 'required|string',
            'public_key' => 'nullable|string',
            'auth_token' => 'nullable|string',
        ]);

        PushSubscription::updateOrCreate(
            ['user_id' => Auth::id(), 'endpoint' => $request->endpoint],
            ['public_key' => $request->public_key, 'auth_token' => $request->auth_token]
        );

        return response()->json(['ok' => true]);
    }

    // Remove subscription (when user unsubscribes)
    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['ok' => true]);
    }

    // Return VAPID public key for browser to subscribe
    public function vapidKey()
    {
        return response()->json(['key' => config('services.vapid.public_key', '')]);
    }

    // Send push to a user (called internally)
    public static function sendToUser(int $userId, string $title, string $body, string $url = '/chat'): void
    {
        $pubKey  = config('services.vapid.public_key');
        $privKey = config('services.vapid.private_key');
        $subject = config('services.vapid.subject', 'mailto:admin@gobazzarweb.heavendwell.com');

        if (!$pubKey || !$privKey) return;

        $subscriptions = PushSubscription::where('user_id', $userId)->get();
        if ($subscriptions->isEmpty()) return;

        try {
            $webPush = new \Minishlink\WebPush\WebPush([
                'VAPID' => [
                    'subject'    => $subject,
                    'publicKey'  => $pubKey,
                    'privateKey' => $privKey,
                ],
            ]);

            $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url, 'tag' => 'gobazaar-msg']);

            foreach ($subscriptions as $sub) {
                $subscription = \Minishlink\WebPush\Subscription::create([
                    'endpoint'        => $sub->endpoint,
                    'publicKey'       => $sub->public_key,
                    'authToken'       => $sub->auth_token,
                    'contentEncoding' => 'aesgcm',
                ]);
                $webPush->queueNotification($subscription, $payload);
            }

            foreach ($webPush->flush() as $report) {
                // Remove expired subscriptions
                if ($report->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint', $report->getEndpoint())->delete();
                }
            }
        } catch (\Throwable) {
            // Silently fail — push is best-effort
        }
    }
}
