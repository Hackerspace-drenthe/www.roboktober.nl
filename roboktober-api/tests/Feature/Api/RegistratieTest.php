<?php

declare(strict_types=1);

use App\Enums\TeamStatus;
use App\Models\Team;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use App\Mail\NieuwTeamAanmelding;

describe('POST /api/v1/registratie', function (): void {
    beforeEach(fn () => Mail::fake());

    it('creates a team with pending status', function (): void {
        $response = $this->postJson('/api/v1/registratie', [
            'naam' => 'Team Robotica',
            'contactpersoon' => 'Jan Jansen',
            'email' => 'jan@example.com',
            'volwassenen' => 2,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.naam', 'Team Robotica')
            ->assertJsonPath('data.status', TeamStatus::Pending->value);

        $this->assertDatabaseHas('teams', [
            'naam' => 'Team Robotica',
            'status' => TeamStatus::Pending->value,
        ]);
    });

    it('sends email notification after registration', function (): void {
        $this->postJson('/api/v1/registratie', [
            'naam' => 'Email Test Team',
            'contactpersoon' => 'Piet',
            'email' => 'piet@example.com',
            'volwassenen' => 1,
        ]);

        Mail::assertSent(NieuwTeamAanmelding::class);
    });

    it('accepts optional opmerkingen', function (): void {
        $response = $this->postJson('/api/v1/registratie', [
            'naam' => 'Team Met Opmerking',
            'contactpersoon' => 'Klaas',
            'email' => 'klaas@example.com',
            'volwassenen' => 1,
            'opmerkingen' => 'Wij zijn beginners.',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('teams', ['opmerkingen' => 'Wij zijn beginners.']);
    });

    it('rejects missing required fields', function (): void {
        $this->postJson('/api/v1/registratie', [])->assertUnprocessable();
    });

    it('rejects invalid email', function (): void {
        $this->postJson('/api/v1/registratie', [
            'naam' => 'Team',
            'contactpersoon' => 'Naam',
            'email' => 'geen-email',
            'volwassenen' => 1,
        ])->assertUnprocessable();
    });

    it('rejects zero adults', function (): void {
        $this->postJson('/api/v1/registratie', [
            'naam' => 'Team',
            'contactpersoon' => 'Naam',
            'email' => 'test@example.com',
            'volwassenen' => 0,
        ])->assertUnprocessable();
    });

    it('does not expose email in response', function (): void {
        $response = $this->postJson('/api/v1/registratie', [
            'naam' => 'Team Privacy',
            'contactpersoon' => 'Anna',
            'email' => 'anna@example.com',
            'volwassenen' => 1,
        ]);

        expect($response->getContent())->not->toContain('anna@example.com');
    });

    it('applies rate limiting to registration requests', function (): void {
        RateLimiter::clear('registratie-ip:127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/registratie', [
                'naam' => 'Throttle Team '.$i,
                'contactpersoon' => 'Contact '.$i,
                'email' => 'throttle-'.$i.'@example.com',
                'volwassenen' => 1,
            ])->assertCreated();
        }

        $this->postJson('/api/v1/registratie', [
            'naam' => 'Throttle Team 6',
            'contactpersoon' => 'Contact 6',
            'email' => 'throttle-6@example.com',
            'volwassenen' => 1,
        ])->assertStatus(429);
    });

    it('adds security headers to api responses', function (): void {
        $response = $this->postJson('/api/v1/registratie', [
            'naam' => 'Header Team',
            'contactpersoon' => 'Header Contact',
            'email' => 'header@example.com',
            'volwassenen' => 1,
        ]);

        $response->assertCreated()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader(
                'Content-Security-Policy',
                "default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'"
            );
    });
});
