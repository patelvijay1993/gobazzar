<?php

namespace App\Console\Commands;

use App\Models\NewsArticle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchNews extends Command
{
    protected $signature   = 'news:fetch';
    protected $description = 'Fetch latest news from newsdata.io and store new articles (skips duplicates)';

    private const ENDPOINT   = 'https://newsdata.io/api/1/latest';
    private const COUNTRY    = 'in';
    private const LANGUAGES  = ['gu', 'hi', 'en'];
    private const CATEGORIES = 'breaking,business,education,crime,politics';

    public function handle(): int
    {
        $apiKey = config('services.newsdata.key');

        if (! $apiKey) {
            $this->error('NEWSDATA_API_KEY is not configured.');
            return self::FAILURE;
        }

        $totalCreated = 0;
        $totalSkipped = 0;

        foreach (self::LANGUAGES as $language) {
            [$created, $skipped] = $this->fetchLanguage($apiKey, $language);
            $totalCreated += $created;
            $totalSkipped += $skipped;
            $this->info("[{$language}] {$created} new, {$skipped} duplicate(s) skipped.");
        }

        $this->info("News fetch complete: {$totalCreated} new, {$totalSkipped} duplicate(s) skipped.");
        return self::SUCCESS;
    }

    /** @return array{0: int, 1: int} [created, skipped] */
    private function fetchLanguage(string $apiKey, string $language): array
    {
        $created = 0;
        $skipped = 0;
        $page    = null;

        // newsdata.io paginates via nextPage cursor; walk a bounded number of pages per run.
        for ($i = 0; $i < 5; $i++) {
            $response = Http::timeout(15)->get(self::ENDPOINT, array_filter([
                'apikey'   => $apiKey,
                'country'  => self::COUNTRY,
                'language' => $language,
                'category' => self::CATEGORIES,
                'page'     => $page,
            ]));

            if (! $response->successful()) {
                Log::warning('news:fetch request failed', ['language' => $language, 'status' => $response->status(), 'body' => $response->body()]);
                break;
            }

            $data = $response->json();

            $pageSkipped = 0;
            foreach ($data['results'] ?? [] as $item) {
                if (empty($item['article_id']) || empty($item['title'])) {
                    continue;
                }

                if (NewsArticle::where('article_id', $item['article_id'])->exists()) {
                    $skipped++;
                    $pageSkipped++;
                    continue;
                }

                NewsArticle::create([
                    'article_id'  => $item['article_id'],
                    'title'       => $item['title'],
                    'slug'        => $this->uniqueSlug($item['title'], $item['article_id']),
                    'description' => $item['description'] ?? null,
                    'content'     => (($item['content'] ?? null) && $item['content'] !== 'ONLY AVAILABLE IN PAID PLANS') ? $item['content'] : null,
                    'link'        => $item['link'] ?? null,
                    'image_url'   => $item['image_url'] ?? null,
                    'video_url'   => $item['video_url'] ?? null,
                    'category'    => $item['category'] ?? null,
                    'keywords'    => $item['keywords'] ?? null,
                    'language'    => $item['language'] ?? null,
                    'country'     => $item['country'] ?? null,
                    'source_id'   => $item['source_id'] ?? null,
                    'source_name' => $item['source_name'] ?? null,
                    'source_icon' => $item['source_icon'] ?? null,
                    'source_url'  => $item['source_url'] ?? null,
                    'pub_date'    => $item['pubDate'] ?? null,
                ]);
                $created++;
            }

            $page = $data['nextPage'] ?? null;

            // Stop paging once we hit articles we already have, or there's no more data.
            if (! $page || $pageSkipped > 0) {
                break;
            }
        }

        return [$created, $skipped];
    }

    private function uniqueSlug(string $title, string $articleId): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'news';
        }
        $slug = $base.'-'.substr($articleId, 0, 8);

        $i = 1;
        $original = $slug;
        while (NewsArticle::where('slug', $slug)->exists()) {
            $slug = $original.'-'.(++$i);
        }

        return $slug;
    }
}
