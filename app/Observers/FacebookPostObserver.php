<?php

namespace App\Observers;

use App\Jobs\PostToFacebookPage;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared by Listing, Job, Event and Business: the moment a record's `status` becomes
 * 'active' (on create or update) — and it hasn't already been posted — share it on the
 * GoBazaar Facebook Page (runs synchronously; see PostToFacebookPage). One observer class
 * registered for all four models in AppServiceProvider, since the "just went active, not
 * yet posted" rule is identical for each.
 */
class FacebookPostObserver
{
    public function created(Model $record): void
    {
        $this->maybeDispatch($record);
    }

    public function updated(Model $record): void
    {
        // Only fire when status actually changed to 'active' this save — not on every
        // unrelated field edit of an already-active record.
        if (!$record->wasChanged('status')) return;

        $this->maybeDispatch($record);
    }

    private function maybeDispatch(Model $record): void
    {
        if ($record->status !== 'active') return;
        if (!empty($record->fb_post_id)) return;

        PostToFacebookPage::dispatch($record::class, $record->id);
    }
}
