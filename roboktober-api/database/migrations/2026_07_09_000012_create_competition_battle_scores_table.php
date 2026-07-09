<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_battle_scores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('competition_battle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('robot_id')->constrained()->cascadeOnDelete();
            $table->integer('punten')->default(0);
            $table->string('opmerkingen', 500)->nullable();
            $table->timestamps();

            $table->unique(['competition_battle_id', 'robot_id']);
            $table->index('robot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_battle_scores');
    }
};
