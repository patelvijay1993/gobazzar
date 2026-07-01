<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ContentModerator
{
    // AI-powered text check via OpenAI Moderation API (free)
    private function aiCheckText(string $text): ?string
    {
        $apiKey = config('services.openai.key');
        if (!$apiKey || mb_strlen(trim($text)) < 10) return null;

        try {
            $res = Http::withToken($apiKey)->timeout(6)
                ->post('https://api.openai.com/v1/moderations', ['input' => strip_tags($text)]);

            if (!$res->successful()) return null;

            $result = $res->json('results.0');
            if (!$result || !$result['flagged']) return null;

            $cats = array_filter($result['categories'] ?? []);
            $cat  = array_key_first($cats) ?? 'policy_violation';

            $labels = [
                'sexual'                => 'sexual content',
                'sexual/minors'         => 'sexual content involving minors',
                'harassment'            => 'harassment',
                'harassment/threatening'=> 'threatening content',
                'hate'                  => 'hate speech',
                'hate/threatening'      => 'threatening hate speech',
                'violence'              => 'violent content',
                'self-harm'             => 'harmful content',
            ];

            $label = $labels[$cat] ?? 'inappropriate content';
            return "Your content was flagged for {$label}. Please revise and resubmit.";

        } catch (\Throwable $e) {
            Log::warning('OpenAI moderation failed: ' . $e->getMessage());
            return null;
        }
    }

    // Category name → expected Vision labels (lowercase)
    private static array $categoryLabels = [
        // Real Estate
        'real estate'        => ['building','house','home','apartment','property','room','architecture','facade','interior','floor','door','window','roof','villa','condo','bedroom','kitchen','bathroom','living room','office','real estate'],
        'real estate agent'  => ['building','house','home','apartment','property','architecture'],
        'roommates'          => ['building','house','home','apartment','room','interior','bedroom','living room'],
        // Autos
        'autos'              => ['car','vehicle','automobile','truck','van','suv','motor vehicle','wheel','tire','automotive','transport'],
        // Buy & Sell
        'buy & sell'         => [], // general — no strict check
        // Services
        'services'           => [],
        // Travel
        'travel'             => ['travel','tourism','landscape','nature','sky','mountain','beach','hotel','airplane','suitcase','monument','city'],
        'travel agency'      => ['travel','tourism','landscape','city','monument','airplane'],
        // Jobs
        'it & software'      => ['computer','laptop','technology','screen','keyboard','office','software'],
        'accounting'         => ['office','document','finance','business'],
        'healthcare'         => ['hospital','clinic','doctor','medical','health'],
        'education'          => ['school','classroom','book','library','student','education'],
        'retail'             => ['store','shop','product','merchandise'],
        // Events
        'festival'           => ['festival','celebration','crowd','performance','stage','event','fireworks'],
        'music & dance'      => ['music','dance','concert','instrument','stage','performance','singer'],
        'religious'          => ['temple','mosque','church','prayer','religion','spiritual','ceremony'],
        'sports'             => ['sport','game','athlete','stadium','ball','fitness','gym','court'],
        'food'               => ['food','restaurant','meal','dish','cuisine','menu','cooking','vegetable','fruit','drink'],
        // Directory
        'restaurant'         => ['food','restaurant','meal','dish','cuisine','dining','cooking'],
        'grocery'            => ['grocery','food','vegetable','fruit','store','supermarket','product'],
        'salon & spa'        => ['salon','spa','hair','beauty','cosmetics','nail','barber'],
        'fashion'            => ['fashion','clothing','apparel','dress','outfit','model','fabric','style'],
        'jewelry'            => ['jewelry','ring','necklace','bracelet','gold','diamond','gemstone','accessory'],
        'medical'            => ['hospital','clinic','doctor','medical','health','pharmacy','medicine'],
        'dental'             => ['dental','dentist','teeth','clinic','tooth','smile'],
        'immigration'        => ['document','passport','office','paper','form','law'],
        'general'            => [],
    ];

    // Google Vision — base64 file check (works on localhost too)
    public function checkImageFile(string $storagePath, ?string $categoryName = null): ?string
    {
        $apiKey = config('services.google_vision.key');
        if (!$apiKey) return null;

        // Try S3 first, fall back to local disk for backward compatibility
        try {
            $fileContents = Storage::disk('s3')->exists($storagePath)
                ? Storage::disk('s3')->get($storagePath)
                : (file_exists(storage_path('app/public/'.$storagePath))
                    ? file_get_contents(storage_path('app/public/'.$storagePath))
                    : null);

            if (!$fileContents) return null;
            $imageData = base64_encode($fileContents);
        } catch (\Exception $e) {
            Log::warning('ContentModerator: could not read image for Vision check: '.$e->getMessage());
            return null;
        }

        try {

            $res = Http::timeout(15)->post(
                "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}",
                ['requests' => [[
                    'image'    => ['content' => $imageData],
                    'features' => [
                        ['type' => 'SAFE_SEARCH_DETECTION'],
                        ['type' => 'LABEL_DETECTION', 'maxResults' => 15],
                    ],
                ]]]
            );

            if (!$res->successful()) {
                Log::warning('Google Vision error: ' . $res->body());
                return null;
            }

            $response = $res->json('responses.0');

            // 1. Safe Search — adult/racy/violent
            $ann = $response['safeSearchAnnotation'] ?? null;
            if ($ann) {
                $bad = ['LIKELY', 'VERY_LIKELY'];
                if (in_array($ann['adult'] ?? '', $bad) || in_array($ann['racy'] ?? '', $bad)) {
                    return 'This image contains adult or explicit content and cannot be uploaded.';
                }
                if (in_array($ann['violence'] ?? '', $bad)) {
                    return 'This image contains violent content and cannot be uploaded.';
                }
            }

            // 2. Category label match
            if ($categoryName) {
                $key      = mb_strtolower(trim($categoryName));
                $expected = self::$categoryLabels[$key] ?? [];

                if (!empty($expected)) {
                    $detectedLabels = array_map(
                        fn($l) => mb_strtolower($l['description'] ?? ''),
                        $response['labelAnnotations'] ?? []
                    );

                    $matched = false;
                    foreach ($expected as $exp) {
                        foreach ($detectedLabels as $detected) {
                            if (str_contains($detected, $exp) || str_contains($exp, $detected)) {
                                $matched = true;
                                break 2;
                            }
                        }
                    }

                    if (!$matched) {
                        $detected = implode(', ', array_slice($detectedLabels, 0, 5));
                        return "This image does not match the \"{$categoryName}\" category "
                             . "(detected: {$detected}). Please upload a relevant image.";
                    }
                }
            }

        } catch (\Throwable $e) {
            Log::warning('Google Vision image check failed: ' . $e->getMessage());
        }

        return null;
    }

    // URL-based fallback (production only — Google cannot access localhost)
    public function checkImageUrl(string $url, ?string $categoryName = null): ?string
    {
        $apiKey = config('services.google_vision.key');
        if (!$apiKey || app()->environment('local')) return null;

        try {
            $res = Http::timeout(10)->post(
                "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}",
                ['requests' => [[
                    'image'    => ['source' => ['imageUri' => $url]],
                    'features' => [['type' => 'SAFE_SEARCH_DETECTION']],
                ]]]
            );

            if (!$res->successful()) return null;
            $ann = $res->json('responses.0.safeSearchAnnotation') ?? [];
            $bad = ['LIKELY', 'VERY_LIKELY'];

            if (in_array($ann['adult'] ?? '', $bad) || in_array($ann['racy'] ?? '', $bad)) {
                return 'This image contains adult or explicit content and cannot be uploaded.';
            }
            if (in_array($ann['violence'] ?? '', $bad)) {
                return 'This image contains violent content and cannot be uploaded.';
            }
        } catch (\Throwable $e) {
            Log::warning('Google Vision URL check failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Check a piece of text. Returns null if clean, or a user-facing error string.
     *
     * @param  string  $text   The text to inspect
     * @param  string  $label  Field name for the message (e.g. "Title", "Description")
     * @param  int|null $minLen Minimum length override
     */
    public function check(?string $text, string $label, ?int $minLen = null): ?string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return null; // empty handled by required/nullable rules elsewhere
        }

        $cfg   = config('moderation');
        $lower = mb_strtolower($text);
        // Strip HTML (descriptions come from the rich-text editor)
        $plain = trim(strip_tags($text));
        $plainLower = mb_strtolower($plain);

        // 1. Minimum length (on plain text)
        if ($minLen !== null && mb_strlen($plain) < $minLen) {
            return "$label is too short. Please write at least {$minLen} characters with real details.";
        }

        // 2. Banned / abusive words (whole word)
        foreach ($cfg['banned_words'] as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/iu', $plainLower)) {
                return "$label contains inappropriate language. Please remove it and try again.";
            }
        }

        // 3. Junk / placeholder phrases
        foreach ($cfg['junk_phrases'] as $junk) {
            if (str_contains($plainLower, mb_strtolower($junk))) {
                return "$label looks like placeholder or test text. Please write genuine details.";
            }
        }

        // 4. Same character repeated too many times (aaaa, !!!!, 1111)
        if (preg_match('/(.)\1{' . ($cfg['max_repeat_run'] - 1) . ',}/u', $plain)) {
            return "$label contains repeated characters. Please write proper text.";
        }

        // 5. Short pattern repeated (asdasdasd, abcabcabc)
        if (preg_match('/(.{2,4})\1{' . ($cfg['max_pattern_repeat'] - 1) . ',}/u', preg_replace('/\s+/', '', $plainLower))) {
            return "$label looks like random typing. Please write meaningful text.";
        }

        // 6. Single-word junk titles (only applies when the whole field is one of these words)
        $cfg_junk_words = $cfg['junk_title_words'] ?? [];
        if (!empty($cfg_junk_words) && in_array(trim($plainLower), $cfg_junk_words, true)) {
            return "$label is too vague. Please provide a meaningful, descriptive $label.";
        }

        // 7. Gibberish: flag if too many words have a low vowel ratio
        $minVowelRatio     = $cfg['min_vowel_ratio'] ?? 0.25;
        $maxGibberishFrac  = $cfg['max_gibberish_word_fraction'] ?? 0.50;
        if (preg_match_all('/\b[a-z]{4,}\b/u', $plainLower, $m) && count($m[0]) >= 2) {
            $gibberish = 0;
            foreach ($m[0] as $token) {
                $vowels = preg_match_all('/[aeiou]/', $token);
                if (($vowels / mb_strlen($token)) < $minVowelRatio) {
                    $gibberish++;
                }
            }
            if ($gibberish / count($m[0]) >= $maxGibberishFrac) {
                return "$label appears to contain gibberish. Please write real words.";
            }
        }

        // 8. Too many links
        if (preg_match_all('/https?:\/\/|www\./i', $plain) > $cfg['max_links']) {
            return "$label contains too many links.";
        }

        // 9. Too many phone numbers
        if (preg_match_all('/\+?\d[\d\s\-().]{7,}\d/', $plain) > $cfg['max_phones']) {
            return "$label contains too many phone numbers.";
        }

        return null;
    }

    /**
     * Validate title + description together. Throws ValidationException on first problem.
     *
     * @param  array  $fields  ['title' => [text, label, minLen], ...] keyed by request key
     */
    public function validateOrFail(array $fields): void
    {
        $errors = [];
        foreach ($fields as $key => [$text, $label, $minLen]) {
            $error = $this->check($text, $label, $minLen);
            if ($error) {
                $errors[$key] = $error;
            }
        }

        // Title == Description (lazy duplicate)
        if (isset($fields['title'][0], $fields['description'][0])) {
            $t = mb_strtolower(trim(strip_tags($fields['title'][0])));
            $d = mb_strtolower(trim(strip_tags($fields['description'][0])));
            if ($t !== '' && $t === $d) {
                $errors['description'] = 'Description must be different from the title — add real details.';
            }
        }

        if ($errors) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }

        // AI check via OpenAI Moderation API on combined text
        $combined = implode("\n", array_map(fn($f) => strip_tags((string)($f[0] ?? '')), $fields));
        $aiError  = $this->aiCheckText($combined);
        if ($aiError) {
            throw \Illuminate\Validation\ValidationException::withMessages(['title' => $aiError]);
        }
    }
}
