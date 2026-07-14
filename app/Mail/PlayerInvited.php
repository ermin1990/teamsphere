<?php

namespace App\Mail;

use App\Models\PlayerInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlayerInvited extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PlayerInvitation $invitation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pozivnica za ligu "' . $this->invitation->competition->name . '" - MojTurnir',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.player-invited',
            with: [
                'invitation' => $this->invitation,
                'acceptUrl' => route('player-invitations.accept', $this->invitation->token),
            ],
        );
    }
}
