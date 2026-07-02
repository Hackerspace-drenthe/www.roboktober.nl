<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the media table.
 *
 * Central file/asset storage for all uploadable content (images, STL, PDF, BOM,
 * firmware, video links). Replaces old post_images + assets tables.
 *
 * SOLID: Single Responsibility — this table only stores file metadata.
 * Open/Closed — adding a new file type requires no schema change, only a new mime_type.
 *
 * @see PLAN.md §5.2 — database schema design
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table): void {
            // Primary key
            $table->id();

            // Human-readable name (e.g. "Wallie v3 CAD model")
            $table->string('naam');

            // Original filename on upload (e.g. "wallie_v3.stl")
            $table->string('bestandsnaam');

            // Storage path relative to disk root
            $table->string('pad');

            // Storage disk identifier (default: 'public', can be 's3', etc.)
            $table->string('disk')->default('public');

            // IANA media type (e.g. 'image/jpeg', 'model/stl', 'application/pdf')
            // This replaces the old enum — OCP compliant, no schema change for new types
            $table->string('mime_type', 100);

            // File extension without dot (e.g. 'jpg', 'stl', 'pdf')
            $table->string('extensie', 20);

            // File size in bytes (unsigned bigint to support files > 2 GB)
            $table->unsignedBigInteger('grootte');

            // SHA-256 hash for deduplication and integrity verification
            // OWASP A8: integrity check on uploaded files
            $table->char('hash', 64)->nullable()->index();

            // Flexible metadata: dimensions for images, triangle count for STL, page count for PDF
            // Stored as JSON for schema-free extensibility (OCP)
            $table->json('meta')->nullable();

            // Semantic versioning string (e.g. "1.0.0", "2.1.3")
            $table->string('versie', 20)->nullable();

            // Human-readable version notes / changelog entry
            $table->text('versie_notities')->nullable();

            // Self-referencing FK for version chain (nullable = first version)
            $table->foreignId('vorige_versie_id')
                ->nullable()
                ->constrained('media')
                ->nullOnDelete();

            // Download counter (unsigned int, starts at 0)
            $table->unsignedInteger('downloads')->default(0);

            // Which authenticated user uploaded this file
            $table->foreignId('geupload_door')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
