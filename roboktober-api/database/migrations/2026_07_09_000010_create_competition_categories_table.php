<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('edition_id')->constrained()->cascadeOnDelete();
            $table->string('naam', 120);
            $table->string('slug', 140);
            $table->text('omschrijving')->nullable();
            $table->unsignedSmallInteger('volgorde')->default(0);
            $table->timestamps();

            $table->unique(['edition_id', 'slug']);
            $table->index(['edition_id', 'volgorde']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_categories');
    }
};
