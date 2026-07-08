<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Enums\RobotStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateTeamRegistrationRequest;
use App\Http\Resources\Api\V1\EditableTeamRegistrationResource;
use App\Models\Robot;
use App\Models\Team;
use App\Services\Uploads\TeamPhotoUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class TeamRegistrationEditController extends Controller
{
    public function __construct(private readonly TeamPhotoUploadService $teamPhotoUploads)
    {
    }

    public function show(string $token): EditableTeamRegistrationResource
    {
        $team = $this->resolveTeamByToken($token);
        $team->load(['media', 'robots']);

        return new EditableTeamRegistrationResource($team);
    }

    public function update(UpdateTeamRegistrationRequest $request, string $token): JsonResponse
    {
        $team = $this->resolveTeamByToken($token);
        $this->authorizeTeamMutation($request, $team);

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        /** @var list<array{id?: int|string|null, naam: string, gewichtsklasse: string, beschrijving?: string|null}> $robots */
        $robots = $validated['robots'];
        $verwijderTeamfoto = (bool) ($validated['teamfoto_verwijderen'] ?? false);
        unset($validated['robots'], $validated['teamfoto'], $validated['teamfoto_verwijderen']);

        DB::transaction(function () use ($request, $team, $validated, $robots, $verwijderTeamfoto): void {
            $team->fill($validated);
            $team->save();

            $bestaandeRobots = $team->robots()->get()->keyBy('id');
            $bewerkteRobotIds = [];

            foreach ($robots as $robotData) {
                $robotId = isset($robotData['id']) ? (int) $robotData['id'] : null;

                if ($robotId !== null) {
                    /** @var Robot|null $bestaandeRobot */
                    $bestaandeRobot = $bestaandeRobots->get($robotId);

                    if ($bestaandeRobot === null) {
                        throw ValidationException::withMessages([
                            'robots' => ['Een robot in de aanvraag hoort niet bij dit team.'],
                        ]);
                    }

                    $bestaandeRobot->fill([
                        'naam' => $robotData['naam'],
                        'gewichtsklasse' => $robotData['gewichtsklasse'],
                        'beschrijving' => $robotData['beschrijving'] ?? null,
                    ]);
                    $bestaandeRobot->save();

                    $bewerkteRobotIds[] = $bestaandeRobot->id;

                    continue;
                }

                $nieuweRobot = Robot::query()->create([
                    'team_id' => $team->id,
                    'naam' => $robotData['naam'],
                    'gewichtsklasse' => $robotData['gewichtsklasse'],
                    'beschrijving' => $robotData['beschrijving'] ?? null,
                    'status' => RobotStatus::InOntwikkeling,
                ]);

                $bewerkteRobotIds[] = $nieuweRobot->id;
            }

            $team->robots()
                ->whereNotIn('id', $bewerkteRobotIds)
                ->delete();

            if ($verwijderTeamfoto) {
                $this->teamPhotoUploads->remove($team);
            }

            if ($request->hasFile('teamfoto')) {
                $this->teamPhotoUploads->replace(
                    team: $team,
                    photo: $request->file('teamfoto'),
                    source: 'team_registratie_update',
                    caption: 'Bijgewerkt via teambewerklink',
                );
            }
        });

        $team->load(['media', 'robots']);

        return (new EditableTeamRegistrationResource($team))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    private function authorizeTeamMutation(Request $request, Team $team): void
    {
        $user = $request->user();

        if ($user === null) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        if ($user->hasAnyRole(UserRole::Admin, UserRole::Moderator)) {
            return;
        }

        if ($team->captain_user_id !== $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'Alleen de gekoppelde teamcaptain mag wijzigingen doorvoeren.');
        }
    }

    private function resolveTeamByToken(string $token): Team
    {
        $tokenHash = hash('sha256', $token);

        return Team::query()
            ->where('edit_token_hash', $tokenHash)
            ->whereNotNull('edit_token_expires_at')
            ->where('edit_token_expires_at', '>', now())
            ->firstOrFail();
    }
}
