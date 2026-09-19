<?php

namespace App\Console\Commands;

use App\Models\BusinessPost;
use App\Models\Event;
use App\Models\Job;
use App\Models\Listing;
use App\Models\Matrimonial;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkExpiredListings extends Command
{
    protected $signature   = 'listings:mark-expired';
    protected $description = 'Mark listings/events past expires_at as inactive, and expired business posts as expired. Jobs/Matrimonials past expires_at are marked inactive.';

    public function handle(): int
    {
        $now = Carbon::now();

        // Listings and Events: active → inactive once expires_at passes.
        // inactive_at is stamped so listings:purge-inactive knows when the 7-day grace period started.
        $expiredListings = Listing::where('status', 'active')
            ->whereNotNull('expires_at')->where('expires_at', '<=', $now)
            ->update(['status' => 'inactive', 'inactive_at' => $now]);

        $expiredEvents = Event::where('status', 'active')
            ->whereNotNull('expires_at')->where('expires_at', '<=', $now)
            ->update(['status' => 'inactive', 'inactive_at' => $now]);

        // Jobs and Matrimonials: their status enums don't include 'expired', only 'inactive'.
        $expiredJobs = Job::where('status', 'active')
            ->whereNotNull('expires_at')->where('expires_at', '<=', $now)
            ->update(['status' => 'inactive']);

        $expiredMatrimonials = Matrimonial::where('status', 'active')
            ->whereNotNull('expires_at')->where('expires_at', '<=', $now)
            ->update(['status' => 'inactive']);

        // BusinessPost's enum does include 'expired', so it keeps its original behavior.
        $expiredBusinessPosts = BusinessPost::where('status', 'active')
            ->whereNotNull('expires_at')->where('expires_at', '<=', $now)
            ->update(['status' => 'expired']);

        $counts = [
            'Listing'      => $expiredListings,
            'Event'        => $expiredEvents,
            'Job'          => $expiredJobs,
            'Matrimonial'  => $expiredMatrimonials,
            'BusinessPost' => $expiredBusinessPosts,
        ];

        foreach ($counts as $model => $count) {
            $this->info("Marked {$count} expired {$model}(s).");
        }

        return self::SUCCESS;
    }
}
