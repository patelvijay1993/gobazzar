<?php

namespace App\Mail;

use App\Models\Business;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class BusinessClaimMail extends Mailable
{
    use SerializesModels;

    public string  $business_name;
    public string  $site_name;
    public string  $claim_url;
    public ?string $business_image;
    public ?string $business_city;
    public ?string $business_category;
    public string  $template;

    /**
     * @param string $template Which email design to render — one of:
     *   'gradient' (default, refined version of the original), 'minimal',
     *   'bold', or 'split' (uses the business photo). See resources/views/emails/business-claim-*.blade.php.
     */
    public function __construct(Business $business, string $template = 'gradient')
    {
        $this->business_name     = $business->name;
        $this->site_name         = config('app.name', 'GoBazaar');
        $this->business_image    = $business->image_url;
        $this->business_city     = $business->location ?: null;
        $this->business_category = $business->category?->name;
        $this->template          = $template;
        $this->claim_url         = URL::temporarySignedRoute(
            'business.claim',
            now()->addDays(14),
            ['business' => $business->id]
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Claim & Verify Your Business on {$this->site_name}");
    }

    public function content(): Content
    {
        return new Content(view: "emails.business-claim-{$this->template}");
    }
}
