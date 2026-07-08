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
        $action = request()->query('action');
        $actorId = request()->query('actor_user_id');
        $subjectType = request()->query('subject_type');

        $logs = AuditLog::query()
            ->when(is_string($action) && $action !== '', static function ($query) use ($action): void {
                $query->where('action', $action);
            })
            ->when(is_numeric($actorId), static function ($query) use ($actorId): void {
                $query->where('actor_user_id', (int) $actorId);
            })
            ->when(is_string($subjectType) && $subjectType !== '', static function ($query) use ($subjectType): void {
                $query->where('subject_type', $subjectType);
            })
            ->with('actor')
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        return AdminAuditLogResource::collection($logs);
    }
}
