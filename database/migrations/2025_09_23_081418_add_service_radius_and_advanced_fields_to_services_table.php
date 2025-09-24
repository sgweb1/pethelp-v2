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
            $table->integer('service_radius')->default(5)->comment('Service radius in kilometers');
            $table->boolean('allow_mixed_pet_types')->default(false)->comment('Allow dogs and cats together');
            $table->integer('minimum_duration')->nullable()->comment('Minimum service duration in minutes');
            $table->integer('maximum_duration')->nullable()->comment('Maximum service duration in minutes');
            $table->enum('price_structure', ['per_hour', 'per_visit', 'per_day', 'custom'])->default('per_hour');
            $table->boolean('requires_consultation')->default(false)->comment('Requires initial consultation');
            $table->boolean('emergency_contact')->default(false)->comment('Available for emergency contact');
            $table->integer('experience_years')->nullable()->comment('Years of experience');
            $table->boolean('insurance_coverage')->default(false)->comment('Has professional insurance');
            $table->boolean('vaccination_requirements')->default(true)->comment('Requires pet vaccinations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'service_radius',
                'allow_mixed_pet_types',
                'minimum_duration',
                'maximum_duration',
                'price_structure',
                'requires_consultation',
                'emergency_contact',
                'experience_years',
                'insurance_coverage',
                'vaccination_requirements'
            ]);
        });
    }
};
