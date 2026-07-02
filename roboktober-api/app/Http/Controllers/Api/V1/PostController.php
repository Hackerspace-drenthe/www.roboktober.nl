<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * REST API controller for published blog posts.
 *
 * All endpoints are read-only (public). Write operations are via Filament admin.
 *
 * Routes:
 * - GET /api/v1/posts         → PostController@index
 * - GET /api/v1/posts/{slug}  → PostController@show
 *
 * @see PLAN.md §5.2 — REST API endpoints
 */
class PostController extends Controller
{
    /**
     * Returns a paginated list of published posts, ordered by publication date (newest first).
     *
     * Query parameters:
     * - per_page: int (default 12, max 50)
     * - categorie: string (filter by category)
     * - tag: string (filter by tag)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min((int) $request->integer('per_page', 12), 50);

        /** @var LengthAwarePaginator<int, Post> $posts */
        $posts = Post::query()
            ->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->when(
                $request->filled('categorie'),
                fn ($q) => $q->where('categorie', $request->string('categorie')->toString()),
            )
            ->when(
                $request->filled('tag'),
                fn ($q) => $q->whereJsonContains('tags', $request->string('tag')->toString()),
            )
            ->orderByDesc('published_at')
            ->paginate($perPage);

        return PostResource::collection($posts);
    }

    /**
     * Returns a single published post by its slug.
     *
     * Loads all media collections: featured, gallery, bijlagen.
     */
    public function show(string $slug): PostResource
    {
        /** @var Post $post */
        $post = Post::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return new PostResource($post);
    }
}
