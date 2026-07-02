<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the robots table.
 *
 * Stores combat robot registrations, linked to a team.
 * Photos stored via mediables 'foto' collection.
 * STL / CAD files stored via mediables 'bijlagen' collection.
 *
 * Weight classes: antweight (up to 150g), beetleweight (up to 1.36 kg),
 * featherweight (up to 13.6 kg). Defined in PLAN.md §5.1.
 *
 * @see PLAN.md §5.2 — robots schema
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('robots', function (Blueprint $table): void {
            $table->id();

            // Which team owns this robot
            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();

            // Robot name (e.g. "Wallie", "Destroyerbot 3000")
            $table->string('naam');

            // Combat weight class — determines which arena/bracket the robot enters
            $table->enum('gewichtsklasse', ['antweight', 'beetleweight', 'featherweight']);

            // Optional build description / story (shown on robot profile page)
            $table->text('beschrijving')->nullable();

            // Build/readiness status for event planning
            $table->enum('status', [
                'in_ontwikkeling', // Under construction
                'gereed',          // Built and tested
                'battle_ready',    // Weighed in and approved for battle
            ])->default('in_ontwikkeling');

            $table->timestamps();

            // Index for weight class bracket filtering
            $table->index('gewichtsklasse');
            $table->index(['team_id', 'gewichtsklasse']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('robots');
    }
};
