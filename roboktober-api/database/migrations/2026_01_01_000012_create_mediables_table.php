<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the mediables pivot table.
 *
 * Polymorphic many-to-many relationship between media and any model.
 * A single media item can appear in multiple contexts (Post gallery, Robot photo, etc.)
 * under different collections.
 *
 * SOLID:
 * - SRP: this table's only job is mapping media ↔ model relationships
 * - LSP: any model implementing HasMedia can use this table uniformly
 * - ISP: consumers query only the collection they need, not the full mediables set
 *
 * Collections used:
 * - 'featured'   → single header/cover image
 * - 'gallery'    → multiple photos in a post or page
 * - 'bijlagen'   → STL, PDF, BOM, firmware downloads
 * - 'foto'       → team or robot profile photo
 * - 'hero'       → full-width page background image
 *
 * @see PLAN.md §5.2 — mediables schema
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mediables', function (Blueprint $table): void {
            $table->id();

            // The media file being attached
            $table->foreignId('media_id')
                ->constrained('media')
                ->cascadeOnDelete();

            // Polymorphic model reference (e.g. App\Models\Post, App\Models\Robot)
            $table->morphs('mediable');

            // Logical grouping within the parent model (default: 'default')
            $table->string('collectie', 50)->default('default');

            // Accessibility: alt text for screen readers (WCAG 2.2 AA compliance)
            $table->string('alt_tekst')->nullable();

            // Optional caption displayed below the image
            $table->string('onderschrift')->nullable();

            // Sort order within the collection (lower = earlier)
            $table->unsignedSmallInteger('volgorde')->default(0);

            // Extra per-attachment metadata (e.g. crop coordinates)
            $table->json('meta')->nullable();

            $table->timestamps();

            // Efficient lookup: find all media for a model in a specific collection
            $table->index(['mediable_type', 'mediable_id', 'collectie'], 'mediables_context_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mediables');
    }
};
