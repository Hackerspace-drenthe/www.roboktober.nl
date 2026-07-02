<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTeamRequest;
use App\Http\Resources\Api\V1\TeamResource;
use App\Mail\NieuwTeamAanmelding;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
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
        /** @var Team $team */
        $team = Team::query()->create([...$validated, 'status' => \App\Enums\TeamStatus::Pending]);

        // Notify organizers — uses MAIL_MAILER=log in local dev (no real email sent)
        Mail::to(config('mail.from.address'))->send(new NieuwTeamAanmelding($team));

        return (new TeamResource($team))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}

