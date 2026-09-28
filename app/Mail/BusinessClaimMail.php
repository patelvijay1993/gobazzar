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

    public string $business_name;
    public string $site_name;
    public string $claim_url;

    public function __construct(Business $business)
    {
        $this->business_name = $business->name;
        $this->site_name     = config('app.name', 'GoBazaar');
        $this->claim_url     = URL::temporarySignedRoute(
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
        return new Content(view: 'emails.business-claim');
    }
}
