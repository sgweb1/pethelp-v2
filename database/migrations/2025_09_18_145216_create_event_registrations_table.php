<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Registration status
            $table->enum('status', [
                'pending',      // Waiting for organizer approval (invitation-only events)
                'confirmed',    // Approved and registered
                'waiting_list', // On waiting list
                'cancelled',    // User cancelled
                'rejected',      // Organizer rejected
            ])->default('pending')->index();

            // Registration metadata
            $table->text('message')->nullable(); // User's message when registering
            $table->text('organizer_notes')->nullable(); // Private notes for organizer
            $table->datetime('registered_at')->index();
            $table->datetime('status_updated_at')->nullable();

            $table->timestamps();

            // Unique constraint
            $table->unique(['event_id', 'user_id']);

            // Performance indexes
            $table->index(['event_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['status', 'registered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
