<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the media_varianten table.
 *
 * Stores auto-generated thumbnails and resized variants of uploaded images.
 * Generated asynchronously via queue jobs after upload.
 *
 * SOLID: SRP — separate table keeps variant metadata apart from source file metadata.
 *
 * @see PLAN.md §5.2 — media_varianten schema
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_varianten', function (Blueprint $table): void {
            $table->id();

            // Source media file
            $table->foreignId('media_id')
                ->constrained('media')
                ->cascadeOnDelete();

            // Variant name: 'thumb_sm' (150px), 'medium' (600px), 'large' (1200px), 'preview' (STL/PDF)
            $table->string('naam', 50);

            // Storage path of the generated variant
            $table->string('pad');

            // IANA media type (may differ from source, e.g. STL preview is 'image/webp')
            $table->string('mime_type', 100);

            // Variant file size in bytes
            $table->unsignedBigInteger('grootte');

            // Variant-specific metadata (e.g. width, height)
            $table->json('meta')->nullable();

            $table->timestamps();

            // Each media item has at most one variant per name
            $table->unique(['media_id', 'naam']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_varianten');
    }
};
