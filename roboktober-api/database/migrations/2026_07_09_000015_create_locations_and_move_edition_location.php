<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 255);
            $table->string('address', 255)->nullable();
            $table->string('place', 255)->nullable();
            $table->string('zipcode', 32)->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();

            $table->index(['name', 'place']);
        });

        Schema::table('editions', function (Blueprint $table): void {
            $table->foreignId('location_id')->nullable()->after('omschrijving')->constrained('locations')->restrictOnDelete();
        });

        $editions = DB::table('editions')->select(['id', 'locatie'])->get();

        foreach ($editions as $edition) {
            $legacyLocation = is_string($edition->locatie) ? trim($edition->locatie) : '';

            $locationId = DB::table('locations')
                ->where('name', $legacyLocation !== '' ? $legacyLocation : 'Onbekende locatie')
                ->whereNull('address')
                ->whereNull('place')
                ->whereNull('zipcode')
                ->whereNull('instructions')
                ->value('id');

            if (! is_int($locationId)) {
                $locationId = (int) DB::table('locations')->insertGetId([
                    'name' => $legacyLocation !== '' ? $legacyLocation : 'Onbekende locatie',
                    'address' => null,
                    'place' => null,
                    'zipcode' => null,
                    'instructions' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('editions')
                ->where('id', $edition->id)
                ->update(['location_id' => $locationId]);
        }

        Schema::table('editions', function (Blueprint $table): void {
            $table->unsignedBigInteger('location_id')->nullable(false)->change();
            $table->dropColumn('locatie');
        });
    }

    public function down(): void
    {
        Schema::table('editions', function (Blueprint $table): void {
            $table->string('locatie', 255)->nullable()->after('omschrijving');
        });

        $editions = DB::table('editions')
            ->leftJoin('locations', 'editions.location_id', '=', 'locations.id')
            ->select(['editions.id', 'locations.name'])
            ->get();

        foreach ($editions as $edition) {
            $legacyLocation = is_string($edition->name) && trim($edition->name) !== ''
                ? trim($edition->name)
                : 'Onbekende locatie';

            DB::table('editions')
                ->where('id', $edition->id)
                ->update(['locatie' => $legacyLocation]);
        }

        Schema::table('editions', function (Blueprint $table): void {
            $table->string('locatie', 255)->nullable(false)->change();
            $table->dropConstrainedForeignId('location_id');
        });

        Schema::dropIfExists('locations');
    }
};
