<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Event;
use App\Models\Job;
use App\Models\Listing;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    private static array $typeMap = [
        'listing'  => Listing::class,
        'job'      => Job::class,
        'event'    => Event::class,
        'business' => Business::class,
    ];

    public function toggle(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasFavorites()) {
            return response()->json(['error' => 'Upgrade to Verified plan to save favorites.'], 403);
        }

        $request->validate([
            'type' => 'required|in:listing,job,event,business',
            'id'   => 'required|integer',
        ]);

        $modelClass = self::$typeMap[$request->type];
        $model = $modelClass::findOrFail($request->id);

        $existing = UserFavorite::where('user_id', $user->id)
            ->where('favoriteable_type', $modelClass)
            ->where('favoriteable_id', $model->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
        } else {
            UserFavorite::create([
                'user_id'           => $user->id,
                'favoriteable_type' => $modelClass,
                'favoriteable_id'   => $model->id,
                'created_at'        => now(),
            ]);
            $favorited = true;
        }

        return response()->json([
            'favorited' => $favorited,
            'count'     => $model->favorites()->count(),
        ]);
    }

    public function index()
    {
        $user = Auth::user();

        abort_unless($user->hasFavorites(), 403);

        $favorites = UserFavorite::where('user_id', $user->id)
            ->with('favoriteable')
            ->latest('created_at')
            ->get()
            ->filter(fn ($f) => $f->favoriteable !== null); // skip deleted items

        $grouped = $favorites->groupBy(fn ($f) => class_basename($f->favoriteable_type));

        return view('user.favorites', compact('favorites', 'grouped'));
    }
}
