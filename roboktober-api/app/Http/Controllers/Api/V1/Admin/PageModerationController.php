<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateAdminPageContentRequest;
use App\Http\Requests\Api\V1\UpdatePublishStateRequest;
use App\Http\Resources\Api\V1\AdminPageResource;
use App\Models\Page;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageModerationController extends Controller
{
    public function __construct(private readonly AuditLogger $audit)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $status = request()->query('status');
        $zoekterm = request()->query('q');

        $pages = Page::query()
            ->when($status === 'published', static fn ($query) => $query->where('is_published', true))
            ->when($status === 'draft', static fn ($query) => $query->where('is_published', false))
            ->when(is_string($zoekterm) && $zoekterm !== '', static function ($query) use ($zoekterm): void {
                $query
                    ->where('titel', 'like', '%'.$zoekterm.'%')
                    ->orWhere('slug', 'like', '%'.$zoekterm.'%');
            })
            ->with('media')
            ->latest('published_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return AdminPageResource::collection($pages);
    }

    public function show(Page $page): AdminPageResource
    {
        $page->load('media');

        return new AdminPageResource($page);
    }

    public function updateStatus(UpdatePublishStateRequest $request, Page $page): AdminPageResource
    {
        /** @var User $actor */
        $actor = $request->user();

        /** @var array{is_published: bool, published_at?: string|null} $validated */
        $validated = $request->validated();

        $isPublished = (bool) $validated['is_published'];

        $before = [
            'is_published' => (bool) $page->is_published,
            'published_at' => $page->published_at?->toISOString(),
        ];

        $page->forceFill([
            'is_published' => $isPublished,
            'published_at' => $isPublished
                ? ($validated['published_at'] ?? $page->published_at ?? now())
                : null,
        ])->save();

        $this->audit->log(
            actor: $actor,
            action: 'page.publish_state_updated',
            subject: $page,
            before: $before,
            after: [
                'is_published' => (bool) $page->is_published,
                'published_at' => $page->published_at?->toISOString(),
            ],
        );

        $page->load('media');

        return new AdminPageResource($page);
    }

    public function updateContent(UpdateAdminPageContentRequest $request, Page $page): AdminPageResource
    {
        /** @var User $actor */
        $actor = $request->user();

        /** @var array{titel: string, content: string, content_format: string, seo?: array<string, mixed>|null} $validated */
        $validated = $request->validated();

        $before = [
            'titel' => $page->titel,
            'content' => $page->content,
            'content_format' => $page->content_format->value,
            'seo' => $page->seo,
        ];

        $page->forceFill([
            'titel' => $validated['titel'],
            'content' => $validated['content'],
            'content_format' => $validated['content_format'],
            'seo' => $validated['seo'] ?? null,
        ])->save();

        $this->audit->log(
            actor: $actor,
            action: 'page.content_updated',
            subject: $page,
            before: $before,
            after: [
                'titel' => $page->titel,
                'content' => $page->content,
                'content_format' => $page->content_format->value,
                'seo' => $page->seo,
            ],
        );

        $page->load('media');

        return new AdminPageResource($page);
    }
}
