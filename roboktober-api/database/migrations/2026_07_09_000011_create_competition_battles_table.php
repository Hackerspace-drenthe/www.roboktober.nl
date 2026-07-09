<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_battles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('competition_category_id')->constrained()->cascadeOnDelete();
            $table->string('naam', 120);
            $table->enum('battle_mode', ['solo', 'multi'])->default('solo');
            $table->text('omschrijving')->nullable();
            $table->unsignedSmallInteger('volgorde')->default(0);
            $table->timestamps();

            $table->index(['competition_category_id', 'volgorde']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_battles');
    }
};
