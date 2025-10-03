<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dodaje pole surface_area (powierzchnia w km²) do tabeli population_data.
     *
     * Umożliwia dokładne obliczanie gęstości zaludnienia i estymację
     * populacji w promieniu bez względu na granice powiatów.
     */
    public function up(): void
    {
        Schema::table('population_data', function (Blueprint $table) {
            // Powierzchnia jednostki terytorialnej w km²
            $table->decimal('surface_area', 10, 2)->nullable()->after('population');

            $table->index('surface_area');
        });
    }

    /**
     * Usuwa pole surface_area z tabeli.
     */
    public function down(): void
    {
        Schema::table('population_data', function (Blueprint $table) {
            $table->dropIndex(['surface_area']);
            $table->dropColumn('surface_area');
        });
    }
};
