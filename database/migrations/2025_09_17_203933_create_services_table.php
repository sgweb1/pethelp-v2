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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sitter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('service_categories');
            $table->string('title');
            $table->text('description');
            $table->decimal('price_per_hour', 8, 2)->nullable();
            $table->decimal('price_per_day', 8, 2)->nullable();
            $table->json('pet_types')->nullable(); // ["dog", "cat", "bird"]
            $table->json('pet_sizes')->nullable(); // ["small", "medium", "large"]
            $table->boolean('home_service')->default(false);
            $table->boolean('sitter_home')->default(false);
            $table->integer('max_pets')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['sitter_id', 'is_active']);
            $table->index(['category_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
