<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the teams table.
 *
 * Stores team registrations for Roboktober combat robot events.
 * Registration is always open (no event-scoped deadline in v1).
 * Team photos are stored in the mediables table under collection 'foto'.
 *
 * @see PLAN.md §5.1 — team registration model
 * @see PLAN.md §5.2 — teams schema
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table): void {
            $table->id();

            // Team display name
            $table->string('naam');

            // Primary contact person name
            $table->string('contactpersoon');

            // Contact email (used for notifications, OWASP: stored hashed if needed)
            $table->string('email');

            // Number of adult (18+) team members
            $table->unsignedSmallInteger('volwassenen')->default(1);

            // Number of minors (<18) — nullable if team has no minors
            $table->unsignedSmallInteger('kinderen')->nullable();

            // Registration status workflow
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Internal organizer notes (not shown to team)
            $table->text('opmerkingen')->nullable();

            $table->timestamps();

            // Index for status filtering (common admin panel query)
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
