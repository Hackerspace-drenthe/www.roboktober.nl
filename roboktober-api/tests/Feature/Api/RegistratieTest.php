<?php

declare(strict_types=1);

use App\Enums\TeamStatus;
use App\Models\Team;
use Illuminate\Support\Facades\Mail;
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
});
