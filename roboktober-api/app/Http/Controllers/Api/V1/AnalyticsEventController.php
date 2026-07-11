<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TrackAnalyticsEventRequest;
use App\Models\AnalyticsEvent;
use App\Services\Analytics\PageVisitAggregateService;
use App\Services\Analytics\PathNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class AnalyticsEventController extends Controller
{
    public function __construct(
        private readonly PathNormalizer $pathNormalizer,
        private readonly PageVisitAggregateService $pageVisitAggregate,
    ) {}

    public function store(TrackAnalyticsEventRequest $request): JsonResponse
    {
        /** @var array{session_id: string, event_type: string, event_name?: string, page_path?: string, route_name?: string, referrer_path?: string, payload?: array<string, mixed>, occurred_at?: string} $validated */
        $validated = $request->validated();

        $pagePath = $this->pathNormalizer->normalizePath($validated['page_path'] ?? null);
        $referrerPath = $this->pathNormalizer->normalizePath($validated['referrer_path'] ?? null);

        $occurredAt = isset($validated['occurred_at'])
            ? Carbon::parse((string) $validated['occurred_at'])
            : Carbon::now();

        $user = $request->user('sanctum');

        AnalyticsEvent::query()->create([
            'user_id' => $user?->id,
            'session_id' => $validated['session_id'],
            'visitor_hash' => $this->resolveVisitorHash($request->ip(), (string) $request->userAgent()),
            'event_type' => $validated['event_type'],
            'event_name' => $validated['event_name'] ?? null,
            'page_path' => $pagePath,
            'route_name' => $validated['route_name'] ?? null,
            'referrer_path' => $referrerPath,
            'payload' => $validated['payload'] ?? null,
            'occurred_at' => $occurredAt,
        ]);

        if ($validated['event_type'] === 'page_view' && is_string($pagePath)) {
            $this->pageVisitAggregate->increment($pagePath, $occurredAt);
        }

        return response()->json(['ok' => true]);
    }

    private function resolveVisitorHash(?string $ip, string $userAgent): string
    {
        return hash('sha256', (string) config('app.key').'|'.($ip ?? 'unknown').'|'.$userAgent);
    }
}
