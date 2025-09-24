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
        Schema::table('services', function (Blueprint $table) {
            // Advanced pricing options
            $table->decimal('price_per_visit', 8, 2)->nullable()->after('price_per_day');
            $table->decimal('price_per_week', 8, 2)->nullable()->after('price_per_visit');
            $table->decimal('price_per_month', 8, 2)->nullable()->after('price_per_week');
            $table->decimal('weekend_surcharge_percent', 5, 2)->nullable()->after('price_per_month');
            $table->decimal('holiday_surcharge_percent', 5, 2)->nullable()->after('weekend_surcharge_percent');
            $table->decimal('early_morning_surcharge_percent', 5, 2)->nullable()->after('holiday_surcharge_percent');
            $table->decimal('late_evening_surcharge_percent', 5, 2)->nullable()->after('early_morning_surcharge_percent');

            // Bulk pricing discounts
            $table->decimal('bulk_discount_threshold', 8, 2)->nullable()->after('late_evening_surcharge_percent');
            $table->decimal('bulk_discount_percent', 5, 2)->nullable()->after('bulk_discount_threshold');

            // Long-term pricing
            $table->decimal('long_term_discount_days', 5, 0)->nullable()->after('bulk_discount_percent');
            $table->decimal('long_term_discount_percent', 5, 2)->nullable()->after('long_term_discount_days');

            // Additional services pricing
            $table->json('additional_services_pricing')->nullable()->after('long_term_discount_percent');

            // Free consultation and trial
            $table->boolean('free_consultation')->default(false)->after('additional_services_pricing');
            $table->boolean('free_trial_visit')->default(false)->after('free_consultation');

            // Payment and cancellation policies
            $table->enum('payment_method', ['cash', 'transfer', 'both'])->default('both')->after('free_trial_visit');
            $table->integer('cancellation_hours')->default(24)->after('payment_method');
            $table->decimal('cancellation_fee_percent', 5, 2)->default(0)->after('cancellation_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'price_per_visit',
                'price_per_week',
                'price_per_month',
                'weekend_surcharge_percent',
                'holiday_surcharge_percent',
                'early_morning_surcharge_percent',
                'late_evening_surcharge_percent',
                'bulk_discount_threshold',
                'bulk_discount_percent',
                'long_term_discount_days',
                'long_term_discount_percent',
                'additional_services_pricing',
                'free_consultation',
                'free_trial_visit',
                'payment_method',
                'cancellation_hours',
                'cancellation_fee_percent'
            ]);
        });
    }
};
