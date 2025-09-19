<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');

            // Address components for flexible searching
            $table->string('full_address', 500)->index();
            $table->string('street', 200)->nullable();
            $table->string('city', 100)->index();
            $table->string('postal_code', 10)->nullable()->index();
            $table->string('country', 2)->default('PL');

            // Coordinates for spatial queries
            $table->decimal('latitude', 10, 8)->index();
            $table->decimal('longitude', 11, 8)->index();

            // Privacy-related fields
            $table->string('public_location', 200)->nullable(); // "Warszawa, Mokotów" for invitation-only
            $table->text('location_notes')->nullable();

            $table->timestamps();

            // Composite indexes
            $table->index(['city', 'country']);
            $table->index(['latitude', 'longitude'], 'spatial_coordinates');
            $table->unique('event_id'); // One location per event
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_locations');
    }
};
