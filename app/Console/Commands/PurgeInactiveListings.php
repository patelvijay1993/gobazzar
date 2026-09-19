<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Listing;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeInactiveListings extends Command
{
    protected $signature   = 'listings:purge-inactive';
    protected $description = 'Soft-delete listings and events that have been inactive for 7+ days (inactive_at set by listings:mark-expired, or by the owner/admin manually toggling status). Files stay on disk so a restore brings images back too, same as a manual delete.';

    private const GRACE_DAYS = 7;

    public function handle(): int
    {
        $cutoff = Carbon::now()->subDays(self::GRACE_DAYS);

        $listingCount = Listing::where('status', 'inactive')
            ->whereNotNull('inactive_at')
            ->where('inactive_at', '<=', $cutoff)
            ->update(['deleted_at' => Carbon::now()]);
        $this->info("Deleted {$listingCount} listing(s) inactive for " . self::GRACE_DAYS . "+ days.");

        $eventCount = Event::where('status', 'inactive')
            ->whereNotNull('inactive_at')
            ->where('inactive_at', '<=', $cutoff)
            ->update(['deleted_at' => Carbon::now()]);
        $this->info("Deleted {$eventCount} event(s) inactive for " . self::GRACE_DAYS . "+ days.");

        return self::SUCCESS;
    }
}
