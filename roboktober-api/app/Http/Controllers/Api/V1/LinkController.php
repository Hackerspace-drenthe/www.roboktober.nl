<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LinkResource;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * REST API controller for Build Hub links/resources.
 *
 * Routes:
 * - GET /api/v1/links → LinkController@index
 *
 * @see PLAN.md §5.2 — REST API endpoints
 * @see PLAN.md §6.5  — Build Hub page design
 */
class LinkController extends Controller
{
    /**
     * Returns all links, optionally filtered by category.
     *
     * Query parameters:
     * - categorie: string (wallie|community|competitie|tools|onderdelen|documentatie)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $links = Link::query()
            ->when(
                $request->filled('categorie'),
                fn ($q) => $q->where('categorie', $request->string('categorie')->toString()),
            )
            ->orderBy('categorie')
            ->orderBy('titel')
            ->get();

        return LinkResource::collection($links);
    }
}
