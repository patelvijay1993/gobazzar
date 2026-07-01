<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify Your GoBazaar Email Address')
            ->greeting('Welcome to GoBazaar!')
            ->line('Thank you for registering. Please click the button below to verify your email address and activate your account.')
            ->action('Verify Email Address', $url)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not create a GoBazaar account, no further action is required.')
            ->salutation('— The GoBazaar Team');
    }
}
