<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the links table.
 *
 * Stores external URLs (community resources, tools, documentation, competition links).
 * This is intentionally separate from the media table (SOLID SRP):
 * - media = uploadable files stored on disk
 * - links = external URLs with optional OG metadata cache
 *
 * @see PLAN.md §5.2 — links schema (renamed from 'resources')
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table): void {
            $table->id();

            // Display title of the link
            $table->string('titel');

            // The external URL (validated as URL on application level)
            $table->string('url', 2048);

            // Optional short description
            $table->text('beschrijving')->nullable();

            // IANA mime type hint (e.g. 'text/html', 'application/pdf' for direct file links)
            $table->string('mime_type', 100)->nullable();

            // Cached Open Graph / link preview metadata
            // Keys: og_title, og_description, og_image, favicon, last_checked_status_code
            $table->json('meta')->nullable();

            // Content category for filtering/grouping in the Build Hub page
            // Values mirror the page design in PLAN.md §6.5
            $table->enum('categorie', [
                'wallie',        // Wallie-specific resources
                'community',     // Community links (Discord, forums)
                'competitie',    // Competition registrations and rules
                'tools',         // Online design tools (Onshape, TinkerCAD, etc.)
                'onderdelen',    // Parts suppliers
                'documentatie',  // Manuals, datasheets
            ])->default('community');

            // Optional: who submitted or maintains this link
            $table->string('eigenaar')->nullable();

            // Timestamp when the link was last verified as alive
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            // Index for category filtering (common query pattern)
            $table->index('categorie');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
