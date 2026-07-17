<?php

namespace App\Mail;

use App\Models\CompetitionJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompetitionJoinRequestDecided extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CompetitionJoinRequest $joinRequest)
    {
    }

    public function envelope(): Envelope
    {
        $approved = $this->joinRequest->status === 'approved';

        return new Envelope(
            subject: ($approved ? 'Prijava prihvaćena' : 'Prijava odbijena') . ' - ' . $this->joinRequest->competition->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.competition-join-request-decided',
            with: [
                'joinRequest' => $this->joinRequest,
                'approved' => $this->joinRequest->status === 'approved',
                'competitionUrl' => route('player.leagues.show', $this->joinRequest->competition),
            ],
        );
    }
}
