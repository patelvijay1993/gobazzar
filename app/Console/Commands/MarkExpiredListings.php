<?php

namespace App\Console\Commands;

use App\Models\BusinessPost;
use App\Models\Job;
use App\Models\Listing;
use App\Models\Matrimonial;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkExpiredListings extends Command
{
    protected $signature   = 'listings:mark-expired';
    protected $description = 'Set status=expired on listings, jobs, and business posts past their expires_at date';

    public function handle(): int
    {
        $now = Carbon::now();

        $counts = [
            'Listing'      => Listing::where('status', 'active')->whereNotNull('expires_at')->where('expires_at', '<=', $now)->update(['status' => 'expired']),
            'Job'          => Job::where('status', 'active')->whereNotNull('expires_at')->where('expires_at', '<=', $now)->update(['status' => 'expired']),
            'BusinessPost' => BusinessPost::where('status', 'active')->whereNotNull('expires_at')->where('expires_at', '<=', $now)->update(['status' => 'expired']),
            'Matrimonial'  => Matrimonial::where('status', 'active')->whereNotNull('expires_at')->where('expires_at', '<=', $now)->update(['status' => 'expired']),
        ];

        foreach ($counts as $model => $count) {
            $this->info("Marked {$count} expired {$model}(s).");
        }

        return self::SUCCESS;
    }
}
