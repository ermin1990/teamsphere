<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Bosnian-language version of Laravel's default email verification email.
 */
class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Potvrdite email adresu - ' . config('app.name'))
            ->greeting('Zdravo!')
            ->line('Kliknite na dugme ispod da potvrdite svoju email adresu.')
            ->action('Potvrdi email adresu', $url)
            ->line('Ako niste kreirali nalog, nije potrebna nikakva dodatna radnja.')
            ->salutation('Pozdrav, ekipa ' . config('app.name'));
    }
}
