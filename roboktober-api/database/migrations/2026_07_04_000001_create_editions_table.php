<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editions', function (Blueprint $table): void {
            $table->id();
            $table->string('naam');
            $table->text('omschrijving')->nullable();
            $table->string('locatie', 255);
            $table->string('afbeelding')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->boolean('is_done')->default(false);
            $table->timestamps();

            $table->index(['is_done', 'start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};
