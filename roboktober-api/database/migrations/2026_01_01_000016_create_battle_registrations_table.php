<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the battle_registrations table.
 *
 * Records which robots are registered for a specific battle day,
 * including technical inspection status and approval.
 *
 * @see PLAN.md §5.2 — battle_registrations schema
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('battle_registrations', function (Blueprint $table): void {
            $table->id();

            // Which robot is registering for this battle
            $table->foreignId('robot_id')
                ->constrained('robots')
                ->cascadeOnDelete();

            // Date of the battle event
            $table->date('datum');

            // Has the robot passed technical inspection (weight, weapon, safety)?
            $table->boolean('technische_check')->default(false);

            // Has the organizer approved this registration?
            $table->boolean('approved')->default(false);

            // Notes from technical inspection or organizer
            $table->text('opmerkingen')->nullable();

            $table->timestamps();

            // A robot can only register once per battle date
            $table->unique(['robot_id', 'datum']);

            // Index for event-day queries
            $table->index('datum');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('battle_registrations');
    }
};
