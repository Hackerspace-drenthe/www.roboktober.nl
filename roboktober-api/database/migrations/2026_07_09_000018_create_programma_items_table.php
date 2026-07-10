<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programma_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('edition_id')->constrained()->cascadeOnDelete();
            $table->string('titel', 255);
            $table->text('beschrijving');
            $table->string('content_format', 20)->default('html');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->unsignedInteger('volgorde')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['edition_id', 'is_published', 'start_at']);
            $table->index(['edition_id', 'volgorde']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programma_items');
    }
};
