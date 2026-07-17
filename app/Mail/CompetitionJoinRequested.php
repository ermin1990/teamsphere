<?php

namespace App\Mail;

use App\Models\CompetitionJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompetitionJoinRequested extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CompetitionJoinRequest $joinRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova prijava za ' . $this->joinRequest->competition->name . ' - MojTurnir',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.competition-join-requested',
            with: [
                'joinRequest' => $this->joinRequest,
                'manageUrl' => route('organizations.competitions.manage-players', [
                    $this->joinRequest->competition->organization,
                    $this->joinRequest->competition,
                ]),
            ],
        );
    }
}
