<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('robots', function (Blueprint $table): void {
            $table->decimal('awesomeness_score', 4, 2)->default(0)->after('status');
            $table->unsignedInteger('awesomeness_votes_count')->default(0)->after('awesomeness_score');
        });

        Schema::create('robot_votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('robot_id')->constrained('robots')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('stars');
            $table->timestamps();

            $table->unique(['robot_id', 'user_id']);
            $table->index('robot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('robot_votes');

        Schema::table('robots', function (Blueprint $table): void {
            $table->dropColumn(['awesomeness_score', 'awesomeness_votes_count']);
        });
    }
};
