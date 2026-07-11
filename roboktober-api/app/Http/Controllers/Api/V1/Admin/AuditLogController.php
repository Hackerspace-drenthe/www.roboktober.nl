<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AdminAuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', AuditLog::class);

        $action = trim(request()->string('action')->toString());
        $actorId = request()->query('actor_user_id');
        $subjectType = trim(request()->string('subject_type')->toString());

        $logs = AuditLog::query()
            ->when($action !== '', static fn ($query) => $query->where('action', $action))
            ->when(is_numeric($actorId), static fn ($query) => $query->where('actor_user_id', (int) $actorId))
            ->when($subjectType !== '', static fn ($query) => $query->where('subject_type', $subjectType))
            ->with('actor')
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        return AdminAuditLogResource::collection($logs);
    }
}
