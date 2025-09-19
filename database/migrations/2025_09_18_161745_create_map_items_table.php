<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship to source content
            $table->morphs('mappable'); // mappable_type, mappable_id

            // Unified location data - REQUIRED for ALL items (map display)
            $table->decimal('latitude', 10, 8)->index(); // REQUIRED - NOT NULL
            $table->decimal('longitude', 11, 8)->index(); // REQUIRED - NOT NULL
            $table->string('city', 100)->index(); // REQUIRED - NOT NULL
            $table->string('voivodeship', 50)->index(); // REQUIRED - NOT NULL
            $table->string('full_address', 500); // REQUIRED for precise location

            // Unified display data for map pins/cards
            $table->string('title', 255)->index();
            $table->text('description_short'); // Max 300 chars for map cards
            $table->string('primary_image_url', 500)->nullable();

            // Unified categorization
            $table->enum('content_type', [
                'event',           // Wydarzenia/spotkania
                'adoption',        // Adopcja zwierząt
                'sale',           // Sprzedaż zwierząt
                'lost_pet',       // Zaginione zwierzęta
                'found_pet',      // Znalezione zwierzęta
                'supplies',       // Akcesoria/karma
                'service',        // Usługi profesjonalne
            ])->index();

            $table->string('category_name', 100)->index(); // Human readable category
            $table->string('category_icon', 50)->nullable();
            $table->string('category_color', 7)->default('#3B82F6');

            // Unified pricing (optional)
            $table->decimal('price_from', 10, 2)->nullable()->index();
            $table->decimal('price_to', 10, 2)->nullable();
            $table->string('currency', 3)->default('PLN');
            $table->boolean('price_negotiable')->default(false);

            // Unified status for map filtering
            $table->enum('status', ['draft', 'pending', 'published', 'completed', 'expired', 'cancelled'])->default('published')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_urgent')->default(false)->index();

            // Time-based data for map filtering
            $table->datetime('starts_at')->nullable()->index(); // Events
            $table->datetime('ends_at')->nullable()->index();   // Events/sales
            $table->datetime('expires_at')->nullable()->index(); // All content

            // Performance counters for popularity sorting
            $table->unsignedInteger('view_count')->default(0)->index();
            $table->unsignedInteger('interaction_count')->default(0); // contacts, registrations, etc.
            $table->decimal('rating_avg', 3, 2)->default(0.00)->index();
            $table->unsignedInteger('rating_count')->default(0);

            // Map-specific optimizations
            $table->unsignedSmallInteger('zoom_level_min')->default(10); // Minimum zoom to show this item
            $table->json('search_keywords')->nullable(); // Searchable keywords for filtering

            $table->timestamps();

            // Composite indexes for map performance
            $table->index(['content_type', 'status', 'created_at']);
            $table->index(['city', 'content_type', 'status']);
            $table->index(['is_featured', 'status', 'interaction_count']);
            $table->index(['status', 'starts_at', 'ends_at']); // Time-based filtering
            $table->index(['latitude', 'longitude', 'zoom_level_min']); // Geo + zoom
            $table->index(['price_from', 'price_to', 'status']); // Price filtering

            // Full-text search index
            $table->fullText(['title', 'description_short', 'category_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_items');
    }
};
