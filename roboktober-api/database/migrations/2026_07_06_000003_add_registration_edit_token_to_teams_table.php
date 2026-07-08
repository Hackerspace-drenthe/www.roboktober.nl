<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->string('edit_token_hash', 64)->nullable()->unique()->after('opmerkingen');
            $table->timestamp('edit_token_expires_at')->nullable()->after('edit_token_hash');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->dropColumn(['edit_token_hash', 'edit_token_expires_at']);
        });
    }
};
