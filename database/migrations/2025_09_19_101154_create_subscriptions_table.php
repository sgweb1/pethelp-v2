<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->string('status'); // active, cancelled, expired, paused
            $table->decimal('price', 8, 2); // Cena w momencie zakupu (może się zmienić)
            $table->string('billing_period'); // monthly, yearly
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->string('payment_method')->nullable(); // payu, stripe, etc.
            $table->string('external_id')->nullable(); // ID w systemie płatności
            $table->json('metadata')->nullable(); // Dodatkowe dane
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'ends_at']);
            $table->index('next_billing_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};