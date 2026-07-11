<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateUserRoleRequest;
use App\Http\Resources\Api\V1\AdminUserResource;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserManagementController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $zoekterm = request()->query('q');

        $users = User::query()
            ->when(is_string($zoekterm) && $zoekterm !== '', static function ($query) use ($zoekterm): void {
                $query
                    ->where('name', 'like', '%'.$zoekterm.'%')
                    ->orWhere('email', 'like', '%'.$zoekterm.'%');
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return AdminUserResource::collection($users);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): AdminUserResource|JsonResponse
    {
        $this->authorize('updateRole', $user);

        /** @var User $actor */
        $actor = $request->user();

        if ($actor->id === $user->id) {
            return response()->json([
                'message' => 'Je kunt je eigen rol niet aanpassen via deze endpoint.',
            ], 422);
        }

        /** @var array{role: string} $validated */
        $validated = $request->validated();

        $beforeRole = $user->role->value;

        $user->forceFill([
            'role' => UserRole::from($validated['role']),
        ])->save();

        $this->audit->log(
            actor: $actor,
            action: 'user.role_updated',
            subject: $user,
            before: ['role' => $beforeRole],
            after: ['role' => $user->role->value],
        );

        return new AdminUserResource($user);
    }
}
