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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('experience_years')->nullable()->after('bio');
            $table->boolean('instant_booking')->default(false)->after('experience_years');
            $table->boolean('flexible_cancellation')->default(false)->after('instant_booking');
            $table->boolean('has_insurance')->default(false)->after('flexible_cancellation');
            $table->text('insurance_details')->nullable()->after('has_insurance');
            $table->json('certifications')->nullable()->after('insurance_details');
            $table->decimal('rating_average', 3, 2)->nullable()->after('certifications');
            $table->integer('reviews_count')->default(0)->after('rating_average');
            $table->integer('total_bookings')->default(0)->after('reviews_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'experience_years',
                'instant_booking',
                'flexible_cancellation',
                'has_insurance',
                'insurance_details',
                'certifications',
                'rating_average',
                'reviews_count',
                'total_bookings'
            ]);
        });
    }
};
