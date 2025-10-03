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
        Schema::table('reviews', function (Blueprint $table) {
            // Status moderacji (pending, approved, rejected)
            $table->enum('moderation_status', ['pending', 'approved', 'rejected'])
                ->default('approved')
                ->after('is_visible')
                ->comment('Status moderacji recenzji');

            // Odpowiedź administratora na recenzję
            $table->text('admin_response')
                ->nullable()
                ->after('moderation_status')
                ->comment('Odpowiedź administratora na recenzję');

            // Kto moderował
            $table->foreignId('moderated_by')
                ->nullable()
                ->after('admin_response')
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Administrator który moderował recenzję');

            // Kiedy moderowano
            $table->timestamp('moderated_at')
                ->nullable()
                ->after('moderated_by')
                ->comment('Data i czas moderacji');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropColumn([
                'moderation_status',
                'admin_response',
                'moderated_by',
                'moderated_at',
            ]);
        });
    }
};
