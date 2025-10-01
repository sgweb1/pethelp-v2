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
        Schema::create('wizard_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('wizard_type')->default('pet_sitter'); // pet_sitter, pet_owner, etc.
            $table->integer('current_step')->default(1);
            $table->json('wizard_data'); // Wszystkie dane z wizard'a
            $table->timestamp('last_accessed_at')->useCurrent();
            $table->timestamps();

            // Jeden draft na użytkownika i typ wizard'a
            $table->unique(['user_id', 'wizard_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wizard_drafts');
    }
};
