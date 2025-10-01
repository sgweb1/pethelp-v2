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
            // Krok 5: Lokalizacja
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('service_radius')->default(10);

            // Krok 6: Dostępność
            $table->json('weekly_availability')->nullable();
            $table->boolean('emergency_available')->default(false);
            $table->boolean('flexible_schedule')->default(true);

            // Krok 7: Dom i środowisko
            $table->string('home_type')->nullable();
            $table->boolean('has_garden')->default(false);
            $table->boolean('is_smoking')->default(false);
            $table->boolean('has_other_pets')->default(false);
            $table->json('other_pets')->nullable();

            // Krok 8: Zdjęcia
            $table->json('home_photos')->nullable();

            // Krok 9: Weryfikacja
            $table->json('verification_documents')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->nullable();

            // Krok 10: Cennik
            $table->enum('pricing_strategy', ['budget', 'competitive', 'premium'])->default('competitive');

            // Krok 11: Zgody
            $table->boolean('marketing_consent')->default(false);

            // Metadane pet sittera
            $table->json('pets_experience')->nullable(); // typ zwierząt i doświadczenie
            $table->timestamp('sitter_activated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'service_radius',
                'weekly_availability',
                'emergency_available',
                'flexible_schedule',
                'home_type',
                'has_garden',
                'is_smoking',
                'has_other_pets',
                'other_pets',
                'home_photos',
                'verification_documents',
                'verification_status',
                'pricing_strategy',
                'marketing_consent',
                'pets_experience',
                'sitter_activated_at',
            ]);
        });
    }
};
