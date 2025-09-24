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
        $isFreshInstallation = !Schema::hasTable('service_categories');

        if ($isFreshInstallation) {
            // Fresh installation - create everything from scratch
            $this->createServiceCategoriesTable();
            $this->createServicesTable();
            $this->createBookingsTable();
            $this->createReviewsTable();
            $this->createPaymentsTable();
            $this->createAvailabilityTable();
        } else {
            // Existing installation - ensure all tables exist
            $this->ensureServiceCategoriesTable();
            $this->ensureServicesTable();
            $this->ensureBookingsTable();
            $this->ensureReviewsTable();
            $this->ensurePaymentsTable();
            $this->ensureAvailabilityTable();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }

    private function createServiceCategoriesTable(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    private function createServicesTable(): void
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

    private function createBookingsTable(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sitter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'status']);
            $table->index(['sitter_id', 'status']);
            $table->index(['status', 'start_date']);
        });
    }

    private function createReviewsTable(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['reviewee_id', 'is_visible']);
            $table->index(['reviewer_id']);
            $table->unique('booking_id'); // One review per booking
        });
    }

    private function createPaymentsTable(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('payment_method'); // 'card', 'transfer', 'cash'
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_id')->nullable()->unique();
            $table->text('payment_details')->nullable(); // JSON with payment gateway response
            $table->timestamp('paid_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index(['status', 'paid_at']);
        });
    }

    private function createAvailabilityTable(): void
    {
        Schema::create('availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sitter_id')->constrained('users')->onDelete('cascade');
            $table->date('available_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_available')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['sitter_id', 'available_date']);
            $table->index(['available_date', 'is_available']);
            $table->unique(['sitter_id', 'available_date']);
        });
    }

    // Ensure methods for existing installations
    private function ensureServiceCategoriesTable(): void
    {
        if (!Schema::hasTable('service_categories')) {
            $this->createServiceCategoriesTable();
        }
    }

    private function ensureServicesTable(): void
    {
        if (!Schema::hasTable('services')) {
            $this->createServicesTable();
        }
    }

    private function ensureBookingsTable(): void
    {
        if (!Schema::hasTable('bookings')) {
            $this->createBookingsTable();
        }
    }

    private function ensureReviewsTable(): void
    {
        if (!Schema::hasTable('reviews')) {
            $this->createReviewsTable();
        }
    }

    private function ensurePaymentsTable(): void
    {
        if (!Schema::hasTable('payments')) {
            $this->createPaymentsTable();
        }
    }

    private function ensureAvailabilityTable(): void
    {
        if (!Schema::hasTable('availability')) {
            $this->createAvailabilityTable();
        }
    }
};