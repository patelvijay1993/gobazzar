<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\Job;
use App\Models\Event;
use App\Models\Business;

class AssistantController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);
        $message = trim($request->input('message'));

        // Try Groq API keys in rotation
        $parsed = $this->parseWithGroq($message);

        // Fallback to rule-based if all keys fail
        if (!$parsed) {
            $parsed = $this->parseRuleBased($message);
        }

        $results = $this->searchDB($parsed);

        return response()->json([
            'parsed'  => $parsed,
            'results' => $results,
            'count'   => count($results),
        ]);
    }

    // ── Groq API with key rotation ────────────────────────────────────
    private function parseWithGroq(string $message): ?array
    {
        $keys = config('services.groq.keys', []);
        if (empty($keys)) return null;

        $prompt = <<<PROMPT
You are a search assistant for GoBazaar, a Canadian community marketplace.
Extract search intent from the user's message and return ONLY valid JSON.

Categories available: listings, jobs, events, businesses, blog

JSON format:
{
  "category": "listings|jobs|events|businesses|blog|all",
  "keywords": "search keywords",
  "city": "city name or null",
  "province": "province name or null",
  "min_price": number or null,
  "max_price": number or null
}

User message: "$message"

Return ONLY the JSON object, nothing else.
PROMPT;

        foreach ($keys as $key) {
            try {
                $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_TIMEOUT        => 8,
                    CURLOPT_HTTPHEADER     => [
                        'Authorization: Bearer ' . $key,
                        'Content-Type: application/json',
                    ],
                    CURLOPT_POSTFIELDS => json_encode([
                        'model'       => 'llama-3.1-8b-instant',
                        'messages'    => [['role' => 'user', 'content' => $prompt]],
                        'temperature' => 0.1,
                        'max_tokens'  => 150,
                    ]),
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 429) continue; // rate limit — try next key

                if ($httpCode === 200) {
                    $data = json_decode($response, true);
                    $content = $data['choices'][0]['message']['content'] ?? '';
                    // Extract JSON from response
                    preg_match('/\{.*\}/s', $content, $matches);
                    if ($matches) {
                        $parsed = json_decode($matches[0], true);
                        if (is_array($parsed)) return $parsed;
                    }
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    // ── Rule-based fallback ───────────────────────────────────────────
    private function parseRuleBased(string $message): array
    {
        $msg = strtolower($message);

        // Category detection
        $category = 'all';
        if (preg_match('/\b(room|rent|apartment|condo|house|basement|studio|roommate|lease)\b/', $msg)) $category = 'listings';
        elseif (preg_match('/\b(job|work|career|hiring|position|salary|employment|developer|engineer|nurse|driver)\b/', $msg)) $category = 'jobs';
        elseif (preg_match('/\b(events?|festival|concert|party|meetup|show|wedding|gathering)\b/', $msg)) $category = 'events';
        elseif (preg_match('/\b(business|restaurant|store|shop|salon|service|company)\b/', $msg)) $category = 'businesses';
        elseif (preg_match('/\b(blog|news|article|post|read)\b/', $msg)) $category = 'blog';

        // Canadian cities
        $cities = ['calgary','toronto','vancouver','edmonton','montreal','ottawa','winnipeg',
                   'mississauga','brampton','surrey','hamilton','quebec','victoria','london',
                   'halifax','saskatoon','regina','kelowna','abbotsford','barrie'];
        $city = null;
        foreach ($cities as $c) {
            if (str_contains($msg, $c)) { $city = ucfirst($c); break; }
        }

        // Provinces
        $provinces = ['ontario','alberta','british columbia','quebec','manitoba','saskatchewan',
                      'nova scotia','new brunswick','bc'];
        $province = null;
        foreach ($provinces as $p) {
            if (str_contains($msg, $p)) {
                $province = $p === 'bc' ? 'British Columbia' : ucwords($p);
                break;
            }
        }

        // Price extraction
        $maxPrice = null; $minPrice = null;
        if (preg_match('/under\s*\$?([\d,]+)/i', $message, $m)) $maxPrice = (int)str_replace(',','',$m[1]);
        elseif (preg_match('/below\s*\$?([\d,]+)/i', $message, $m)) $maxPrice = (int)str_replace(',','',$m[1]);
        elseif (preg_match('/less\s+than\s*\$?([\d,]+)/i', $message, $m)) $maxPrice = (int)str_replace(',','',$m[1]);
        elseif (preg_match('/max(?:imum)?\s*\$?([\d,]+)/i', $message, $m)) $maxPrice = (int)str_replace(',','',$m[1]);
        elseif (preg_match('/\$?([\d,]+)\s*(?:or less|max|maximum)/i', $message, $m)) $maxPrice = (int)str_replace(',','',$m[1]);
        if (preg_match('/(?:above|over|min(?:imum)?|more than|at least)\s*\$?([\d,]+)/i', $message, $m)) $minPrice = (int)str_replace(',','',$m[1]);

        // Keywords — remove stop words
        $stopWords = ['i','want','need','find','looking','for','a','an','the','in','at','on',
                      'under','below','above','over','with','and','or','is','are','can','you',
                      'please','show','me','get','some','any','good','best','near','around'];
        $words = preg_split('/\s+/', $msg);
        $keywords = implode(' ', array_filter($words, fn($w) => strlen($w) > 2 && !in_array($w, $stopWords)));

        return [
            'category'  => $category,
            'keywords'  => $keywords,
            'city'      => $city,
            'province'  => $province,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
        ];
    }

    // ── DB Search ─────────────────────────────────────────────────────
    private function searchDB(array $p): array
    {
        $results = [];
        $category = $p['category'] ?? 'all';
        $keywords = $p['keywords'] ?? '';
        $city     = $p['city'] ?? null;
        $province = $p['province'] ?? null;
        $maxPrice = $p['max_price'] ?? null;
        $minPrice = $p['min_price'] ?? null;

        $searchListings = in_array($category, ['listings','all']);
        $searchJobs     = in_array($category, ['jobs','all']);
        $searchEvents   = in_array($category, ['events','all']);
        $searchBiz      = in_array($category, ['businesses','all']);

        // Listings
        if ($searchListings) {
            $q = Listing::query()->where('status','active');
            if ($keywords) $q->where(fn($q) => $q->where('title','like',"%$keywords%")->orWhere('description','like',"%$keywords%"));
            if ($city)     $q->where('city','like',"%$city%");
            if ($province) $q->where('province','like',"%$province%");
            if ($maxPrice) $q->where('price','<=',$maxPrice);
            if ($minPrice) $q->where('price','>=',$minPrice);
            $q->latest()->limit(5)->get()->each(function($item) use (&$results) {
                $images = is_array($item->images) ? $item->images : json_decode($item->images ?? '[]', true);
                $results[] = [
                    'type'     => 'listing',
                    'title'    => $item->title,
                    'price'    => $item->price ? '$'.number_format($item->price) : null,
                    'location' => trim(($item->city ?? '').' '.($item->province ?? '')),
                    'url'      => route('classifieds.show', $item->slug ?? $item->id),
                    'image'    => $images[0] ?? null,
                ];
            });
        }

        // Jobs
        if ($searchJobs) {
            $q = Job::query()->where('status','active');
            if ($keywords) $q->where(fn($q) => $q->where('title','like',"%$keywords%")->orWhere('description','like',"%$keywords%"));
            if ($city)     $q->where('city','like',"%$city%");
            if ($province) $q->where('province','like',"%$province%");
            $q->latest()->limit(5)->get()->each(function($item) use (&$results) {
                $results[] = [
                    'type'     => 'job',
                    'title'    => $item->title,
                    'price'    => $item->salary ?? null,
                    'location' => trim(($item->city ?? '').' '.($item->province ?? '')),
                    'url'      => route('jobs.show', $item->slug ?? $item->id),
                    'image'    => null,
                ];
            });
        }

        // Events
        if ($searchEvents) {
            $q = Event::query()->where('status','active')->where('start_date','>=',now());
            if ($keywords) $q->where(fn($q) => $q->where('title','like',"%$keywords%")->orWhere('description','like',"%$keywords%"));
            if ($city)     $q->where('city','like',"%$city%");
            if ($province) $q->where('province','like',"%$province%");
            $q->latest()->limit(5)->get()->each(function($item) use (&$results) {
                $results[] = [
                    'type'     => 'event',
                    'title'    => $item->title,
                    'price'    => $item->price ? '$'.number_format($item->price) : 'Free',
                    'location' => trim(($item->city ?? '').' '.($item->province ?? '')),
                    'url'      => route('events.show', $item->slug ?? $item->id),
                    'image'    => $item->image ?? null,
                ];
            });
        }

        // Businesses
        if ($searchBiz) {
            $q = Business::query()->where('status','active');
            if ($keywords) $q->where(fn($q) => $q->where('name','like',"%$keywords%")->orWhere('description','like',"%$keywords%"));
            if ($city)     $q->where('city','like',"%$city%");
            if ($province) $q->where('province','like',"%$province%");
            $q->latest()->limit(5)->get()->each(function($item) use (&$results) {
                $results[] = [
                    'type'     => 'business',
                    'title'    => $item->name,
                    'price'    => null,
                    'location' => trim(($item->city ?? '').' '.($item->province ?? '')),
                    'url'      => route('directory.show', $item->slug ?? $item->id),
                    'image'    => $item->logo ?? null,
                ];
            });
        }

        return array_slice($results, 0, 10);
    }
}
