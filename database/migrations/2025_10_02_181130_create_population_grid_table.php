<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tworzy tabelę dla gridów populacyjnych (siatka 1km²).
     *
     * Gridy pochodzą z Eurostat GEOSTAT i zawierają dane o ludności
     * w kratkach 1km × 1km dla całej Polski/Europy.
     */
    public function up(): void
    {
        Schema::create('population_grid', function (Blueprint $table) {
            $table->id();
            $table->string('grid_id', 50)->unique()->comment('Identyfikator kratki (np. 1kmN5400E2100)');
            $table->decimal('latitude', 10, 7)->comment('Szerokość geograficzna środka kratki');
            $table->decimal('longitude', 10, 7)->comment('Długość geograficzna środka kratki');
            $table->unsignedInteger('population')->default(0)->comment('Liczba ludności w kratce');
            $table->unsignedSmallInteger('year')->default(2024)->comment('Rok danych');
            $table->timestamps();

            // Indeksy dla szybkiego wyszukiwania spatial
            $table->index(['latitude', 'longitude'], 'idx_coordinates');
            $table->index('population', 'idx_population');
            $table->index('year', 'idx_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('population_grid');
    }
};
