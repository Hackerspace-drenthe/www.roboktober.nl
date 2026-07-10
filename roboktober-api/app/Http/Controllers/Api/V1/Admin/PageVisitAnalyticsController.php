<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\PageVisitAggregate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PageVisitAnalyticsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var array{granularity?: string, from?: string, to?: string, limit_pages?: int} $validated */
        $validated = $request->validate([
            'granularity' => ['nullable', 'string', 'in:hourly,daily'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'limit_pages' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $granularity = (string) ($validated['granularity'] ?? 'daily');
        $limitPages = (int) ($validated['limit_pages'] ?? 8);

        $defaultFrom = $granularity === 'hourly'
            ? Carbon::now()->subHours(23)->startOfHour()
            : Carbon::now()->subDays(29)->startOfDay();

        $defaultTo = $granularity === 'hourly'
            ? Carbon::now()->startOfHour()
            : Carbon::now()->endOfDay();

        $from = isset($validated['from'])
            ? Carbon::parse((string) $validated['from'])
            : $defaultFrom;

        $to = isset($validated['to'])
            ? Carbon::parse((string) $validated['to'])
            : $defaultTo;

        if ($granularity === 'hourly') {
            $from = $from->startOfHour();
            $to = $to->startOfHour();
        } else {
            $from = $from->startOfDay();
            $to = $to->endOfDay();
        }

        if ($to->lt($from)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        $retentionCutoff = Carbon::now()->subDays(30)->startOfDay();
        if ($from->lt($retentionCutoff)) {
            $from = $retentionCutoff;
        }

        $rows = PageVisitAggregate::query()
            ->whereBetween('bucket_start', [$from, $to])
            ->get(['page_path', 'bucket_start', 'visits']);

        /** @var array<string, int> $totalsByPage */
        $totalsByPage = [];

        foreach ($rows as $row) {
            $path = (string) $row->page_path;
            $totalsByPage[$path] = ($totalsByPage[$path] ?? 0) + (int) $row->visits;
        }

        arsort($totalsByPage);
        $topPages = array_slice(array_keys($totalsByPage), 0, $limitPages);

        $bucketLabels = $this->buildBucketLabels($from, $to, $granularity);

        /** @var array<string, array<int, int>> $seriesMap */
        $seriesMap = [];

        foreach ($topPages as $pagePath) {
            $seriesMap[$pagePath] = array_fill(0, count($bucketLabels), 0);
        }

        $bucketIndex = array_flip($bucketLabels);

        foreach ($rows as $row) {
            $path = (string) $row->page_path;

            if (! in_array($path, $topPages, true)) {
                continue;
            }

            $bucketKey = $this->bucketKey(
                bucketStart: Carbon::parse((string) $row->bucket_start),
                granularity: $granularity,
            );

            if (! array_key_exists($bucketKey, $bucketIndex)) {
                continue;
            }

            $seriesMap[$path][$bucketIndex[$bucketKey]] += (int) $row->visits;
        }

        $series = collect($topPages)
            ->map(static fn (string $path): array => [
                'page_path' => $path,
                'total' => $totalsByPage[$path] ?? 0,
                'points' => $seriesMap[$path] ?? [],
            ])
            ->values();

        $eventRows = AnalyticsEvent::query()
            ->whereBetween('occurred_at', [$from, $to])
            ->orderBy('occurred_at')
            ->get([
                'session_id',
                'user_id',
                'visitor_hash',
                'event_type',
                'event_name',
                'page_path',
                'occurred_at',
            ]);

        $loggedInUsers = $eventRows
            ->pluck('user_id')
            ->filter(static fn (mixed $id): bool => is_int($id))
            ->unique()
            ->count();

        $anonymousVisitors = $eventRows
            ->filter(static fn ($event): bool => $event->user_id === null)
            ->pluck('visitor_hash')
            ->filter(static fn (mixed $hash): bool => is_string($hash) && $hash !== '')
            ->unique()
            ->count();

        $eventTypeCounts = $eventRows
            ->groupBy('event_type')
            ->map(static fn ($items): int => $items->count())
            ->sortDesc()
            ->all();

        $sessionGroups = $eventRows->groupBy('session_id');

        /** @var array<string, int> $transitionCounts */
        $transitionCounts = [];

        $recentSessions = $sessionGroups
            ->map(function ($sessionRows, string $sessionId) use (&$transitionCounts): array {
                $rows = $sessionRows->sortBy('occurred_at')->values();
                $sequence = [];

                foreach ($rows as $row) {
                    if (! is_string($row->page_path) || $row->page_path === '') {
                        continue;
                    }

                    $last = $sequence[count($sequence) - 1] ?? null;
                    if ($last !== $row->page_path) {
                        $sequence[] = $row->page_path;
                    }
                }

                for ($index = 0; $index < count($sequence) - 1; $index++) {
                    $fromPath = $sequence[$index] ?? null;
                    $toPath = $sequence[$index + 1] ?? null;

                    if (! is_string($fromPath) || ! is_string($toPath)) {
                        continue;
                    }

                    $transitionKey = $fromPath.' -> '.$toPath;
                    $transitionCounts[$transitionKey] = ($transitionCounts[$transitionKey] ?? 0) + 1;
                }

                $firstRow = $rows->first();
                $lastRow = $rows->last();

                return [
                    'session_id' => $sessionId,
                    'actor_type' => $firstRow?->user_id === null ? 'anonymous' : 'logged_in',
                    'user_id' => $firstRow?->user_id,
                    'events_count' => $rows->count(),
                    'steps' => array_slice($sequence, 0, 12),
                    'last_seen_at' => Carbon::parse((string) $lastRow?->occurred_at)->toIso8601String(),
                ];
            })
            ->sortByDesc('last_seen_at')
            ->take(15)
            ->values();

        arsort($transitionCounts);
        $topTransitions = collect(array_slice($transitionCounts, 0, 12, true))
            ->map(static function (int $count, string $key): array {
                [$fromPath, $toPath] = explode(' -> ', $key, 2);

                return [
                    'from' => $fromPath,
                    'to' => $toPath,
                    'count' => $count,
                ];
            })
            ->values();

        return response()->json([
            'data' => [
                'granularity' => $granularity,
                'from' => $from->toIso8601String(),
                'to' => $to->toIso8601String(),
                'labels' => $bucketLabels,
                'series' => $series,
                'totals' => [
                    'overall_visits' => array_sum($totalsByPage),
                    'pages_tracked' => count($totalsByPage),
                    'sessions_tracked' => $sessionGroups->count(),
                    'logged_in_users' => $loggedInUsers,
                    'anonymous_visitors' => $anonymousVisitors,
                ],
                'events_by_type' => $eventTypeCounts,
                'journeys' => [
                    'top_transitions' => $topTransitions,
                    'recent_sessions' => $recentSessions,
                ],
                'retention_days' => 30,
            ],
        ]);
    }

    /**
     * @return list<string>
     */
    private function buildBucketLabels(Carbon $from, Carbon $to, string $granularity): array
    {
        $labels = [];
        $cursor = $from->copy();

        if ($granularity === 'hourly') {
            while ($cursor->lte($to)) {
                $labels[] = $cursor->format('Y-m-d H:00');
                $cursor->addHour();
            }

            return $labels;
        }

        while ($cursor->lte($to)) {
            $labels[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }

        return $labels;
    }

    private function bucketKey(Carbon $bucketStart, string $granularity): string
    {
        return $granularity === 'hourly'
            ? $bucketStart->format('Y-m-d H:00')
            : $bucketStart->format('Y-m-d');
    }
}
