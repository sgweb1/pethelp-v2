<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tworzy tabelę admin_logs dla audytu aktywności administratorów.
     *
     * Loguje wszystkie akcje wykonywane przez administratorów w panelu admina
     * umożliwiając pełny audyt zmian i historię działań.
     */
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();

            // Administrator wykonujący akcję
            $table->foreignId('admin_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Administrator który wykonał akcję');

            // Typ akcji
            $table->string('action')
                ->comment('Typ akcji: created, updated, deleted, viewed, exported, itp.');

            // Model którego dotyczy akcja
            $table->string('model_type')
                ->nullable()
                ->comment('Typ modelu (klasa)');

            $table->unsignedBigInteger('model_id')
                ->nullable()
                ->comment('ID zmodyfikowanego rekordu');

            // Dane zmian (JSON)
            $table->json('old_values')
                ->nullable()
                ->comment('Poprzednie wartości pól (przed zmianą)');

            $table->json('new_values')
                ->nullable()
                ->comment('Nowe wartości pól (po zmianie)');

            // Dodatkowe informacje
            $table->text('description')
                ->nullable()
                ->comment('Dodatkowy opis akcji');

            // Informacje techniczne
            $table->ipAddress('ip_address')
                ->nullable()
                ->comment('Adres IP administratora');

            $table->text('user_agent')
                ->nullable()
                ->comment('User agent przeglądarki');

            $table->timestamps();

            // Indeksy dla wydajności
            $table->index('admin_id');
            $table->index('action');
            $table->index('model_type');
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
