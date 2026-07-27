<?php

namespace App\Filament\Pages;

use App\Mail\BusinessMarketingMail;
use App\Models\Business;
use App\Models\Category;
use App\Models\Lead;
use App\Services\GooglePlacesService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeadFinder extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Lead Finder (Google Maps)';
    protected static ?string $navigationGroup = 'Advertising';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.pages.lead-finder';

    public string $search_keyword  = '';
    public string $search_city     = '';
    public string $search_province = '';
    public bool   $fetch_details   = false;

    public string $msg_subject = '';
    public string $msg_body    = '';
    public string $send_type   = 'email';

    public ?array $results    = null;
    public array  $selected   = [];
    public ?array $send_log   = null;
    public string $api_status = '';

    // Add to Directory
    public bool   $show_dir_modal  = false;
    public int    $dir_category_id = 0;
    public ?array $dir_import_log  = null;

    public function search(): void
    {
        $this->results  = null;
        $this->selected = [];
        $this->send_log = null;

        if (!config('services.google_places.key')) {
            $this->api_status = 'no_key';
            return;
        }

        if (!trim($this->search_keyword) || !trim($this->search_city)) {
            Notification::make()->title('Please enter keyword and city.')->warning()->send();
            return;
        }

        $service = new GooglePlacesService();
        $places  = $service->searchBusinesses(
            trim($this->search_keyword),
            trim($this->search_city),
            trim($this->search_province)
        );

        if (empty($places)) {
            $this->results    = [];
            $this->api_status = 'no_results';
            return;
        }

        // If fetch_details ON — get phone/website for each (costs extra API calls)
        if ($this->fetch_details) {
            foreach ($places as &$place) {
                if (!empty($place['google_place_id'])) {
                    $details = $service->getPlaceDetails($place['google_place_id']);
                    $place   = array_merge($place, $details);
                }
            }
        }

        $this->results    = $places;
        $this->selected   = array_column($places, 'google_place_id');
        $this->api_status = 'ok';
    }

    public function saveLeads(): void
    {
        if (empty($this->results) || empty($this->selected)) {
            Notification::make()->title('No results selected.')->warning()->send();
            return;
        }

        $saved    = 0;
        $skipped  = 0;

        foreach ($this->results as $place) {
            if (!in_array($place['google_place_id'], $this->selected)) continue;
            if (!$place['google_place_id']) continue;

            $existing = Lead::where('google_place_id', $place['google_place_id'])->first();
            if ($existing) { $skipped++; continue; }

            Lead::create([
                'name'            => $place['name'],
                'category'        => $place['category'] ?? $this->search_keyword,
                'city'            => $place['city'] ?? $this->search_city,
                'province'        => $place['province'] ?? $this->search_province,
                'address'         => $place['address'] ?? null,
                'phone'           => $place['phone'] ?? null,
                'email'           => $place['email'] ?? null,
                'website'         => $place['website'] ?? null,
                'rating'          => $place['rating'] ?? null,
                'review_count'    => $place['review_count'] ?? null,
                'google_place_id' => $place['google_place_id'],
                'google_maps_url' => $place['google_maps_url'] ?? null,
                'status'          => 'new',
                'source'          => 'google_maps',
            ]);
            $saved++;
        }

        Notification::make()
            ->title("Saved: $saved leads | Already existed: $skipped")
            ->success()->send();
    }

    public function selectAll(): void
    {
        if ($this->results) {
            $this->selected = array_column($this->results, 'google_place_id');
        }
    }

    public function deselectAll(): void
    {
        $this->selected = [];
    }

    public function sendMarketing(): void
    {
        if (empty($this->selected) || empty(trim($this->msg_body))) {
            Notification::make()->title('Select businesses and write a message.')->warning()->send();
            return;
        }

        $log = [];

        foreach ($this->results ?? [] as $place) {
            if (!in_array($place['google_place_id'], $this->selected)) continue;

            if ($this->send_type === 'email') {
                if (empty($place['email'])) {
                    $log[] = ['name' => $place['name'], 'status' => 'skipped', 'reason' => 'No email'];
                    continue;
                }
                try {
                    Mail::to($place['email'])->send(
                        new BusinessMarketingMail(
                            $this->msg_subject ?: 'Message from GoBazaar',
                            $this->msg_body,
                            $place['name']
                        )
                    );
                    // Update lead status if exists
                    Lead::where('google_place_id', $place['google_place_id'])
                        ->update(['status' => 'contacted', 'contact_method' => 'email', 'last_contacted_at' => now()]);

                    $log[] = ['name' => $place['name'], 'status' => 'sent', 'contact' => $place['email']];
                } catch (\Exception $e) {
                    $log[] = ['name' => $place['name'], 'status' => 'failed', 'reason' => $e->getMessage()];
                }
            } else {
                $phone = preg_replace('/[^0-9]/', '', $place['phone'] ?? '');
                if (empty($phone)) {
                    $log[] = ['name' => $place['name'], 'status' => 'skipped', 'reason' => 'No phone'];
                    continue;
                }
                $text = urlencode("Hello {$place['name']},\n\n{$this->msg_body}\n\n— GoBazaar Team");
                $link = "https://wa.me/{$phone}?text={$text}";

                Lead::where('google_place_id', $place['google_place_id'])
                    ->update(['status' => 'contacted', 'contact_method' => 'whatsapp', 'last_contacted_at' => now()]);

                $log[] = ['name' => $place['name'], 'status' => 'whatsapp', 'link' => $link, 'contact' => $place['phone']];
            }
        }

        $this->send_log = $log;

        $sent    = count(array_filter($log, fn($l) => $l['status'] === 'sent'));
        $wa      = count(array_filter($log, fn($l) => $l['status'] === 'whatsapp'));
        $skipped = count(array_filter($log, fn($l) => in_array($l['status'], ['skipped', 'failed'])));

        Notification::make()
            ->title($this->send_type === 'email' ? "Sent: $sent | Skipped: $skipped" : "$wa WhatsApp links ready")
            ->success()->send();
    }

    public function openDirModal(): void
    {
        if (empty($this->selected)) {
            Notification::make()->title('Select at least one business first.')->warning()->send();
            return;
        }
        $this->dir_import_log = null;
        $this->show_dir_modal = true;
    }

    public function closeDirModal(): void
    {
        $this->show_dir_modal = false;
    }

    public function addToDirectory(): void
    {
        if (empty($this->selected)) {
            Notification::make()->title('No businesses selected.')->warning()->send();
            return;
        }

        if (!$this->dir_category_id) {
            Notification::make()->title('Please select a category.')->warning()->send();
            return;
        }

        $service  = new GooglePlacesService();
        $disk     = config('filesystems.default', 'public');
        $adminId  = 1; // Admin user owns imported listings
        $log      = [];
        $created  = 0;
        $skipped  = 0;

        foreach ($this->results ?? [] as $place) {
            if (!in_array($place['google_place_id'], $this->selected)) continue;

            // Skip if already in directory
            $exists = Business::where('google_place_id', $place['google_place_id'])->exists();
            if ($exists) {
                $log[]   = ['name' => $place['name'], 'status' => 'skipped', 'reason' => 'Already in directory'];
                $skipped++;
                continue;
            }

            // Fetch full details (phone, website, photos)
            $details = $service->getPlaceFullDetails($place['google_place_id']);

            // Download up to 3 photos → store in S3/local
            $imagePaths = [];
            foreach ($details['photo_refs'] ?? [] as $ref) {
                $bytes = $service->downloadPhoto($ref, 1200);
                if ($bytes) {
                    $path = 'businesses/' . Str::uuid() . '.jpg';
                    Storage::disk($disk)->put($path, $bytes);
                    $imagePaths[] = $path;
                }
            }

            // AI-generate description via Groq
            $description = $this->generateDescription(
                $place['name'],
                $this->search_keyword,
                $details['address'] ?? $place['address'] ?? '',
                $place['city'] ?? $this->search_city
            );

            // Parse address parts
            $addressParts = $this->parseAddress($details['address'] ?? $place['address'] ?? '');

            $slug = $this->uniqueSlug($place['name']);

            Business::create([
                'user_id'         => $adminId,
                'category_id'     => $this->dir_category_id,
                'name'            => $place['name'],
                'slug'            => $slug,
                'description'     => $description,
                'address'         => $details['address'] ?? $place['address'] ?? null,
                'city'            => $addressParts['city'] ?: ($place['city'] ?? $this->search_city),
                'province'        => $addressParts['province'] ?: ($place['province'] ?? $this->search_province),
                'postal_code'     => $addressParts['postal'] ?: null,
                'phone'           => $details['phone'] ?? $place['phone'] ?? null,
                'email'           => null,
                'website'         => $details['website'] ?? $place['website'] ?? null,
                'map_url'         => $details['google_maps_url'] ?? $place['google_maps_url'] ?? null,
                'rating'          => $details['rating'] ?? $place['rating'] ?? null,
                'review_count'    => $details['review_count'] ?? $place['review_count'] ?? 0,
                'images'          => !empty($imagePaths) ? $imagePaths : null,
                'image'           => $imagePaths[0] ?? null,
                'status'          => 'active',
                'is_verified'     => false,
                'is_featured'     => false,
                'google_place_id' => $place['google_place_id'],
            ]);

            $log[] = [
                'name'   => $place['name'],
                'status' => 'added',
                'photos' => count($imagePaths),
                'slug'   => $slug,
            ];
            $created++;
        }

        $this->dir_import_log = $log;
        $this->show_dir_modal = false;

        Notification::make()
            ->title("Added to Directory: $created | Skipped: $skipped")
            ->success()->send();
    }

    private function generateDescription(string $name, string $category, string $address, string $city): string
    {
        $keys   = config('services.groq.keys', []);
        $apiKey = is_array($keys) ? (array_values(array_filter($keys))[0] ?? null) : $keys;

        if (!$apiKey) {
            return "$name is a $category business located in $city. Contact us for more information about our services.";
        }

        try {
            $prompt = "Write a 2-3 sentence professional business description for a directory listing. "
                . "Business: \"$name\", Type: $category, Location: $address. "
                . "Keep it factual, friendly, and suitable for a Canadian business directory. "
                . "Do not use asterisks, markdown, or bullet points. Plain text only.";

            $response = Http::withToken($apiKey)
                ->timeout(10)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'    => 'llama-3.1-8b-instant',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens' => 200,
                ]);

            return trim($response->json('choices.0.message.content') ?? '')
                ?: "$name is a $category business located in $city.";
        } catch (\Exception $e) {
            return "$name is a $category business located in $city.";
        }
    }

    private function parseAddress(string $address): array
    {
        $city     = '';
        $province = '';
        $postal   = '';

        // Canadian postal code pattern
        if (preg_match('/([A-Z]\d[A-Z]\s?\d[A-Z]\d)/i', $address, $m)) {
            $postal = strtoupper(str_replace(' ', '', $m[1]));
        }

        // Province abbreviations
        $provinces = ['AB','BC','MB','NB','NL','NS','NT','NU','ON','PE','QC','SK','YT'];
        foreach ($provinces as $prov) {
            if (preg_match('/\b' . $prov . '\b/i', $address)) {
                $province = $prov;
                break;
            }
        }

        // City: try to extract from "City, Province" pattern
        if (preg_match('/([A-Za-z\s]+),\s*(?:' . implode('|', $provinces) . ')/i', $address, $m)) {
            $city = trim($m[1]);
        }

        return ['city' => $city, 'province' => $province, 'postal' => $postal];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;
        while (Business::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function getDirectoryCategoriesProperty(): array
    {
        return Category::where('type', 'directory')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name', 'icon'])
            ->map(fn($c) => ['id' => $c->id, 'name' => ($c->icon ? $c->icon . ' ' : '') . $c->name])
            ->toArray();
    }

    public function getLeadsCountProperty(): int
    {
        return Lead::count();
    }

    public function getNewLeadsCountProperty(): int
    {
        return Lead::where('status', 'new')->count();
    }
}
