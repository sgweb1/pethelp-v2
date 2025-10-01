<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dodaje brakujące kolumny user_one_id i user_two_id do tabeli conversations.
     * Dodaje również kolumnę booking_id dla związania rozmów z rezerwacjami.
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Sprawdzenie czy kolumny już nie istnieją
            if (! Schema::hasColumn('conversations', 'user_one_id')) {
                $table->foreignId('user_one_id')->after('id')->constrained('users')->onDelete('cascade');
            }

            if (! Schema::hasColumn('conversations', 'user_two_id')) {
                $table->foreignId('user_two_id')->after('user_one_id')->constrained('users')->onDelete('cascade');
            }

            if (! Schema::hasColumn('conversations', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->after('user_two_id')->constrained('bookings')->onDelete('set null');
            }

            // Dodanie indeksu dla wydajności (jeśli dodano kolumny)
            $table->index(['user_one_id', 'user_two_id']);
        });
    }

    /**
     * Usuwa dodane kolumny.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'booking_id')) {
                $table->dropForeign(['booking_id']);
                $table->dropColumn('booking_id');
            }

            if (Schema::hasColumn('conversations', 'user_two_id')) {
                $table->dropForeign(['user_two_id']);
                $table->dropColumn('user_two_id');
            }

            if (Schema::hasColumn('conversations', 'user_one_id')) {
                $table->dropForeign(['user_one_id']);
                $table->dropColumn('user_one_id');
            }
        });
    }
};
