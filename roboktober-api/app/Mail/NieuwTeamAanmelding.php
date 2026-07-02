<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email notification sent to organizers when a new team registers.
 *
 * Sent to the configured MAIL_FROM_ADDRESS (organizer inbox).
 * Contains team details for quick review in the Filament admin.
 */
class NieuwTeamAanmelding extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Team $team,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nieuwe teamaanmelding: {$this->team->naam}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nieuw-team-aanmelding',
        );
    }
}
