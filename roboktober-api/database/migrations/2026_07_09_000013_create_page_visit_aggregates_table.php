<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_visit_aggregates', function (Blueprint $table): void {
            $table->id();
            $table->string('page_path', 255);
            $table->timestamp('bucket_start');
            $table->unsignedInteger('visits')->default(1);
            $table->timestamps();

            $table->unique(['page_path', 'bucket_start']);
            $table->index('bucket_start');
            $table->index('page_path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_visit_aggregates');
    }
};
