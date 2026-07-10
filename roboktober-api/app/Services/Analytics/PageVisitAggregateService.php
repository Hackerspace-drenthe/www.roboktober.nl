<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\PageVisitAggregate;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

class PageVisitAggregateService
{
    public function increment(string $path, Carbon $occurredAt): void
    {
        if (str_starts_with($path, '/admin')) {
            return;
        }

        $bucketStart = $occurredAt->copy()->startOfHour();

        $updated = PageVisitAggregate::query()
            ->where('page_path', $path)
            ->where('bucket_start', $bucketStart)
            ->increment('visits');

        if ($updated > 0) {
            return;
        }

        try {
            PageVisitAggregate::query()->create([
                'page_path' => $path,
                'bucket_start' => $bucketStart,
                'visits' => 1,
            ]);
        } catch (QueryException) {
            PageVisitAggregate::query()
                ->where('page_path', $path)
                ->where('bucket_start', $bucketStart)
                ->increment('visits');
        }
    }
}
