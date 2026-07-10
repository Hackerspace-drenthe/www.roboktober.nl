<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Models\Post;

describe('GET /api/v1/posts', function (): void {
    it('returns only published posts', function (): void {
        Post::factory()->create(['is_published' => false]);
        Post::factory()->create([
            'is_published' => true,
            'published_at' => now()->subDay(),
            'titel' => 'Gepubliceerde post',
        ]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.titel', 'Gepubliceerde post');
    });

    it('returns expected JSON structure', function (): void {
        Post::factory()->create(['is_published' => true, 'published_at' => now()->subDay()]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'slug', 'titel', 'excerpt', 'content_format', 'published_at']],
                'meta',
                'links',
            ]);
    });

    it('paginates results', function (): void {
        Post::factory(5)->create(['is_published' => true, 'published_at' => now()->subDay()]);

        $response = $this->getJson('/api/v1/posts?per_page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 5);
    });
});

describe('GET /api/v1/posts/{slug}', function (): void {
    it('returns a single published post by slug', function (): void {
        $post = Post::factory()->create([
            'is_published' => true,
            'published_at' => now()->subDay(),
            'slug' => 'mijn-post',
            'titel' => 'Mijn Post',
        ]);

        $response = $this->getJson("/api/v1/posts/{$post->slug}");

        $response->assertOk()
            ->assertJsonPath('data.slug', 'mijn-post')
            ->assertJsonPath('data.titel', 'Mijn Post');
    });

    it('returns 404 for unpublished post', function (): void {
        $post = Post::factory()->create(['is_published' => false, 'slug' => 'draft']);

        $this->getJson("/api/v1/posts/{$post->slug}")->assertNotFound();
    });

    it('returns 404 for unknown slug', function (): void {
        $this->getJson('/api/v1/posts/bestaat-niet')->assertNotFound();
    });

    it('sanitizes unsafe html in post content output', function (): void {
        $post = Post::factory()->create([
            'is_published' => true,
            'published_at' => now()->subDay(),
            'slug' => 'veilig-html-artikel',
            'content_format' => ContentFormat::Html,
            'content' => '<p>Veilige tekst</p><script>alert(1)</script><a href="javascript:alert(2)">klik</a>',
        ]);

        $response = $this->getJson('/api/v1/posts/'.$post->slug);

        $response->assertOk();

        $content = (string) $response->json('data.content');

        expect($content)->toContain('<p>Veilige tekst</p>');
        expect($content)->not->toContain('<script>');
        expect($content)->not->toContain('javascript:alert(2)');
    });
});
