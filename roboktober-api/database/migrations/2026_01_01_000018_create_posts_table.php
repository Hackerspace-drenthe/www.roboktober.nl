<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the posts table.
 *
 * Blog/news posts for the Nieuws section of Roboktober.
 * Supports multiple media attachments via mediables:
 * - 'featured'  → single header image (shown in card and at top of article)
 * - 'gallery'   → additional in-article photos
 * - 'bijlagen'  → downloadable files (PDFs, STL, etc.)
 *
 * Content written in Filament RichEditor (HTML) or Markdown.
 * B1 language level for middelbare school audience (PLAN.md §4).
 *
 * @see PLAN.md §5.2 — posts schema
 * @see PLAN.md §6.7  — Nieuws/Blog page design
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();

            // URL slug (e.g. 'wallie-v3-gereed', 'eerste-roboktober-aangekondigd')
            $table->string('slug')->unique();

            // Post title
            $table->string('titel');

            // Short teaser text (shown in post cards, max ~160 chars)
            $table->string('excerpt', 320)->nullable();

            // Full article body
            $table->longText('content');

            // Content format: 'html' (RichEditor output) or 'markdown'
            $table->enum('content_format', ['html', 'markdown'])->default('html');

            // Editorial category (free-form string for v1, can be enum later)
            $table->string('categorie', 100)->nullable();

            // Tags stored as JSON array (e.g. ["wallie", "build-log", "beetleweight"])
            $table->json('tags')->nullable();

            // Visibility
            $table->boolean('is_published')->default(false);

            // Publication date (can be set in the future for scheduled posts)
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            // Index for public listing queries
            $table->index(['is_published', 'published_at']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
