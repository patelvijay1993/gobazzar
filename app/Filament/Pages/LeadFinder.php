<?php

namespace App\Filament\Pages;

use App\Mail\BusinessMarketingMail;
use App\Models\Lead;
use App\Services\GooglePlacesService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;

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
    public bool   $fetch_details   = false; // fetch phone/website per place (slower, uses more API quota)

    public string $msg_subject = '';
    public string $msg_body    = '';
    public string $send_type   = 'email'; // email | whatsapp

    public ?array $results   = null;
    public array  $selected  = [];
    public ?array $send_log  = null;
    public string $api_status = '';

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

    public function getLeadsCountProperty(): int
    {
        return Lead::count();
    }

    public function getNewLeadsCountProperty(): int
    {
        return Lead::where('status', 'new')->count();
    }
}
