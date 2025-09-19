<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisement_categories', function (Blueprint $table) {
            $table->id();

            // Category hierarchy support
            $table->foreignId('parent_id')->nullable()->constrained('advertisement_categories')->onDelete('cascade');

            // Basic info
            $table->string('name', 100)->index();
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 7)->default('#3B82F6');

            // Category type (adoption, sales, services, etc.)
            $table->enum('type', ['adoption', 'sales', 'services', 'lost_found', 'supplies'])->index();

            // Display settings
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('requires_approval')->default(false);
            $table->boolean('allows_images')->default(true);
            $table->unsignedTinyInteger('max_images')->default(10);

            // Performance metadata
            $table->unsignedInteger('advertisement_count')->default(0);

            $table->timestamps();

            // Performance indexes
            $table->index(['type', 'is_active', 'sort_order']);
            $table->index(['parent_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisement_categories');
    }
};
