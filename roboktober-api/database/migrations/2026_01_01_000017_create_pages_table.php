<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the pages table.
 *
 * CMS pages managed in Filament admin.
 * Static content pages: Home, Programma, Build Hub, Resources, Credits, etc.
 * Media attached via mediables 'hero' collection (full-width background image).
 *
 * @see PLAN.md §5.2 — pages schema
 * @see PLAN.md §6.x  — page designs
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table): void {
            $table->id();

            // URL slug (e.g. 'home', 'programma', 'build-hub')
            $table->string('slug')->unique();

            // Page title (shown in <title> and <h1>)
            $table->string('titel');

            // Full page body content
            $table->longText('content');

            // Content format: 'html' (Filament RichEditor) or 'markdown'
            $table->enum('content_format', ['html', 'markdown'])->default('html');

            // SEO metadata: title, description, og_image, canonical_url
            $table->json('seo')->nullable();

            // Visibility: draft vs. published
            $table->boolean('is_published')->default(false);

            // When the page went live (nullable for drafts)
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            // Index for slug lookups (primary public API query)
            $table->index('slug');
            $table->index(['is_published', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
