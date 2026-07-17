<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Bosnian-language version of Laravel's default password reset email.
 */
class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Resetovanje lozinke - ' . config('app.name'))
            ->greeting('Zdravo!')
            ->line('Primili ste ovaj email jer smo zaprimili zahtjev za resetovanje lozinke za vaš nalog.')
            ->action('Resetuj lozinku', $url)
            ->line('Ovaj link za resetovanje lozinke ističe za ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' minuta.')
            ->line('Ako niste zatražili resetovanje lozinke, nije potrebna nikakva dodatna radnja.')
            ->salutation('Pozdrav, ekipa ' . config('app.name'));
    }
}
