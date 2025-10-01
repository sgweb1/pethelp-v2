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
            // Usuń unique constraint który blokuje tworzenie wielu slotów na ten sam dzień
            $table->dropUnique(['sitter_id', 'available_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability', function (Blueprint $table) {
            // Przywróć unique constraint (ale to może nie zadziałać jeśli są już duplikaty)
            $table->unique(['sitter_id', 'available_date'], 'availability_sitter_id_available_date_unique');
        });
    }
};
