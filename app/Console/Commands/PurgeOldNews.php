<?php

namespace App\Console\Commands;

use App\Models\NewsArticle;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeOldNews extends Command
{
    protected $signature   = 'news:purge-old';
    protected $description = 'Permanently delete news articles older than 7 days (by pub_date), keeping the table small so /news stays fast.';

    private const MAX_AGE_DAYS = 7;
    private const CHUNK_SIZE   = 1000;

    public function handle(): int
    {
        $cutoff = Carbon::now()->subDays(self::MAX_AGE_DAYS);

        $total = 0;
        // Delete in small batches rather than one giant DELETE — this table can be in the
        // hundreds of thousands of rows, and a single huge delete can itself lock things
        // up on a resource-constrained server (exactly what caused the Nov 2 outage).
        do {
            $deleted = NewsArticle::where('pub_date', '<', $cutoff)
                ->limit(self::CHUNK_SIZE)
                ->delete();
            $total += $deleted;
        } while ($deleted > 0);

        $this->info("Deleted {$total} news article(s) older than " . self::MAX_AGE_DAYS . " days.");
        return self::SUCCESS;
    }
}
