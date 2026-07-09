<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TrackPageVisitRequest;
use App\Models\PageVisitAggregate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Database\QueryException;

class PageVisitController extends Controller
{
    public function store(TrackPageVisitRequest $request): JsonResponse
    {
        $rawPath = (string) $request->validated()['page_path'];
        $path = parse_url($rawPath, PHP_URL_PATH);

        if (! is_string($path) || $path === '' || str_starts_with($path, '/api') || str_starts_with($path, '/admin')) {
            return response()->json(['ok' => true]);
        }

        $bucketStart = Carbon::now()->startOfHour();

        $updated = PageVisitAggregate::query()
            ->where('page_path', $path)
            ->where('bucket_start', $bucketStart)
            ->increment('visits');

        if ($updated === 0) {
            try {
                PageVisitAggregate::query()->create([
                    'page_path' => $path,
                    'bucket_start' => $bucketStart,
                    'visits' => 1,
                ]);
            } catch (QueryException $exception) {
                // Handle a race between first inserts in the same hour.
                PageVisitAggregate::query()
                    ->where('page_path', $path)
                    ->where('bucket_start', $bucketStart)
                    ->increment('visits');
            }
        }

        return response()->json(['ok' => true]);
    }
}
