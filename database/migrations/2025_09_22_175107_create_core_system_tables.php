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
        $isFreshInstallation = !Schema::hasTable('users');

        if ($isFreshInstallation) {
            // Fresh installation - create everything from scratch
            $this->createUsersTable();
            $this->createUserProfilesTable();
            $this->createSessionsTable();
            $this->createCacheTable();
        } else {
            // Existing installation - ensure all tables and fields exist
            $this->ensureUsersTable();
            $this->ensureUserProfilesTable();
            $this->ensureSessionsTable();
            $this->ensureCacheTable();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('users');
    }

    private function createUsersTable(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    private function createUserProfilesTable(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['owner', 'sitter', 'admin']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->integer('experience_years')->nullable();
            $table->boolean('instant_booking')->default(false);
            $table->boolean('flexible_cancellation')->default(false);
            $table->boolean('has_insurance')->default(false);
            $table->text('insurance_details')->nullable();
            $table->json('certifications')->nullable();
            $table->decimal('rating_average', 3, 2)->nullable();
            $table->integer('reviews_count')->default(0);
            $table->integer('total_bookings')->default(0);
            $table->string('avatar')->nullable();
            $table->json('address')->nullable(); // {street, city, postal_code, lat, lng}
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['role', 'is_verified']);
        });
    }

    private function createSessionsTable(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    private function createCacheTable(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    private function ensureUsersTable(): void
    {
        if (!Schema::hasTable('users')) {
            $this->createUsersTable();
        }
    }

    private function ensureUserProfilesTable(): void
    {
        if (!Schema::hasTable('user_profiles')) {
            $this->createUserProfilesTable();
        } else {
            // Add missing advanced fields if they don't exist
            $this->ensureAdvancedProfileFields();
        }
    }

    private function ensureAdvancedProfileFields(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'experience_years')) {
                $table->integer('experience_years')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('user_profiles', 'instant_booking')) {
                $table->boolean('instant_booking')->default(false)->after('experience_years');
            }
            if (!Schema::hasColumn('user_profiles', 'flexible_cancellation')) {
                $table->boolean('flexible_cancellation')->default(false)->after('instant_booking');
            }
            if (!Schema::hasColumn('user_profiles', 'has_insurance')) {
                $table->boolean('has_insurance')->default(false)->after('flexible_cancellation');
            }
            if (!Schema::hasColumn('user_profiles', 'insurance_details')) {
                $table->text('insurance_details')->nullable()->after('has_insurance');
            }
            if (!Schema::hasColumn('user_profiles', 'certifications')) {
                $table->json('certifications')->nullable()->after('insurance_details');
            }
            if (!Schema::hasColumn('user_profiles', 'rating_average')) {
                $table->decimal('rating_average', 3, 2)->nullable()->after('certifications');
            }
            if (!Schema::hasColumn('user_profiles', 'reviews_count')) {
                $table->integer('reviews_count')->default(0)->after('rating_average');
            }
            if (!Schema::hasColumn('user_profiles', 'total_bookings')) {
                $table->integer('total_bookings')->default(0)->after('reviews_count');
            }
        });
    }

    private function ensureSessionsTable(): void
    {
        if (!Schema::hasTable('sessions')) {
            $this->createSessionsTable();
        }
    }

    private function ensureCacheTable(): void
    {
        if (!Schema::hasTable('cache')) {
            $this->createCacheTable();
        }
    }
};