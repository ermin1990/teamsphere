<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanUpgradeRequested extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public ?string $requestNote)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zahtjev za veći plan - MojTurnir',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.plan-upgrade-requested',
            with: [
                'requestingUser' => $this->user,
                'requestNote' => $this->requestNote,
            ],
        );
    }
}
