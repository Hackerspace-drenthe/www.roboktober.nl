<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\RobotStatus;
use App\Enums\TeamStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTeamRequest;
use App\Http\Resources\Api\V1\TeamResource;
use App\Mail\NieuwTeamAanmelding;
use App\Models\Media;
use App\Models\Robot;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for public team registration submissions.
 *
 * Routes:
 * - POST /api/v1/registratie → RegistratieController@store
 *
 * @see PLAN.md §5.1 — registration always open
 */
class RegistratieController extends Controller
{
    /**
     * Accepts a new team registration.
     *
     * New registrations always start with status 'pending' — an organizer must approve.
     * Sends email notification to organizer after successful registration.
     * OWASP A03: Input is validated via StoreTeamRequest before processing.
     */
    public function store(StoreTeamRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        /** @var list<array{naam: string, gewichtsklasse: string, beschrijving?: string|null}> $robots */
        $robots = $validated['robots'];
        unset($validated['robots'], $validated['teamfoto']);

        /** @var Team $team */
        $team = Team::query()->create([...$validated, 'status' => TeamStatus::Pending]);

        foreach ($robots as $robotData) {
            Robot::query()->create([
                'team_id' => $team->id,
                'naam' => $robotData['naam'],
                'gewichtsklasse' => $robotData['gewichtsklasse'],
                'beschrijving' => $robotData['beschrijving'] ?? null,
                'status' => RobotStatus::InOntwikkeling,
            ]);
        }

        if ($request->hasFile('teamfoto')) {
            $this->koppelTeamFoto($team, $request->file('teamfoto'));
        }

        $team->load(['media', 'robots.media']);

        // Notify organizers — uses MAIL_MAILER=log in local dev (no real email sent)
        Mail::to(config('mail.from.address'))->send(new NieuwTeamAanmelding($team));

        return (new TeamResource($team))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    private function koppelTeamFoto(Team $team, UploadedFile $foto): void
    {
        $pad = $foto->store('team-fotos', 'public');
        $realPath = $foto->getRealPath();
        $hash = is_string($realPath) ? hash_file('sha256', $realPath) : null;

        $media = Media::query()->create([
            'naam' => 'Teamfoto '.$team->naam,
            'bestandsnaam' => $foto->getClientOriginalName(),
            'pad' => $pad,
            'disk' => 'public',
            'mime_type' => $foto->getMimeType() ?? 'application/octet-stream',
            'extensie' => strtolower($foto->getClientOriginalExtension()),
            'grootte' => $foto->getSize() ?? 0,
            'hash' => $hash,
            'meta' => [
                'bron' => 'team_registratie',
                'orig_name' => $foto->getClientOriginalName(),
            ],
            'versie' => '1.0.0',
            'downloads' => 0,
        ]);

        $team->koppelMedia($media, 'foto', [
            'alt_tekst' => 'Teamfoto van '.$team->naam,
            'onderschrift' => 'Ingestuurd via teamregistratie',
            'volgorde' => 0,
            'meta' => ['uuid' => (string) Str::uuid()],
        ]);
    }
}

