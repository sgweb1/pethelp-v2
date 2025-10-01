<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dodaje kolumny starts_at i ends_at do tabeli subscriptions.
     * Te kolumny są wymagane przez Subscription model jako aliasy dla current_period_start/end.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'starts_at')) {
                $table->timestamp('starts_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('subscriptions', 'ends_at')) {
                $table->timestamp('ends_at')->nullable()->after('starts_at');
            }

            if (! Schema::hasColumn('subscriptions', 'billing_period')) {
                $table->string('billing_period')->default('monthly')->after('ends_at');
            }

            if (! Schema::hasColumn('subscriptions', 'price')) {
                $table->decimal('price', 8, 2)->nullable()->after('billing_period');
            }

            if (! Schema::hasColumn('subscriptions', 'last_payment_at')) {
                $table->timestamp('last_payment_at')->nullable()->after('price');
            }

            if (! Schema::hasColumn('subscriptions', 'next_billing_at')) {
                $table->timestamp('next_billing_at')->nullable()->after('last_payment_at');
            }

            if (! Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('next_billing_at');
            }

            if (! Schema::hasColumn('subscriptions', 'external_id')) {
                $table->string('external_id')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Usuwa dodane kolumny.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $columns = ['external_id', 'payment_method', 'next_billing_at', 'last_payment_at', 'price', 'billing_period', 'ends_at', 'starts_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('subscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
