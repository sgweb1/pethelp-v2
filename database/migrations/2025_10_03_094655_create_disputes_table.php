<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tworzy tabelę disputes dla systemu zgłoszeń i sporów.
     *
     * Umożliwia użytkownikom zgłaszanie problemów związanych z rezerwacjami
     * oraz administratorom zarządzanie i rozwiązywanie sporów.
     */
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();

            // Powiązania
            $table->foreignId('booking_id')
                ->nullable()
                ->constrained('bookings')
                ->onDelete('set null')
                ->comment('Rezerwacja której dotyczy spór');

            $table->foreignId('reported_by')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Użytkownik zgłaszający problem');

            $table->foreignId('against_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Użytkownik przeciwko któremu zgłoszenie');

            // Szczegóły zgłoszenia
            $table->enum('status', ['new', 'in_progress', 'resolved', 'rejected'])
                ->default('new')
                ->comment('Status zgłoszenia');

            $table->enum('category', ['cancellation', 'payment', 'behavior', 'quality', 'other'])
                ->default('other')
                ->comment('Kategoria zgłoszenia');

            $table->string('title')
                ->comment('Tytuł zgłoszenia');

            $table->text('description')
                ->comment('Szczegółowy opis problemu');

            // Zarządzanie przez admina
            $table->text('admin_notes')
                ->nullable()
                ->comment('Notatki administratora (niewidoczne dla użytkowników)');

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Administrator przypisany do sprawy');

            // Rozwiązanie
            $table->text('resolution')
                ->nullable()
                ->comment('Opis rozwiązania problemu');

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Administrator który rozwiązał spór');

            $table->timestamp('resolved_at')
                ->nullable()
                ->comment('Data i czas rozwiązania');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
