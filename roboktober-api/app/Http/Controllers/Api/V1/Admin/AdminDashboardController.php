<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\TeamStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Page;
use App\Models\Post;
use App\Models\Team;
use App\Models\TeamUpdate;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        $this->authorize('viewAdminIndex', Team::class);

        $pendingTeamsCount = Team::query()->where('status', TeamStatus::Pending)->count();
        $draftPostsCount = Post::query()->where('is_published', false)->count();
        $draftPagesCount = Page::query()->where('is_published', false)->count();
        $draftTeamUpdatesCount = TeamUpdate::query()->where('is_published', false)->count();

        $pendingTeams = Team::query()
            ->where('status', TeamStatus::Pending)
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'naam', 'contactpersoon', 'created_at']);

        $recentActivity = AuditLog::query()
            ->with('actor')
            ->latest('id')
            ->limit(8)
            ->get();

        return response()->json([
            'data' => [
                'stats' => [
                    'pending_teams' => $pendingTeamsCount,
                    'draft_posts' => $draftPostsCount,
                    'draft_pages' => $draftPagesCount,
                    'draft_team_updates' => $draftTeamUpdatesCount,
                ],
                'pending_teams' => $pendingTeams->map(static fn (Team $team): array => [
                    'id' => $team->id,
                    'naam' => $team->naam,
                    'contactpersoon' => $team->contactpersoon,
                    'created_at' => $team->created_at?->toISOString(),
                ])->values(),
                'recent_activity' => $recentActivity->map(static fn (AuditLog $log): array => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'subject_type' => $log->subject_type,
                    'subject_id' => $log->subject_id,
                    'actor' => [
                        'id' => $log->actor?->id,
                        'name' => $log->actor?->name,
                        'email' => $log->actor?->email,
                    ],
                    'created_at' => $log->created_at?->toISOString(),
                ])->values(),
            ],
        ]);
    }
}
