<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamBewerkLink extends Mailable
{
    use Queueable;
    use SerializesModels;

    public readonly string $bewerkUrl;

    public function __construct(
        public readonly Team $team,
        string $token,
    ) {
        $this->bewerkUrl = $this->buildBewerkUrl($token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Bewerk je teamaanmelding: {$this->team->naam}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team-bewerk-link',
        );
    }

    private function buildBewerkUrl(string $token): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        return $baseUrl.'/app/aanmelding/bewerken/'.$token;
    }
}
