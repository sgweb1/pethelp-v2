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
        // Check if this is a fresh installation
        $isFreshInstallation = !Schema::hasTable('locations');

        if ($isFreshInstallation) {
            // Fresh installation - create everything from scratch
            $this->createLocationsTable();
            $this->createEventTypesTable();
            $this->createEventsTable();
            $this->createEventLocationsTable();
            $this->createEventRegistrationsTable();
        } else {
            // Existing installation - ensure all tables exist
            $this->ensureLocationsTable();
            $this->ensureEventTypesTable();
            $this->ensureEventsTable();
            $this->ensureEventLocationsTable();
            $this->ensureEventRegistrationsTable();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('event_locations');
        Schema::dropIfExists('events');
        Schema::dropIfExists('event_types');
        Schema::dropIfExists('locations');
    }

    private function createLocationsTable(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('city');
            $table->string('postal_code');
            $table->string('country')->default('PL');
            $table->json('facilities')->nullable(); // ["parking", "indoor", "outdoor"]
            $table->integer('max_capacity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['city', 'is_active']);
            $table->index(['latitude', 'longitude']);
        });
    }

    private function createEventTypesTable(): void
    {
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable(); // Hex color for UI
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    private function createEventsTable(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_type_id')->constrained('event_types');
            $table->string('title');
            $table->text('description');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->integer('max_participants')->nullable();
            $table->decimal('price', 8, 2)->nullable(); // Event fee
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->text('requirements')->nullable(); // Special requirements
            $table->json('contact_info')->nullable(); // Contact details
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['organizer_id', 'status']);
            $table->index(['event_type_id', 'status']);
            $table->index(['start_date', 'status']);
            $table->index(['is_featured', 'start_date']);
        });
    }

    private function createEventLocationsTable(): void
    {
        Schema::create('event_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'is_primary']);
            $table->unique(['event_id', 'location_id']);
        });
    }

    private function createEventRegistrationsTable(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('participants_count')->default(1);
            $table->enum('status', ['registered', 'cancelled', 'attended'])->default('registered');
            $table->text('special_requests')->nullable();
            $table->json('participant_details')->nullable(); // Pet info, dietary requirements, etc.
            $table->timestamp('registered_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->unique(['event_id', 'user_id']);
        });
    }

    // Ensure methods for existing installations
    private function ensureLocationsTable(): void
    {
        if (!Schema::hasTable('locations')) {
            $this->createLocationsTable();
        }
    }

    private function ensureEventTypesTable(): void
    {
        if (!Schema::hasTable('event_types')) {
            $this->createEventTypesTable();
        }
    }

    private function ensureEventsTable(): void
    {
        if (!Schema::hasTable('events')) {
            $this->createEventsTable();
        }
    }

    private function ensureEventLocationsTable(): void
    {
        if (!Schema::hasTable('event_locations')) {
            $this->createEventLocationsTable();
        }
    }

    private function ensureEventRegistrationsTable(): void
    {
        if (!Schema::hasTable('event_registrations')) {
            $this->createEventRegistrationsTable();
        }
    }
};