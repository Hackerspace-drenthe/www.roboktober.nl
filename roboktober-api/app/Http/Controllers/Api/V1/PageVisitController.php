<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TrackPageVisitRequest;
use App\Services\Analytics\PageVisitAggregateService;
use App\Services\Analytics\PathNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class PageVisitController extends Controller
{
    public function __construct(
        private readonly PathNormalizer $pathNormalizer,
        private readonly PageVisitAggregateService $pageVisitAggregate,
    ) {}

    public function store(TrackPageVisitRequest $request): JsonResponse
    {
        /** @var array{page_path: string} $validated */
        $validated = $request->validated();

        $path = $this->pathNormalizer->normalizePath(
            path: $validated['page_path'],
            excludeAdmin: true,
        );

        if (! is_string($path)) {
            return response()->json(['ok' => true]);
        }

        $this->pageVisitAggregate->increment($path, Carbon::now());

        return response()->json(['ok' => true]);
    }
}
