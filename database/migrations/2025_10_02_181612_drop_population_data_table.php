<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Usuwa starą tabelę population_data.
     *
     * Tabela była używana w poprzednim systemie estymacji opartym
     * na jednostkach administracyjnych (gminy/powiaty). Została
     * zastąpiona przez population_grid wykorzystujący siatki 1km².
     */
    public function up(): void
    {
        Schema::dropIfExists('population_data');
    }

    /**
     * Przywraca tabelę population_data.
     */
    public function down(): void
    {
        Schema::create('population_data', function (Blueprint $table) {
            $table->id();
            $table->string('teryt_code', 20)->unique()->comment('Kod TERYT jednostki GUS');
            $table->string('city_name')->nullable()->comment('Nazwa miasta/gminy');
            $table->unsignedInteger('population')->default(0)->comment('Liczba ludności');
            $table->decimal('surface_area', 10, 2)->nullable()->comment('Powierzchnia w km²');
            $table->timestamp('last_updated_at')->nullable()->comment('Kiedy dane zostały ostatnio zaktualizowane');
            $table->timestamps();

            // Indeksy
            $table->index('city_name');
            $table->index('last_updated_at');
        });
    }
};
