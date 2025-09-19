<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_type_id')->constrained()->onDelete('restrict');

            // Basic event info
            $table->string('title', 200)->index();
            $table->text('description');
            $table->datetime('starts_at')->index();
            $table->datetime('ends_at')->nullable()->index();

            // Registration settings
            $table->unsignedSmallInteger('max_participants')->nullable();
            $table->decimal('entry_fee', 8, 2)->default(0.00);
            $table->string('currency', 3)->default('PLN');

            // Privacy and status
            $table->boolean('is_invitation_only')->default(false)->index();
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])
                ->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();

            // Registration management
            $table->datetime('registration_deadline')->nullable()->index();
            $table->boolean('allow_waiting_list')->default(true);

            // Performance metadata
            $table->unsignedInteger('current_participants')->default(0)->index();
            $table->unsignedInteger('view_count')->default(0);

            $table->timestamps();

            // Composite indexes for performance
            $table->index(['status', 'starts_at']);
            $table->index(['event_type_id', 'status', 'starts_at']);
            $table->index(['user_id', 'status']);
            $table->index(['is_featured', 'status', 'starts_at']);
            $table->index(['starts_at', 'ends_at']); // For date range queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
