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
        Schema::table('availabilities', function (Blueprint $table) {
            // Dodaj kolumny dla obsługi różnych typów usług i slotów
            $table->unsignedBigInteger('service_id')->nullable()->after('sitter_id');
            $table->string('service_type')->nullable()->after('service_id'); // 'home_service', 'sitter_home', 'walking', 'overnight', 'transport'
            $table->string('time_slot')->default('all_day')->after('service_type'); // 'morning', 'afternoon', 'evening', 'all_day'
            $table->json('available_services')->nullable()->after('time_slot'); // Tablica dostępnych usług dla tego slotu

            // Indeksy dla lepszej wydajności
            $table->index(['sitter_id', 'date', 'service_type']);
            $table->index(['sitter_id', 'date', 'time_slot']);

            // Foreign key constraint
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropIndex(['sitter_id', 'date', 'service_type']);
            $table->dropIndex(['sitter_id', 'date', 'time_slot']);
            $table->dropColumn(['service_id', 'service_type', 'time_slot', 'available_services']);
        });
    }
};
