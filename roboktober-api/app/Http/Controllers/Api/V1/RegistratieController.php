<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\RobotStatus;
use App\Enums\TeamStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTeamRequest;
use App\Http\Resources\Api\V1\TeamResource;
use App\Mail\NieuwTeamAanmelding;
use App\Models\Robot;
use App\Models\Team;
use App\Models\User;
use App\Services\Uploads\TeamPhotoUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for authenticated team registration submissions.
 *
 * Routes:
 * - POST /api/v1/registratie → RegistratieController@store
 *
 * @see PLAN.md §5.1 — registration always open
 */
class RegistratieController extends Controller
{
    public function __construct(private readonly TeamPhotoUploadService $teamPhotoUploads)
    {
    }

    /**
    * Accepts a new team registration.
     *
     * New registrations always start with status 'pending' — an organizer must approve.
     * Sends email notification to organizer after successful registration.
     * OWASP A03: Input is validated via StoreTeamRequest before processing.
     */
    public function store(StoreTeamRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        /** @var list<array{naam: string, gewichtsklasse: string, beschrijving?: string|null}> $robots */
        $robots = $validated['robots'];
        unset($validated['robots'], $validated['teamfoto']);

        /** @var Team $team */
        $team = DB::transaction(function () use ($validated, $robots, $request, $user): Team {
            /** @var Team $team */
            $team = Team::query()->create([
                ...$validated,
                'email' => mb_strtolower($user->email),
                'status' => TeamStatus::Pending,
                'captain_user_id' => $user->id,
            ]);

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
                $this->teamPhotoUploads->attach(
                    team: $team,
                    photo: $request->file('teamfoto'),
                    source: 'team_registratie',
                    caption: 'Ingestuurd via teamregistratie',
                );
            }

            return $team;
        });

        $user->promoteToTeamCaptainIfVisitor();

        $team->load(['edition', 'media', 'robots.media']);

        // Notify organizers
        try {
            Mail::to(config('mail.from.address'))->send(new NieuwTeamAanmelding($team));
        } catch (\Throwable $exception) {
            Log::warning('Registratie mail kon niet worden verstuurd.', [
                'team_id' => $team->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return (new TeamResource($team))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}

