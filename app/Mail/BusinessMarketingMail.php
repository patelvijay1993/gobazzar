<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessMarketingMail extends Mailable
{
    use SerializesModels;

    public string $subject_line;
    public string $body;
    public string $business_name;
    public string $site_name;

    public function __construct(string $subject, string $body, string $businessName)
    {
        $this->subject_line  = $subject;
        $this->body          = $body;
        $this->business_name = $businessName;
        $this->site_name     = config('app.name', 'GoBazaar');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subject_line);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.business-marketing');
    }
}
