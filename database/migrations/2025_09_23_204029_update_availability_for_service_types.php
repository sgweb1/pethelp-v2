<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('availability', function (Blueprint $table) {
            // Sprawdź czy kolumny już istnieją (mogły być dodane w innych migracjach)
            if (!Schema::hasColumn('availability', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable()->after('sitter_id');
            }
            if (!Schema::hasColumn('availability', 'service_type')) {
                $table->string('service_type')->nullable()->after('service_id'); // 'home_service', 'sitter_home', 'walking', 'overnight', 'transport'
            }
            if (!Schema::hasColumn('availability', 'time_slot')) {
                $table->string('time_slot')->default('all_day')->after('service_type'); // 'morning', 'afternoon', 'evening', 'all_day'
            }
            if (!Schema::hasColumn('availability', 'available_services')) {
                $table->json('available_services')->nullable()->after('time_slot'); // Tablica dostępnych usług dla tego slotu
            }

            // Indeksy dla lepszej wydajności (tylko jeśli nie istnieją)
            try {
                $table->index(['sitter_id', 'available_date', 'service_type']);
            } catch (\Exception $e) {
                // Indeks już istnieje
            }
            try {
                $table->index(['sitter_id', 'available_date', 'time_slot']);
            } catch (\Exception $e) {
                // Indeks już istnieje
            }

            // Foreign key constraint (tylko jeśli service_id został dodany)
            if (Schema::hasColumn('availability', 'service_id')) {
                try {
                    $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key już istnieje
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropIndex(['sitter_id', 'available_date', 'service_type']);
            $table->dropIndex(['sitter_id', 'available_date', 'time_slot']);
            $table->dropColumn(['service_id', 'service_type', 'time_slot', 'available_services']);
        });
    }
};
