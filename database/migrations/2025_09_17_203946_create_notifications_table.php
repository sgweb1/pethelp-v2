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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'booking_confirmed', 'message_received', etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // additional data like booking_id, etc.
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_important')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
