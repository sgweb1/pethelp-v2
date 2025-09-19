<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('advertisement_category_id')->constrained()->onDelete('restrict');

            // Basic advertisement info
            $table->string('title', 255)->index();
            $table->text('description');
            $table->decimal('price', 10, 2)->nullable()->index();
            $table->string('currency', 3)->default('PLN');
            $table->boolean('price_negotiable')->default(false);

            // Location data
            $table->string('city', 100)->index();
            $table->string('voivodeship', 50)->index(); // województwo
            $table->string('full_address', 500)->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->index();
            $table->decimal('longitude', 11, 8)->nullable()->index();

            // Pet-specific data (for adoption/sales)
            $table->string('pet_name', 100)->nullable()->index();
            $table->enum('pet_type', ['dog', 'cat', 'bird', 'rabbit', 'other'])->nullable()->index();
            $table->string('pet_breed', 100)->nullable();
            $table->enum('pet_gender', ['male', 'female'])->nullable();
            $table->date('pet_birth_date')->nullable();
            $table->decimal('pet_weight', 5, 2)->nullable();
            $table->boolean('pet_vaccinated')->nullable();
            $table->boolean('pet_sterilized')->nullable();
            $table->text('pet_health_info')->nullable();

            // Advertisement settings
            $table->enum('status', ['draft', 'pending', 'published', 'sold', 'expired', 'rejected'])->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_urgent')->default(false)->index();
            $table->datetime('expires_at')->nullable()->index();

            // Contact preferences
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('show_phone')->default(true);
            $table->boolean('show_email')->default(false);
            $table->enum('preferred_contact', ['phone', 'email', 'both'])->default('phone');

            // Moderation
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Performance counters
            $table->unsignedInteger('view_count')->default(0)->index();
            $table->unsignedInteger('contact_count')->default(0);
            $table->unsignedInteger('favorite_count')->default(0);

            $table->timestamps();

            // Performance indexes
            $table->index(['status', 'created_at']);
            $table->index(['advertisement_category_id', 'status', 'created_at']);
            $table->index(['city', 'status']);
            $table->index(['pet_type', 'status']);
            $table->index(['price', 'status']);
            $table->index(['is_featured', 'status', 'created_at']);
            $table->index(['expires_at', 'status']);
            $table->index(['user_id', 'status']);

            // Spatial index for location queries
            $table->index(['latitude', 'longitude'], 'spatial_coordinates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
