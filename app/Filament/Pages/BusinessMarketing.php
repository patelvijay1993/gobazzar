<?php

namespace App\Filament\Pages;

use App\Mail\BusinessMarketingMail;
use App\Models\Business;
use App\Models\Category;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;

class BusinessMarketing extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Business Marketing';
    protected static ?string $navigationGroup = 'Advertising';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.business-marketing';

    // Filters
    public string $filter_city     = '';
    public string $filter_category = '';
    public string $filter_type     = 'email'; // email | whatsapp

    // Message
    public string $subject  = '';
    public string $message  = '';

    // Selected business IDs
    public array $selected = [];

    // Results
    public ?array $businesses = null;
    public ?array $send_log   = null;

    public function search(): void
    {
        $query = Business::with(['category', 'user'])
            ->where('status', 'active');

        if ($this->filter_city) {
            $query->where('city', 'like', '%' . $this->filter_city . '%');
        }

        if ($this->filter_category) {
            $query->where('category_id', $this->filter_category);
        }

        $this->businesses = $query->orderBy('name')
            ->get()
            ->map(fn($b) => [
                'id'       => $b->id,
                'name'     => $b->name,
                'city'     => $b->city,
                'category' => $b->category?->name ?? '—',
                'email'    => $b->email,
                'phone'    => $b->phone,
            ])
            ->toArray();

        $this->selected  = array_column($this->businesses, 'id');
        $this->send_log  = null;
    }

    public function selectAll(): void
    {
        if ($this->businesses) {
            $this->selected = array_column($this->businesses, 'id');
        }
    }

    public function deselectAll(): void
    {
        $this->selected = [];
    }

    public function send(): void
    {
        if (empty($this->selected)) {
            Notification::make()->title('No businesses selected.')->warning()->send();
            return;
        }
        if (empty(trim($this->message))) {
            Notification::make()->title('Message is empty.')->warning()->send();
            return;
        }

        $log        = [];
        $businesses = collect($this->businesses ?? [])->whereIn('id', $this->selected);

        foreach ($businesses as $biz) {
            if ($this->filter_type === 'email') {
                if (empty($biz['email'])) {
                    $log[] = ['name' => $biz['name'], 'status' => 'skipped', 'reason' => 'No email'];
                    continue;
                }
                try {
                    Mail::to($biz['email'])->send(
                        new BusinessMarketingMail($this->subject ?: 'Message from GoBazaar', $this->message, $biz['name'])
                    );
                    $log[] = ['name' => $biz['name'], 'status' => 'sent', 'contact' => $biz['email']];
                } catch (\Exception $e) {
                    $log[] = ['name' => $biz['name'], 'status' => 'failed', 'reason' => $e->getMessage()];
                }
            } else {
                // WhatsApp — generate wa.me link
                $phone = preg_replace('/[^0-9]/', '', $biz['phone'] ?? '');
                if (empty($phone)) {
                    $log[] = ['name' => $biz['name'], 'status' => 'skipped', 'reason' => 'No phone'];
                    continue;
                }
                $text  = urlencode("Hello {$biz['name']},\n\n{$this->message}\n\n— GoBazaar Team");
                $link  = "https://wa.me/{$phone}?text={$text}";
                $log[] = ['name' => $biz['name'], 'status' => 'whatsapp', 'link' => $link, 'contact' => $biz['phone']];
            }
        }

        $this->send_log = $log;

        $sent    = count(array_filter($log, fn($l) => $l['status'] === 'sent'));
        $wa      = count(array_filter($log, fn($l) => $l['status'] === 'whatsapp'));
        $skipped = count(array_filter($log, fn($l) => in_array($l['status'], ['skipped', 'failed'])));

        if ($this->filter_type === 'email') {
            Notification::make()
                ->title("Done! Sent: $sent | Skipped: $skipped")
                ->success()->send();
        } else {
            Notification::make()
                ->title("$wa WhatsApp links generated below. Click each to open WhatsApp.")
                ->success()->send();
        }
    }

    public function getCategoriesProperty(): array
    {
        return Category::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getCitiesProperty(): array
    {
        return Business::where('status', 'active')
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->toArray();
    }
}
