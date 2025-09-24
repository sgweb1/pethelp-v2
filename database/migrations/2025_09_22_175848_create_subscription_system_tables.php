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
        $isFreshInstallation = !Schema::hasTable('subscription_plans');

        if ($isFreshInstallation) {
            // Fresh installation - create everything from scratch
            $this->createSubscriptionPlansTable();
            $this->createSubscriptionsTable();
        } else {
            // Existing installation - ensure all tables exist
            $this->ensureSubscriptionPlansTable();
            $this->ensureSubscriptionsTable();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }

    private function createSubscriptionPlansTable(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('currency', 3)->default('PLN');
            $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly');
            $table->json('features'); // List of features included
            $table->integer('max_listings')->nullable(); // Null = unlimited
            $table->integer('max_photos_per_listing')->default(10);
            $table->boolean('featured_listings')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('analytics_access')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false); // Highlight on pricing page
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    private function createSubscriptionsTable(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained();
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->enum('status', ['active', 'inactive', 'cancelled', 'past_due', 'unpaid'])->default('inactive');
            $table->datetime('current_period_start');
            $table->datetime('current_period_end');
            $table->datetime('trial_ends_at')->nullable();
            $table->datetime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('PLN');
            $table->json('metadata')->nullable(); // Additional subscription data
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['subscription_plan_id', 'status']);
            $table->index(['status', 'current_period_end']);
            $table->index('stripe_subscription_id');
        });
    }

    // Ensure methods for existing installations
    private function ensureSubscriptionPlansTable(): void
    {
        if (!Schema::hasTable('subscription_plans')) {
            $this->createSubscriptionPlansTable();
        }
    }

    private function ensureSubscriptionsTable(): void
    {
        if (!Schema::hasTable('subscriptions')) {
            $this->createSubscriptionsTable();
        }
    }
};