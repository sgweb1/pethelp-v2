<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisement_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->onDelete('cascade');

            // Image storage info
            $table->string('filename', 255);
            $table->string('original_filename', 255);
            $table->string('path', 500);
            $table->string('disk', 50)->default('public');

            // Image metadata
            $table->unsignedInteger('file_size'); // bytes
            $table->string('mime_type', 100);
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();

            // Optimization variants
            $table->json('variants')->nullable(); // thumbnails, webp versions, etc.

            // Display settings
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->text('alt_text')->nullable();

            // Performance metadata
            $table->unsignedInteger('view_count')->default(0);

            $table->timestamps();

            // Performance indexes
            $table->index(['advertisement_id', 'sort_order']);
            $table->index(['advertisement_id', 'is_primary']);
            $table->index(['created_at']); // For cleanup of orphaned images
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisement_images');
    }
};
