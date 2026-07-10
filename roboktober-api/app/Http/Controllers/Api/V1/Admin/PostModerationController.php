<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateAdminPostContentRequest;
use App\Http\Requests\Api\V1\UpdatePublishStateRequest;
use App\Http\Resources\Api\V1\AdminPostResource;
use App\Models\Post;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostModerationController extends Controller
{
    public function __construct(private readonly AuditLogger $audit)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $status = request()->query('status');
        $zoekterm = request()->query('q');

        $posts = Post::query()
            ->when($status === 'published', static fn ($query) => $query->where('is_published', true))
            ->when($status === 'draft', static fn ($query) => $query->where('is_published', false))
            ->when(is_string($zoekterm) && $zoekterm !== '', static function ($query) use ($zoekterm): void {
                $query
                    ->where('titel', 'like', '%'.$zoekterm.'%')
                    ->orWhere('slug', 'like', '%'.$zoekterm.'%')
                    ->orWhere('categorie', 'like', '%'.$zoekterm.'%');
            })
            ->with('media')
            ->latest('published_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return AdminPostResource::collection($posts);
    }

    public function show(Post $post): AdminPostResource
    {
        $this->authorize('view', $post);

        $post->load('media');

        return new AdminPostResource($post);
    }

    public function updateStatus(UpdatePublishStateRequest $request, Post $post): AdminPostResource
    {
        $this->authorize('update', $post);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{is_published: bool, published_at?: string|null} $validated */
        $validated = $request->validated();

        $isPublished = (bool) $validated['is_published'];

        $before = [
            'is_published' => (bool) $post->is_published,
            'published_at' => $post->published_at?->toISOString(),
        ];

        $post->forceFill([
            'is_published' => $isPublished,
            'published_at' => $isPublished
                ? ($validated['published_at'] ?? $post->published_at ?? now())
                : null,
        ])->save();

        $this->audit->log(
            actor: $actor,
            action: 'post.publish_state_updated',
            subject: $post,
            before: $before,
            after: [
                'is_published' => (bool) $post->is_published,
                'published_at' => $post->published_at?->toISOString(),
            ],
        );

        $post->load('media');

        return new AdminPostResource($post);
    }

    public function updateContent(UpdateAdminPostContentRequest $request, Post $post): AdminPostResource
    {
        $this->authorize('update', $post);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{titel: string, excerpt?: string|null, content: string, content_format: string, categorie?: string|null, tags?: list<string>|null} $validated */
        $validated = $request->validated();

        $before = [
            'titel' => $post->titel,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'content_format' => $post->content_format->value,
            'categorie' => $post->categorie,
            'tags' => $post->tags,
        ];

        $post->forceFill([
            'titel' => $validated['titel'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'content_format' => $validated['content_format'],
            'categorie' => $validated['categorie'] ?? null,
            'tags' => $validated['tags'] ?? [],
        ])->save();

        $this->audit->log(
            actor: $actor,
            action: 'post.content_updated',
            subject: $post,
            before: $before,
            after: [
                'titel' => $post->titel,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'content_format' => $post->content_format->value,
                'categorie' => $post->categorie,
                'tags' => $post->tags,
            ],
        );

        $post->load('media');

        return new AdminPostResource($post);
    }
}
