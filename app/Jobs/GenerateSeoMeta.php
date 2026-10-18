<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\Event;
use App\Models\Job as JobListing;
use App\Models\Listing;
use App\Services\SeoGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Generates and saves seo_title/seo_description for a newly-created Listing/Job/Event/
 * Business, if they're not already set. Dispatched by AutoSeoObserver on create.
 *
 * Runs synchronously (not queued) — same reasoning as PostToFacebookPage: this app has
 * no queue worker/cron set up on its shared hosting. SeoGeneratorService never throws, so
 * a slow/down/unconfigured AI call only adds a little latency, never breaks the user's post.
 */
class GenerateSeoMeta
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $modelClass,
        public int $modelId,
    ) {}

    public function handle(SeoGeneratorService $seo): void
    {
        $record = $this->modelClass::find($this->modelId);
        if (!$record) return;

        // Already has one (or both) set — never overwrite a value that was auto-generated
        // before, or one an admin/user deliberately typed.
        if (!empty($record->seo_title) && !empty($record->seo_description)) return;

        [$type, $context] = match (true) {
            $record instanceof Listing    => ['listing', $this->listingContext($record)],
            $record instanceof JobListing => ['job', $this->jobContext($record)],
            $record instanceof Event      => ['event', $this->eventContext($record)],
            $record instanceof Business   => ['business', $this->businessContext($record)],
            default => [null, []],
        };

        if (!$type) return;

        [$title, $description] = $seo->generate($type, $context);

        $updates = array_filter([
            'seo_title'       => empty($record->seo_title) ? $title : null,
            'seo_description' => empty($record->seo_description) ? $description : null,
        ]);

        if ($updates) {
            $record->update($updates);
        }
    }

    private function listingContext(Listing $l): array
    {
        return [
            'title'       => $l->title,
            'category'    => $l->category?->name,
            'description' => strip_tags($l->description ?? ''),
            'price'       => $l->price ? "\${$l->price}" : null,
            'city'        => $l->city,
            'province'    => $l->province,
        ];
    }

    private function jobContext(JobListing $j): array
    {
        return [
            'job title'  => $j->title,
            'company'    => $j->company,
            'job type'   => $j->job_type,
            'work mode'  => $j->work_mode,
            'city'       => $j->city,
            'province'   => $j->province,
            'salary'     => $j->salary,
        ];
    }

    private function eventContext(Event $e): array
    {
        return [
            'event name'  => $e->title,
            'description' => strip_tags($e->description ?? ''),
            'venue'       => $e->venue,
            'city'        => $e->city,
            'province'    => $e->province,
            'date'        => $e->start_date?->format('M j, Y'),
        ];
    }

    private function businessContext(Business $b): array
    {
        return [
            'business name' => $b->name,
            'category'      => $b->category?->name,
            'description'   => strip_tags($b->description ?? ''),
            'city'          => $b->city,
            'province'      => $b->province,
        ];
    }
}
