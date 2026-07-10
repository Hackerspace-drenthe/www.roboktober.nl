<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 64);
            $table->string('visitor_hash', 64);
            $table->string('event_type', 32);
            $table->string('event_name', 120)->nullable();
            $table->string('page_path', 255)->nullable();
            $table->string('route_name', 120)->nullable();
            $table->string('referrer_path', 255)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index('occurred_at');
            $table->index('event_type');
            $table->index('page_path');
            $table->index('session_id');
            $table->index('visitor_hash');
            $table->index(['session_id', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
