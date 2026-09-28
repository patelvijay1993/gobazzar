<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessClaimController extends Controller
{
    /**
     * Landing page for a claim link. Requires auth (route middleware, not `signed`) so a
     * guest is bounced to login/register first — Laravel's redirect()->intended() replays
     * this exact URL (including the signature query params) afterward. The signature is
     * then checked here, explicitly, once the user is authenticated — mirrors
     * AuthController::verificationVerify()'s manual-check pattern for the same reason.
     */
    public function show(Request $request, Business $business)
    {
        abort_unless($request->hasValidSignature(), 403, 'This claim link is invalid or has expired.');

        if ($business->is_claimed) {
            $ownedByMe = (int) $business->user_id === (int) Auth::id();
            return view('business.claim', [
                'business'  => $business,
                'alreadyClaimed' => true,
                'ownedByMe' => $ownedByMe,
            ]);
        }

        return view('business.claim', [
            'business'       => $business,
            'alreadyClaimed' => false,
            'ownedByMe'      => false,
        ]);
    }

    public function confirm(Request $request, Business $business)
    {
        abort_if($business->is_claimed, 409, 'This business has already been claimed.');

        $business->update([
            'user_id'    => Auth::id(),
            'claimed_at' => now(),
            // A claimed business should go through the normal verification review,
            // not inherit whatever verified/featured state it had while admin-owned.
            'is_verified' => false,
        ]);

        ActivityLog::log($request, 'claimed_business', [
            'subject_type'  => Business::class,
            'subject_id'    => $business->id,
            'subject_label' => $business->name,
        ]);

        return redirect()->route('account', ['panel' => 'business'])
            ->with('success', "You've successfully claimed \"{$business->name}\"! Our team will review your verification shortly.");
    }
}
