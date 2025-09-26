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
            $table->boolean('is_recurring')->default(false)->after('notes');
            $table->json('recurring_days')->nullable()->after('is_recurring');
            $table->date('recurring_end_date')->nullable()->after('recurring_days');
            $table->integer('recurring_weeks')->nullable()->after('recurring_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability', function (Blueprint $table) {
            $table->dropColumn(['is_recurring', 'recurring_days', 'recurring_end_date', 'recurring_weeks']);
        });
    }
};
