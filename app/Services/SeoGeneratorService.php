<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Generates a search-friendly SEO <title> + meta description for a listing/job/event/
 * business via Groq, so pages don't rely solely on whatever raw title/description the
 * poster happened to type. Never throws — a failed/unconfigured AI call just means the
 * page keeps using its existing template-based fallback (see each show.blade.php).
 */
class SeoGeneratorService
{
    private ?string $apiKey;

    public function __construct()
    {
        $keys = config('services.groq.keys', []);
        $this->apiKey = is_array($keys) ? (array_values(array_filter($keys))[0] ?? null) : $keys;
    }

    public function isConfigured(): bool
    {
        return (bool) $this->apiKey;
    }

    /**
     * @param string $type One of: listing, job, event, business.
     * @param array $context Freeform facts about the post (title, category, city, price, etc.)
     *                        — only non-empty values are sent to the model.
     * @return array{0: ?string, 1: ?string} [seo_title, seo_description], both null on failure.
     */
    public function generate(string $type, array $context): array
    {
        if (!$this->isConfigured()) {
            Log::info('SeoGeneratorService: skipped — no Groq API key configured.');
            return [null, null];
        }

        $facts = collect($context)->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v, $k) => "{$k}: {$v}")
            ->implode("\n");

        $typeNote = match ($type) {
            'listing'  => 'a classified ad (something for sale) on GoBazaar, a Canadian community marketplace',
            'job'      => 'a job posting on GoBazaar, a Canadian community marketplace',
            'event'    => 'a community event listed on GoBazaar, a Canadian community marketplace',
            'business' => 'a local business directory listing on GoBazaar, a Canadian community marketplace',
            default    => 'a listing on GoBazaar, a Canadian community marketplace',
        };

        $prompt = <<<PROMPT
You are an SEO copywriter. Here are the facts about {$typeNote}:

{$facts}

Write:
1. "seo_title": a search-engine <title> tag. Under 60 characters. Include the most
   important specific detail (e.g. item name, job title, city) — not generic filler.
2. "seo_description": a meta description. Under 155 characters. Compelling, specific,
   makes someone want to click. No quotation marks, no emoji, no markdown.

Return ONLY valid JSON, exactly this shape, no markdown fences, no explanation:
{"seo_title":"...","seo_description":"..."}
PROMPT;

        try {
            $res = Http::withToken($this->apiKey)
                ->timeout(20)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'qwen/qwen3.8-27b',
                    'temperature' => 0.5,
                    'messages'    => [
                        ['role' => 'system', 'content' => 'You are an SEO copywriter. Always respond with valid JSON only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if (!$res->successful()) {
                Log::warning('SeoGeneratorService: Groq call failed', ['status' => $res->status(), 'body' => $res->body()]);
                return [null, null];
            }

            $text = trim($res->json('choices.0.message.content') ?? '');
            $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
            $text = preg_replace('/\s*```\s*$/i', '', trim($text));

            $parsed = json_decode($text, true);
            if (!is_array($parsed) || empty($parsed['seo_title'])) {
                Log::warning('SeoGeneratorService: unexpected AI response', ['text' => $text]);
                return [null, null];
            }

            $title = Str::limit(trim($parsed['seo_title']), 70, '');
            $desc  = isset($parsed['seo_description']) ? Str::limit(trim($parsed['seo_description']), 170, '') : null;

            return [$title ?: null, $desc ?: null];
        } catch (\Throwable $e) {
            Log::error('SeoGeneratorService: exception', ['error' => $e->getMessage()]);
            return [null, null];
        }
    }
}
