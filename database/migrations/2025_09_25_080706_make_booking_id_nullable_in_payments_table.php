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
        Schema::table('payments', function (Blueprint $table) {
            // Najpierw usuń foreign key constraint
            $table->dropForeign(['booking_id']);

            // Zrób kolumnę nullable
            $table->unsignedBigInteger('booking_id')->nullable()->change();

            // Dodaj z powrotem foreign key constraint jako nullable
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Przywróć oryginalny stan - booking_id jako wymagane
            $table->dropForeign(['booking_id']);
            $table->unsignedBigInteger('booking_id')->nullable(false)->change();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }
};
