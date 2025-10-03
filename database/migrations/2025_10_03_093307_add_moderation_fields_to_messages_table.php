<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dodaje pola moderacyjne do tabeli messages.
     *
     * Umożliwia administratorom ukrywanie niewłaściwych wiadomości
     * z zapisem powodu i informacji o moderatorze.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_hidden')
                ->default(false)
                ->after('read_at')
                ->comment('Czy wiadomość została ukryta przez moderatora');

            $table->text('hidden_reason')
                ->nullable()
                ->after('is_hidden')
                ->comment('Powód ukrycia wiadomości');

            $table->foreignId('hidden_by')
                ->nullable()
                ->after('hidden_reason')
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Administrator który ukrył wiadomość');

            $table->timestamp('hidden_at')
                ->nullable()
                ->after('hidden_by')
                ->comment('Data i czas ukrycia wiadomości');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hidden_by');
            $table->dropColumn(['is_hidden', 'hidden_reason', 'hidden_at']);
        });
    }
};
