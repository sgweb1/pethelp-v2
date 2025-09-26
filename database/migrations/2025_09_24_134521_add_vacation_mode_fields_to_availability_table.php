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
            // Dodaj tylko kolumny, które nie istnieją
            if (!Schema::hasColumn('availability', 'time_slot')) {
                $table->string('time_slot')->default('custom')->after('is_available');
            }
            if (!Schema::hasColumn('availability', 'available_services')) {
                $table->json('available_services')->nullable()->after('time_slot');
            }
            if (!Schema::hasColumn('availability', 'vacation_end_date')) {
                $table->date('vacation_end_date')->nullable()->after('available_services');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability', function (Blueprint $table) {
            $table->dropColumn(['time_slot', 'available_services', 'vacation_end_date']);
        });
    }
};
