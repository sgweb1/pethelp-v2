<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uruchamia migrację - tworzy tabelę faktur.
     *
     * Przechowuje faktury wygenerowane przez system dla:
     * - Płatności za zlecenia (booking payments)
     * - Płatności za subskrypcje (subscription payments)
     * - Prowizje platformy (platform commissions)
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Powiązania
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Nabywca
            $table->foreignId('issuer_id')->nullable()->constrained('users')->nullOnDelete(); // Wystawca (dla sitterów)

            // Dane faktury
            $table->string('invoice_number')->unique(); // Numer faktury (np. PETHELP/2025/001)
            $table->enum('invoice_type', ['vat', 'proforma', 'correction', 'receipt'])->default('vat');
            $table->enum('status', ['draft', 'issued', 'sent', 'paid', 'cancelled', 'overdue'])->default('draft');

            // Daty
            $table->date('issue_date'); // Data wystawienia
            $table->date('sale_date'); // Data sprzedaży
            $table->date('payment_due_date'); // Termin płatności
            $table->date('paid_date')->nullable(); // Data zapłaty

            // Kwoty (w groszach dla precyzji)
            $table->decimal('net_amount', 10, 2); // Kwota netto
            $table->decimal('tax_amount', 10, 2); // Kwota VAT
            $table->decimal('gross_amount', 10, 2); // Kwota brutto
            $table->decimal('paid_amount', 10, 2)->default(0); // Zapłacono
            $table->string('currency', 3)->default('PLN');

            // Dane nabywcy (buyer)
            $table->string('buyer_name');
            $table->string('buyer_tax_id')->nullable(); // NIP
            $table->string('buyer_address')->nullable();
            $table->string('buyer_postal_code')->nullable();
            $table->string('buyer_city')->nullable();
            $table->string('buyer_country')->default('Polska');
            $table->string('buyer_email')->nullable();

            // Dane wystawcy (seller) - jeśli sitter wystawia fakturę
            $table->string('seller_name')->nullable();
            $table->string('seller_tax_id')->nullable();
            $table->string('seller_address')->nullable();
            $table->string('seller_postal_code')->nullable();
            $table->string('seller_city')->nullable();
            $table->string('seller_country')->nullable();

            // Pozycje faktury (jako JSON)
            $table->json('line_items'); // Pozycje faktury

            // Metoda płatności
            $table->string('payment_method')->nullable(); // transfer, card, blik, cash

            // Notatki
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Integracja z inFakt
            $table->unsignedBigInteger('infakt_id')->nullable(); // ID faktury w inFakt
            $table->string('infakt_number')->nullable(); // Numer faktury w inFakt
            $table->text('infakt_pdf_url')->nullable(); // URL do PDF w inFakt
            $table->longText('infakt_pdf_content')->nullable(); // Base64 PDF content
            $table->json('infakt_response')->nullable(); // Pełna odpowiedź z inFakt API

            // Pliki
            $table->string('pdf_path')->nullable(); // Ścieżka do lokalnego PDF

            // Audyt
            $table->timestamp('sent_at')->nullable(); // Kiedy wysłano
            $table->timestamp('cancelled_at')->nullable(); // Kiedy anulowano
            $table->string('cancelled_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indeksy
            $table->index('invoice_number');
            $table->index('status');
            $table->index('user_id');
            $table->index('payment_id');
            $table->index('booking_id');
            $table->index('issue_date');
            $table->index('infakt_id');
        });
    }

    /**
     * Cofa migrację - usuwa tabelę faktur.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
