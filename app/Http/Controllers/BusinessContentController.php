<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BusinessContentController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:150',
            'category'      => 'required|string|max:100',
            'keywords'      => 'nullable|string|max:500',
            'language'      => 'nullable|in:en,gu,hi',
        ]);

        $apiKey = config('services.groq.key');
        if (!$apiKey) {
            return response()->json(['error' => 'AI generation is not configured.'], 503);
        }

        $name     = trim($request->business_name);
        $category = trim($request->category);
        $keywords = trim($request->keywords ?? '');
        $lang     = $request->language ?? 'en';

        $langNote = match($lang) {
            'gu' => 'Write entirely in Gujarati (using Gujarati script).',
            'hi' => 'Write entirely in Hindi (using Devanagari script).',
            default => 'Write in English.',
        };

        $prompt = <<<PROMPT
You are a professional copywriter creating directory listings for an Indian community website in Canada (GoBazaar).

Business Name: {$name}
Category: {$category}
Keywords / Notes: {$keywords}

{$langNote}

Generate the following and return as JSON:

1. "description": A professional, warm, and engaging business description for the GoBazaar directory. It should:
   - Be 3–4 short paragraphs
   - Mention the business name naturally in the first sentence
   - Highlight services, unique selling points, and community connection (Indian community in Canada)
   - Sound friendly and trustworthy — like a real local business, not a generic template
   - Be 180–250 words
   - Return as HTML (use <p> tags for paragraphs, <strong> for emphasis if appropriate)

2. "tagline": A short one-liner tagline/slogan (max 12 words)

3. "tags": An array of 8–12 relevant search keywords (lowercase, no duplicates) customers might use to find this business

Return ONLY valid JSON — no markdown, no code fences, no explanation. Exactly this format:
{"description":"<p>...</p><p>...</p>","tagline":"...","tags":["tag1","tag2"]}
PROMPT;

        try {
            $res = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'llama-3.1-8b-instant',
                    'temperature' => 0.7,
                    'messages'    => [
                        [
                            'role'    => 'system',
                            'content' => 'You are a professional business copywriter. Always respond with valid JSON only — no markdown, no code blocks, no explanation.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]);

            if (!$res->successful()) {
                Log::warning('Groq content gen failed: ' . $res->body());
                $errCode = $res->status();
                if ($errCode === 429) {
                    return response()->json(['error' => 'AI rate limit reached. Please wait a moment and try again.'], 429);
                }
                $errMsg = $res->json('error.message') ?? 'AI generation failed.';
                return response()->json(['error' => $errMsg], 502);
            }

            $text = trim($res->json('choices.0.message.content') ?? '');

            // Strip markdown code fences if model returns them anyway
            $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
            $text = preg_replace('/\s*```\s*$/i', '', trim($text));

            $parsed = json_decode($text, true);
            if (!$parsed || !isset($parsed['description'])) {
                Log::warning('Groq returned non-JSON: ' . $text);
                return response()->json(['error' => 'Unexpected AI response. Please try again.'], 502);
            }

            return response()->json([
                'description' => $parsed['description'],
                'tagline'     => $parsed['tagline'] ?? '',
                'tags'        => $parsed['tags'] ?? [],
            ]);

        } catch (\Throwable $e) {
            Log::error('BusinessContentController generate: ' . $e->getMessage());
            return response()->json(['error' => 'Generation failed: ' . $e->getMessage()], 500);
        }
    }
}
