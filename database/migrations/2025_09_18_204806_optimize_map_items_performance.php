<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('map_items', function (Blueprint $table) {
            // Kompozytowy indeks dla polimorficznych związków
            $table->index(['mappable_type', 'mappable_id'], 'idx_map_items_mappable');

            // Indeks geograficzny dla zapytań przestrzennych
            $table->index(['latitude', 'longitude'], 'idx_map_items_coordinates');

            // Indeks dla aktywnych elementów z sortowaniem
            $table->index(['status', 'created_at'], 'idx_map_items_status_date');

            // Indeks dla wyszukiwania tekstowego
            $table->index(['title'], 'idx_map_items_title');

            // Indeks dla filtrowania po typie treści
            $table->index(['content_type'], 'idx_map_items_content_type');

            // Indeks dla geolokalizacji z typem treści
            $table->index(['content_type', 'latitude', 'longitude'], 'idx_map_items_geo_content');

            // Indeks dla wyróżnionych i pilnych
            $table->index(['is_featured', 'is_urgent'], 'idx_map_items_featured_urgent');
        });

        // Optymalizacja tabeli
        DB::statement('OPTIMIZE TABLE map_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('map_items', function (Blueprint $table) {
            $table->dropIndex('idx_map_items_mappable');
            $table->dropIndex('idx_map_items_coordinates');
            $table->dropIndex('idx_map_items_status_date');
            $table->dropIndex('idx_map_items_title');
            $table->dropIndex('idx_map_items_content_type');
            $table->dropIndex('idx_map_items_geo_content');
            $table->dropIndex('idx_map_items_featured_urgent');
        });
    }
};
