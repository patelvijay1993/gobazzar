<?php

namespace App\Observers;

use App\Jobs\GenerateSeoMeta;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared by Listing, Job, Event and Business: the moment a record is created, generate
 * its SEO title/description via AI (if not already set) so every page — even ones posted
 * with a bare-minimum description — gets a real, search-friendly <title> and meta
 * description instead of falling back to a generic template. One observer class
 * registered for all four models in AppServiceProvider.
 */
class AutoSeoObserver
{
    public function created(Model $record): void
    {
        if (!empty($record->seo_title) && !empty($record->seo_description)) return;

        GenerateSeoMeta::dispatch($record::class, $record->id);
    }
}
