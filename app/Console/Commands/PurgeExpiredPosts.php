<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\Matrimonial;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredPosts extends Command
{
    protected $signature   = 'posts:purge-expired';
    protected $description = 'Delete jobs and matrimonial profiles that have passed their expires_at date. Listings are handled separately by listings:mark-expired + listings:purge-inactive (expire → inactive → 7-day grace → delete).';

    public function handle(): int
    {
        $now = Carbon::now();

        // Jobs
        $jobs = Job::whereNotNull('expires_at')->where('expires_at', '<=', $now)->get();
        foreach ($jobs as $r) {
            if ($r->company_logo) Storage::disk(config('filesystems.default'))->delete($r->company_logo);
            $r->delete();
        }
        $this->info("Deleted {$jobs->count()} expired job listing(s).");

        // Matrimonials
        $matris = Matrimonial::whereNotNull('expires_at')->where('expires_at', '<=', $now)->get();
        foreach ($matris as $r) {
            if ($r->photo) Storage::disk(config('filesystems.default'))->delete($r->photo);
            $r->delete();
        }
        $this->info("Deleted {$matris->count()} expired matrimonial profile(s).");

        return self::SUCCESS;
    }
}

