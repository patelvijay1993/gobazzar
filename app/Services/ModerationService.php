<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    // Returns ['flagged' => bool, 'reason' => string|null]
    public function checkText(string $text): array
    {
        $apiKey = config('services.openai.key');
        if (!$apiKey || empty(trim($text))) {
            return ['flagged' => false, 'reason' => null];
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(8)
                ->post('https://api.openai.com/v1/moderations', [
                    'input' => $text,
                ]);

            if (!$response->successful()) {
                return ['flagged' => false, 'reason' => null];
            }

            $result = $response->json('results.0');
            if (!$result || !$result['flagged']) {
                return ['flagged' => false, 'reason' => null];
            }

            // Find top flagged category
            $categories = $result['categories'] ?? [];
            $flagged = array_filter($categories);
            $reason = !empty($flagged) ? array_key_first($flagged) : 'policy_violation';

            return ['flagged' => true, 'reason' => $reason];

        } catch (\Throwable $e) {
            Log::warning('ModerationService text check failed: ' . $e->getMessage());
            return ['flagged' => false, 'reason' => null];
        }
    }

    // Returns ['flagged' => bool, 'reason' => string|null]
    public function checkImage(string $imageUrl): array
    {
        $apiKey = config('services.google_vision.key');
        if (!$apiKey || !str_starts_with($imageUrl, 'http')) {
            return ['flagged' => false, 'reason' => null];
        }

        try {
            $response = Http::timeout(10)
                ->post("https://vision.googleapis.com/v1/images:annotate?key={$apiKey}", [
                    'requests' => [[
                        'image'    => ['source' => ['imageUri' => $imageUrl]],
                        'features' => [['type' => 'SAFE_SEARCH_DETECTION']],
                    ]],
                ]);

            if (!$response->successful()) {
                return ['flagged' => false, 'reason' => null];
            }

            $annotation = $response->json('responses.0.safeSearchAnnotation');
            if (!$annotation) {
                return ['flagged' => false, 'reason' => null];
            }

            // LIKELY or VERY_LIKELY = flag
            $badLevels = ['LIKELY', 'VERY_LIKELY'];
            $checks = [
                'adult'    => 'pornography',
                'violence' => 'harmful',
                'racy'     => 'pornography',
            ];

            foreach ($checks as $field => $reason) {
                if (in_array($annotation[$field] ?? '', $badLevels)) {
                    return ['flagged' => true, 'reason' => $reason];
                }
            }

            return ['flagged' => false, 'reason' => null];

        } catch (\Throwable $e) {
            Log::warning('ModerationService image check failed: ' . $e->getMessage());
            return ['flagged' => false, 'reason' => null];
        }
    }

    // Check text + all images, return first flagged result
    public function checkListing(string $text, array $imageUrls = []): array
    {
        $textResult = $this->checkText($text);
        if ($textResult['flagged']) {
            return $textResult;
        }

        foreach ($imageUrls as $url) {
            if (!$url) continue;
            $imgResult = $this->checkImage($url);
            if ($imgResult['flagged']) {
                return $imgResult;
            }
        }

        return ['flagged' => false, 'reason' => null];
    }
}
