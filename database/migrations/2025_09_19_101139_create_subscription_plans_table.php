<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Basic, Pro, Premium, Business
            $table->string('slug')->unique(); // basic, pro, premium, business
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2); // Cena w PLN
            $table->string('billing_period')->default('monthly'); // monthly, yearly
            $table->integer('max_listings')->nullable(); // null = unlimited
            $table->json('features'); // Lista features dla planu
            $table->boolean('is_popular')->default(false); // Wyróżnienie na stronie pricing
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};