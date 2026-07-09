<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_battles', function (Blueprint $table): void {
            $table->timestamp('scheduled_at')->nullable()->after('omschrijving');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('competition_battles', function (Blueprint $table): void {
            $table->dropIndex(['scheduled_at']);
            $table->dropColumn('scheduled_at');
        });
    }
};
