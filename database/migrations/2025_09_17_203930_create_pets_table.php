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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['dog', 'cat', 'bird', 'rabbit', 'other']);
            $table->string('breed')->nullable();
            $table->enum('size', ['small', 'medium', 'large']);
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->text('description')->nullable();
            $table->json('special_needs')->nullable(); // ["medication", "exercise", "diet"]
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['owner_id', 'is_active']);
            $table->index(['type', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
