<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if this is a fresh installation
        $isFreshInstallation = !Schema::hasTable('advertisement_categories');

        if ($isFreshInstallation) {
            // Fresh installation - create everything from scratch
            $this->createAdvertisementCategoriesTable();
            $this->createAdvertisementsTable();
            $this->createAdvertisementImagesTable();
            $this->createProfessionalServicesTable();
            $this->createMapItemsTable();
        } else {
            // Existing installation - ensure all tables exist
            $this->ensureAdvertisementCategoriesTable();
            $this->ensureAdvertisementsTable();
            $this->ensureAdvertisementImagesTable();
            $this->ensureProfessionalServicesTable();
            $this->ensureMapItemsTable();
            $this->ensureMapItemsUpdates();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_items');
        Schema::dropIfExists('professional_services');
        Schema::dropIfExists('advertisement_images');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('advertisement_categories');
    }

    private function createAdvertisementCategoriesTable(): void
    {
        Schema::create('advertisement_categories', function (Blueprint $table) {
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

    private function createAdvertisementsTable(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('advertisement_categories');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 3)->default('PLN');
            $table->enum('condition', ['new', 'used', 'refurbished'])->nullable();
            $table->string('location');
            $table->json('contact_info')->nullable();
            $table->enum('status', ['draft', 'active', 'sold', 'expired', 'removed'])->default('draft');
            $table->datetime('expires_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['is_featured', 'status']);
        });
    }

    private function createAdvertisementImagesTable(): void
    {
        Schema::create('advertisement_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['advertisement_id', 'sort_order']);
        });
    }

    private function createProfessionalServicesTable(): void
    {
        Schema::create('professional_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->text('description');
            $table->json('service_types'); // ["veterinary", "grooming", "training"]
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('address'); // Full address object
            $table->json('operating_hours'); // Business hours
            $table->decimal('rating_average', 3, 2)->nullable();
            $table->integer('reviews_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['is_verified', 'is_active']);
        });
    }

    private function createMapItemsTable(): void
    {
        Schema::create('map_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('mappable_type'); // "App\Models\Service", "App\Models\Event", etc.
            $table->bigInteger('mappable_id'); // ID of the related model
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('city');
            $table->string('voivodeship');
            $table->string('full_address');
            $table->string('title');
            $table->text('description_short')->nullable();
            $table->string('content_type'); // "service", "event", "advertisement", "pet_sitter"
            $table->string('category_name')->nullable();
            $table->string('category_icon')->nullable();
            $table->string('category_color')->nullable();
            $table->decimal('price_from', 10, 2)->nullable();
            $table->string('currency', 3)->default('PLN');
            $table->enum('status', ['draft', 'published', 'suspended', 'expired'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->decimal('rating_avg', 3, 2)->nullable();
            $table->integer('rating_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->string('primary_image_url')->nullable();
            $table->datetime('published_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->timestamps();

            // Optimized indexes for performance
            $table->index(['status', 'published_at']);
            $table->index(['content_type', 'status']);
            $table->index(['city', 'status']);
            $table->index(['voivodeship', 'status']);
            $table->index(['latitude', 'longitude', 'status']);
            $table->index(['is_featured', 'status', 'published_at']);
            $table->index(['is_urgent', 'status', 'published_at']);
            $table->index(['user_id', 'status']);
            $table->index(['mappable_type', 'mappable_id']);

            // Full-text search index
            $table->fullText(['title', 'description_short', 'category_name'], 'map_items_search_index');

            // Composite indexes for common query patterns
            $table->index(['status', 'content_type', 'city']);
            $table->index(['status', 'is_featured', 'published_at']);
            $table->index(['latitude', 'longitude', 'status', 'content_type'], 'idx_bounds_filter');
        });
    }

    // Ensure methods for existing installations
    private function ensureAdvertisementCategoriesTable(): void
    {
        if (!Schema::hasTable('advertisement_categories')) {
            $this->createAdvertisementCategoriesTable();
        }
    }

    private function ensureAdvertisementsTable(): void
    {
        if (!Schema::hasTable('advertisements')) {
            $this->createAdvertisementsTable();
        }
    }

    private function ensureAdvertisementImagesTable(): void
    {
        if (!Schema::hasTable('advertisement_images')) {
            $this->createAdvertisementImagesTable();
        }
    }

    private function ensureProfessionalServicesTable(): void
    {
        if (!Schema::hasTable('professional_services')) {
            $this->createProfessionalServicesTable();
        }
    }

    private function ensureMapItemsTable(): void
    {
        if (!Schema::hasTable('map_items')) {
            $this->createMapItemsTable();
        }
    }

    private function ensureMapItemsUpdates(): void
    {
        // Apply any updates that were in separate migrations

        // Update content types for pet sitters (from update_map_items_content_types_for_pet_sitters)
        if (Schema::hasTable('map_items')) {
            DB::statement("
                UPDATE map_items
                SET content_type = 'pet_sitter'
                WHERE mappable_type = 'App\\Models\\Service'
                AND content_type IN ('service', 'sitter')
            ");
        }

        // Ensure optimized indexes exist (from optimize_map_items_indexes)
        Schema::table('map_items', function (Blueprint $table) {
            // Add indexes if they don't exist (Laravel will skip if they exist)
            try {
                if (!$this->indexExists('map_items', 'idx_bounds_filter')) {
                    $table->index(['latitude', 'longitude', 'status', 'content_type'], 'idx_bounds_filter');
                }
                if (!$this->indexExists('map_items', 'map_items_search_index')) {
                    $table->fullText(['title', 'description_short', 'category_name'], 'map_items_search_index');
                }
            } catch (\Exception $e) {
                // Indexes might already exist, continue
            }
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
        return count($indexes) > 0;
    }
};