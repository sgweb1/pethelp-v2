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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade'); // who wrote review
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade'); // who is being reviewed
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamp('moderated_at')->nullable();
            $table->timestamps();

            $table->index(['reviewee_id', 'is_visible']);
            $table->index(['booking_id']);
            $table->unique(['booking_id', 'reviewer_id']); // one review per booking per reviewer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
