<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professional_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('advertisement_category_id')->constrained()->onDelete('restrict');

            // Service basic info
            $table->string('business_name', 200)->index();
            $table->string('contact_person', 100)->nullable();
            $table->text('description');
            $table->text('services_offered'); // JSON or comma-separated

            // Pricing and availability
            $table->decimal('base_price', 8, 2)->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->string('currency', 3)->default('PLN');
            $table->json('pricing_details')->nullable(); // Flexible pricing structure
            $table->json('availability')->nullable(); // Days/hours availability

            // Location and service area
            $table->string('city', 100)->index();
            $table->string('voivodeship', 50)->index();
            $table->string('full_address', 500)->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->index();
            $table->decimal('longitude', 11, 8)->nullable()->index();
            $table->unsignedSmallInteger('service_radius_km')->default(25)->index(); // How far they travel

            // Contact info
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('social_media')->nullable(); // Facebook, Instagram, etc.

            // Professional credentials
            $table->json('certifications')->nullable(); // Professional certifications
            $table->json('specializations')->nullable(); // e.g., specific dog breeds, behavioral issues
            $table->unsignedTinyInteger('experience_years')->nullable();
            $table->boolean('is_insured')->default(false);
            $table->boolean('is_licensed')->default(false);

            // Service settings
            $table->enum('status', ['draft', 'pending', 'published', 'suspended', 'rejected'])->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('accepts_online_booking')->default(false);
            $table->boolean('offers_emergency_services')->default(false);

            // Moderation
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Performance counters
            $table->unsignedInteger('view_count')->default(0)->index();
            $table->unsignedInteger('contact_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00)->index();
            $table->unsignedInteger('review_count')->default(0);

            $table->timestamps();

            // Performance indexes
            $table->index(['status', 'created_at']);
            $table->index(['advertisement_category_id', 'status']);
            $table->index(['city', 'status']);
            $table->index(['is_featured', 'status', 'average_rating']);
            $table->index(['user_id', 'status']);
            $table->index(['service_radius_km', 'status']);

            // Spatial index for location-based queries
            $table->index(['latitude', 'longitude'], 'service_location_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_services');
    }
};
