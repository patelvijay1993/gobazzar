<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\Event;
use App\Models\Job;
use App\Models\Listing;
use App\Observers\AutoSeoObserver;
use App\Observers\FacebookPostObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.simple-default');

        // Auto-share to the GoBazaar Facebook Page the moment any of these go live.
        Listing::observe(FacebookPostObserver::class);
        Job::observe(FacebookPostObserver::class);
        Event::observe(FacebookPostObserver::class);
        Business::observe(FacebookPostObserver::class);

        // Auto-generate SEO title/description on creation, if not already set.
        Listing::observe(AutoSeoObserver::class);
        Job::observe(AutoSeoObserver::class);
        Event::observe(AutoSeoObserver::class);
        Business::observe(AutoSeoObserver::class);
    }
}
