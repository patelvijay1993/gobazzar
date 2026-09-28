<?php

namespace App\Filament\Pages;

use App\Mail\BusinessClaimMail;
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
    public string $filter_province = '';
    public string $filter_city     = '';
    public string $filter_category = '';

    // Message
    public string $subject = '';
    public string $message = '';

    // Which claim-email design to send — see resources/views/emails/business-claim-*.blade.php
    public string $claim_template = 'gradient';

    // Per-business send type: ['biz_id' => 'email'|'whatsapp'|'none']
    public array $send_types = [];

    // Selected business IDs
    public array $selected = [];

    // Results
    public ?array $businesses = null;
    public ?array $send_log   = null;

    // When province changes, reset city
    public function updatedFilterProvince(): void
    {
        $this->filter_city = '';
        $this->businesses  = null;
        $this->selected    = [];
    }

    public function search(): void
    {
        $query = Business::with(['category', 'user'])
            ->where('status', 'active');

        if ($this->filter_province) {
            $query->where('province', $this->filter_province);
        }
        if ($this->filter_city) {
            $query->where('city', 'like', '%' . $this->filter_city . '%');
        }
        if ($this->filter_category) {
            $query->where('category_id', $this->filter_category);
        }

        $this->businesses = $query->orderBy('name')
            ->get()
            ->map(fn($b) => [
                'id'          => $b->id,
                'name'        => $b->name,
                'city'        => $b->city,
                'province'    => $b->province,
                'category'    => $b->category?->name ?? '—',
                'email'       => $b->email,
                'phone'       => $b->phone,
                'is_claimed'  => $b->is_claimed,
            ])
            ->toArray();

        // Default: email if has email, whatsapp if only phone, none if neither
        $this->send_types = [];
        $this->selected   = [];
        foreach ($this->businesses as $b) {
            if ($b['email']) {
                $this->send_types[$b['id']] = 'email';
                $this->selected[]           = $b['id'];
            } elseif ($b['phone']) {
                $this->send_types[$b['id']] = 'whatsapp';
                $this->selected[]           = $b['id'];
            } else {
                $this->send_types[$b['id']] = 'none';
            }
        }

        $this->send_log = null;
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

        $hasNonClaimSend = collect($this->selected)
            ->contains(fn($id) => ($this->send_types[$id] ?? 'none') !== 'claim' && ($this->send_types[$id] ?? 'none') !== 'none');

        if ($hasNonClaimSend && empty(trim($this->message))) {
            Notification::make()->title('Message is empty.')->warning()->send();
            return;
        }

        $log        = [];
        $businesses = collect($this->businesses ?? [])->whereIn('id', $this->selected);

        foreach ($businesses as $biz) {
            $type = $this->send_types[$biz['id']] ?? 'none';

            if ($type === 'none') {
                $log[] = ['name' => $biz['name'], 'status' => 'skipped', 'reason' => 'No contact method selected'];
                continue;
            }

            if ($type === 'claim') {
                if (empty($biz['email'])) {
                    $log[] = ['name' => $biz['name'], 'status' => 'skipped', 'reason' => 'No email'];
                    continue;
                }
                if ($biz['is_claimed'] ?? false) {
                    $log[] = ['name' => $biz['name'], 'status' => 'skipped', 'reason' => 'Already claimed'];
                    continue;
                }
                try {
                    $business = Business::find($biz['id']);
                    Mail::to($biz['email'])->send(new BusinessClaimMail($business, $this->claim_template));
                    $business->update(['claim_email_sent_at' => now()]);
                    $log[] = ['name' => $biz['name'], 'status' => 'sent', 'contact' => $biz['email']];
                } catch (\Exception $e) {
                    $log[] = ['name' => $biz['name'], 'status' => 'failed', 'reason' => $e->getMessage()];
                }
            } elseif ($type === 'email') {
                if (empty($biz['email'])) {
                    $log[] = ['name' => $biz['name'], 'status' => 'skipped', 'reason' => 'No email'];
                    continue;
                }
                try {
                    Mail::to($biz['email'])->send(
                        new BusinessMarketingMail(
                            $this->subject ?: 'Message from GoBazaar',
                            $this->message,
                            $biz['name']
                        )
                    );
                    $log[] = ['name' => $biz['name'], 'status' => 'sent', 'contact' => $biz['email']];
                } catch (\Exception $e) {
                    $log[] = ['name' => $biz['name'], 'status' => 'failed', 'reason' => $e->getMessage()];
                }
            } elseif ($type === 'whatsapp') {
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

        Notification::make()
            ->title("✅ Email sent: $sent | 💬 WhatsApp: $wa | ⚠️ Skipped: $skipped")
            ->success()->send();
    }

    /** Send the selected claim-email design to the logged-in admin, using the first selected business as sample data. */
    public function previewClaimEmail(): void
    {
        $adminEmail = \Illuminate\Support\Facades\Auth::user()?->email;
        if (!$adminEmail) {
            Notification::make()->title('Could not determine your email address.')->danger()->send();
            return;
        }

        $sampleId = collect($this->selected)->first() ?? collect($this->businesses ?? [])->first()['id'] ?? null;
        if (!$sampleId) {
            Notification::make()->title('Search and select at least one business first.')->warning()->send();
            return;
        }

        $business = Business::find($sampleId);
        if (!$business) {
            Notification::make()->title('Business not found.')->danger()->send();
            return;
        }

        try {
            Mail::to($adminEmail)->send(new BusinessClaimMail($business, $this->claim_template));
            Notification::make()
                ->title("📧 Preview sent to {$adminEmail}")
                ->body('Template: '.ucfirst($this->claim_template).' · Sample business: '.$business->name)
                ->success()->send();
        } catch (\Exception $e) {
            Notification::make()->title('Failed to send preview.')->body($e->getMessage())->danger()->send();
        }
    }

    public function getProvincesProperty(): array
    {
        return Business::where('status', 'active')
            ->whereNotNull('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province', 'province')
            ->toArray();
    }

    public function getCitiesProperty(): array
    {
        $query = Business::where('status', 'active')->whereNotNull('city');
        if ($this->filter_province) {
            $query->where('province', $this->filter_province);
        }
        return $query->distinct()->orderBy('city')->pluck('city', 'city')->toArray();
    }

    public function getCategoriesProperty(): array
    {
        return Category::orderBy('name')->pluck('name', 'id')->toArray();
    }
}
