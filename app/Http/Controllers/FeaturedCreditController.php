<?php

namespace App\Http\Controllers;

use App\Models\FeaturedCreditLog;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeaturedCreditController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate(['listing_id' => 'required|integer']);

        $user    = Auth::user();
        $listing = Listing::findOrFail($request->listing_id);

        abort_if($listing->user_id !== $user->id, 403);
        abort_unless($user->featuredCredits() > 0, 403);

        // --- UN-FEATURE ---
        if ($listing->is_featured) {
            $listing->update(['is_featured' => false]);

            // Mark log entry closed
            FeaturedCreditLog::where('listing_id', $listing->id)
                ->whereNull('unfeatured_at')
                ->update(['unfeatured_at' => now()]);

            return response()->json([
                'featured'          => false,
                'credits_remaining' => $user->fresh()->featuredCreditsRemaining(),
                'message'           => 'Listing removed from featured.',
            ]);
        }

        // --- FEATURE ---
        if (!$user->canFeatureListing()) {
            return response()->json([
                'error' => 'No featured credits remaining this month. Resets on '
                    . $user->featured_credits_reset_at?->format('d M Y') . '.',
            ], 422);
        }

        $listing->update(['is_featured' => true]);

        $user->increment('featured_credits_used');

        FeaturedCreditLog::create([
            'user_id'    => $user->id,
            'listing_id' => $listing->id,
            'featured_at' => now(),
        ]);

        return response()->json([
            'featured'          => true,
            'credits_remaining' => $user->fresh()->featuredCreditsRemaining(),
            'message'           => 'Listing is now featured!',
        ]);
    }
}
