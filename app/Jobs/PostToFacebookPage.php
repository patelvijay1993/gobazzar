<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\Event;
use App\Models\Job as JobListing;
use App\Models\Listing;
use App\Services\FacebookPageService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Posts a newly-active Listing/Job/Event/Business to the GoBazaar Facebook Page.
 * Dispatched by the matching model Observer the moment `status` first becomes 'active'.
 *
 * Runs synchronously (not queued) — this app has no queue worker/cron set up on its
 * shared hosting, so a queued job would just sit unprocessed in the `jobs` table forever.
 * FacebookPageService never throws, so a slow/down Facebook API only adds a little latency
 * to the user's post — it can't fail their submission.
 */
class PostToFacebookPage
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $modelClass,
        public int $modelId,
    ) {}

    public function handle(FacebookPageService $fb): void
    {
        $record = $this->modelClass::find($this->modelId);
        if (!$record) return;

        // Already posted (e.g. the job ran twice) — never double-post the same record.
        if ($record->fb_post_id) return;

        // Status may have changed again (e.g. flagged/rejected) between dispatch and run.
        if ($record->status !== 'active') return;

        [$message, $link, $imageUrl] = match (true) {
            $record instanceof Listing  => $this->forListing($record),
            $record instanceof JobListing => $this->forJob($record),
            $record instanceof Event   => $this->forEvent($record),
            $record instanceof Business => $this->forBusiness($record),
            default => [null, null, null],
        };

        if (!$message) return;

        $postId = $fb->post($message, $link, $imageUrl);

        if ($postId) {
            $record->update(['fb_post_id' => $postId]);
        }
    }

    /** @return array{0: string, 1: string, 2: ?string} */
    private function forListing(Listing $l): array
    {
        $message = "🛍️ New listing on GoBazaar: {$l->title}"
            . ($l->formatted_price ? " — {$l->formatted_price}" : '')
            . ($l->city ? "\n📍 {$l->city}" . ($l->province ? ", {$l->province}" : '') : '');

        return [$message, route('classifieds.show', $l->slug), $l->image_url];
    }

    /** @return array{0: string, 1: string, 2: ?string} */
    private function forJob(JobListing $j): array
    {
        $message = "💼 New job on GoBazaar: {$j->title}"
            . ($j->company ? " at {$j->company}" : '')
            . ($j->city ? "\n📍 {$j->city}" . ($j->province ? ", {$j->province}" : '') : '');

        return [$message, route('jobs.show', $j->slug), null];
    }

    /** @return array{0: string, 1: string, 2: ?string} */
    private function forEvent(Event $e): array
    {
        $when = $e->start_date ? $e->start_date->format('M j, Y') : null;

        $message = "📅 New event on GoBazaar: {$e->title}"
            . ($when ? "\n🗓️ {$when}" : '')
            . ($e->venue ? "\n📍 {$e->venue}" : '');

        return [$message, route('events.show', $e->slug), $e->image_url];
    }

    /** @return array{0: string, 1: string, 2: ?string} */
    private function forBusiness(Business $b): array
    {
        $message = "🏪 New business on GoBazaar: {$b->name}"
            . ($b->city ? "\n📍 {$b->city}" . ($b->province ? ", {$b->province}" : '') : '');

        return [$message, route('directory.show', $b->slug), $b->image_url];
    }
}
