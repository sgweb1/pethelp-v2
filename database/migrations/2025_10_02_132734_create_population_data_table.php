<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tworzy tabelę do przechowywania danych demograficznych z GUS.
     *
     * Dane są cache'owane w bazie i aktualizowane tylko gdy są starsze niż 6 miesięcy.
     */
    public function up(): void
    {
        Schema::create('population_data', function (Blueprint $table) {
            $table->id();

            // Kod TERYT gminy/powiatu (unikalny identyfikator jednostki terytorialnej)
            $table->string('teryt_code', 10)->unique();

            // Nazwa miejscowości/powiatu (opcjonalna, dla czytelności)
            $table->string('city_name')->nullable();

            // Liczba ludności
            $table->unsignedInteger('population');

            // Data ostatniej aktualizacji danych z API GUS
            // Jeśli starsze niż 6 miesięcy, dane zostaną odświeżone
            $table->timestamp('last_updated_at');

            // Standardowe timestampy
            $table->timestamps();

            // Indeksy dla szybszego wyszukiwania
            $table->index('teryt_code');
            $table->index('last_updated_at');
        });
    }

    /**
     * Usuwa tabelę danych demograficznych.
     */
    public function down(): void
    {
        Schema::dropIfExists('population_data');
    }
};
