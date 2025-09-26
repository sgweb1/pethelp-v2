<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dodaje pola związane z płatnościami subskrypcji do tabeli payments.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Sprawdź które kolumny już istnieją i dodaj tylko te, których brak
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('booking_id');
            }
            if (!Schema::hasColumn('payments', 'subscription_plan_id')) {
                $table->foreignId('subscription_plan_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('payments', 'original_amount')) {
                $table->decimal('original_amount', 8, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('payments', 'proration_credit')) {
                $table->decimal('proration_credit', 8, 2)->default(0)->after('original_amount');
            }

            // Dodaj metadata na końcu tabeli (bezpieczniejsze)
            if (!Schema::hasColumn('payments', 'metadata')) {
                $table->json('metadata')->nullable();
            }
            if (!Schema::hasColumn('payments', 'external_id')) {
                $table->string('external_id')->nullable();
            }
            if (!Schema::hasColumn('payments', 'gateway_response')) {
                $table->json('gateway_response')->nullable();
            }
            if (!Schema::hasColumn('payments', 'commission')) {
                $table->decimal('commission', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('payments', 'processed_at')) {
                $table->timestamp('processed_at')->nullable();
            }

            // Indeksy dla płatności subskrypcji (tylko jeśli kolumny istnieją i indeksy nie istnieją)
            try {
                if (Schema::hasColumn('payments', 'user_id') && Schema::hasColumn('payments', 'status')) {
                    $table->index(['user_id', 'status']);
                }
            } catch (\Exception $e) {
                // Indeks już istnieje
            }

            try {
                if (Schema::hasColumn('payments', 'subscription_plan_id') && Schema::hasColumn('payments', 'status')) {
                    $table->index(['subscription_plan_id', 'status']);
                }
            } catch (\Exception $e) {
                // Indeks już istnieje
            }

            try {
                if (Schema::hasColumn('payments', 'user_id') && Schema::hasColumn('payments', 'subscription_plan_id')) {
                    $table->index(['user_id', 'subscription_plan_id']);
                }
            } catch (\Exception $e) {
                // Indeks już istnieje
            }

            // Foreign keys (tylko jeśli kolumny istnieją)
            try {
                if (Schema::hasColumn('payments', 'user_id')) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            } catch (\Exception $e) {
                // Foreign key już istnieje
            }

            try {
                if (Schema::hasColumn('payments', 'subscription_plan_id')) {
                    $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
                }
            } catch (\Exception $e) {
                // Foreign key już istnieje
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Usuń foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['subscription_plan_id']);

            // Usuń indeksy
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['subscription_plan_id', 'status']);
            $table->dropIndex(['user_id', 'subscription_plan_id']);

            // Usuń kolumny
            $table->dropColumn([
                'user_id',
                'subscription_plan_id',
                'original_amount',
                'proration_credit',
                'metadata'
            ]);
        });
    }
};
