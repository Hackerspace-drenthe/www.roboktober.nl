<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageResource;
use App\Models\Page;

/**
 * REST API controller for CMS pages.
 *
 * Routes:
 * - GET /api/v1/pages/{slug} → PageController@show
 *
 * @see PLAN.md §5.2 — REST API endpoints
 */
class PageController extends Controller
{
    /**
     * Returns a single published page by its slug.
     */
    public function show(string $slug): PageResource
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with(['media'])
            ->firstOrFail();

        return new PageResource($page);
    }
}
