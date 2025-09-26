<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\PayURestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Command do synchronizacji statusów płatności z PayU.
 *
 * Sprawdza płatności pending i aktualizuje ich statusy
 * na podstawie danych z PayU API.
 */
class SyncPayUPayments extends Command
{
    protected $signature = 'payu:sync
                            {--payment= : ID konkretnej płatności do synchronizacji}
                            {--pending-only : Synchronizuj tylko płatności pending}
                            {--dry-run : Tylko sprawdź, nie aktualizuj}';

    protected $description = 'Synchronizuje statusy płatności PayU z API';

    protected PayURestService $payuService;

    public function __construct(PayURestService $payuService)
    {
        parent::__construct();
        $this->payuService = $payuService;
    }

    public function handle(): int
    {
        $this->info('🚀 Rozpoczynam synchronizację płatności PayU...');

        // Sprawdź konkretną płatność jeśli podano ID
        if ($paymentId = $this->option('payment')) {
            return $this->syncSinglePayment((int) $paymentId);
        }

        // Pobierz płatności do synchronizacji
        $query = Payment::where('payment_method', 'payu');

        if ($this->option('pending-only')) {
            $query->where('status', 'pending');
        }

        $payments = $query->whereNotNull('gateway_response->payu_order_id')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        if ($payments->isEmpty()) {
            $this->warn('Brak płatności do synchronizacji');
            return self::SUCCESS;
        }

        $this->info("Znaleziono {$payments->count()} płatności do synchronizacji");

        $synchronized = 0;
        $updated = 0;

        foreach ($payments as $payment) {
            $result = $this->syncPayment($payment);

            if ($result['checked']) {
                $synchronized++;
            }

            if ($result['updated']) {
                $updated++;
            }

            // Pauza między żądaniami
            usleep(500000); // 0.5 sekundy
        }

        $this->info("✅ Synchronizacja zakończona:");
        $this->line("   - Sprawdzono: {$synchronized}");
        $this->line("   - Zaktualizowano: {$updated}");

        return self::SUCCESS;
    }

    protected function syncSinglePayment(int $paymentId): int
    {
        $payment = Payment::find($paymentId);

        if (!$payment) {
            $this->error("Płatność {$paymentId} nie istnieje");
            return self::FAILURE;
        }

        $this->info("Synchronizacja płatności {$paymentId}...");

        $result = $this->syncPayment($payment);

        if ($result['updated']) {
            $this->info("✅ Płatność zaktualizowana: {$result['old_status']} → {$result['new_status']}");
        } else {
            $this->info("ℹ️  Status bez zmian: {$result['current_status']}");
        }

        return self::SUCCESS;
    }

    protected function syncPayment(Payment $payment): array
    {
        $gatewayResponse = $payment->gateway_response ?? [];
        $payuOrderId = $gatewayResponse['payu_order_id'] ?? null;

        if (!$payuOrderId) {
            $this->warn("Płatność {$payment->id}: brak PayU Order ID");
            return ['checked' => false, 'updated' => false];
        }

        // Sprawdź status w PayU
        $payuStatus = $this->payuService->checkPaymentStatus($payuOrderId);

        if (!$payuStatus || !$payuStatus['success']) {
            $this->error("Płatność {$payment->id}: błąd sprawdzania PayU - " . ($payuStatus['error'] ?? 'unknown'));
            return ['checked' => false, 'updated' => false];
        }

        $currentStatus = $payment->status;
        $payuStatusName = $payuStatus['status'];

        $this->line("Payment {$payment->id}: {$currentStatus} | PayU: {$payuStatusName}");

        // Mapowanie statusów PayU na status lokalny
        $statusMap = [
            'NEW' => 'pending',
            'PENDING' => 'pending',
            'WAITING_FOR_CONFIRMATION' => 'pending',
            'COMPLETED' => 'completed',
            'CANCELED' => 'failed',
            'REJECTED' => 'failed',
        ];

        $newStatus = $statusMap[$payuStatusName] ?? 'pending';

        // Sprawdź czy status wymaga aktualizacji
        if ($currentStatus === $newStatus) {
            return [
                'checked' => true,
                'updated' => false,
                'current_status' => $currentStatus
            ];
        }

        // Aktualizuj status jeśli nie dry-run
        if (!$this->option('dry-run')) {
            $this->updatePaymentStatus($payment, $newStatus, $payuStatus);

            // Jeśli płatność została ukończona, aktywuj subskrypcję
            if ($newStatus === 'completed' && $currentStatus !== 'completed') {
                $this->handlePaymentCompletion($payment);
            }
        } else {
            $this->info("🔍 DRY RUN: Payment {$payment->id} zostałaby zaktualizowana: {$currentStatus} → {$newStatus}");
        }

        return [
            'checked' => true,
            'updated' => true,
            'old_status' => $currentStatus,
            'new_status' => $newStatus
        ];
    }

    protected function updatePaymentStatus(Payment $payment, string $newStatus, array $payuData): void
    {
        $gatewayResponse = $payment->gateway_response ?? [];
        $gatewayResponse['sync_updated_at'] = now()->toISOString();
        $gatewayResponse['payu_sync_data'] = $payuData;

        $updateData = [
            'status' => $newStatus,
            'gateway_response' => $gatewayResponse
        ];

        if ($newStatus === 'completed') {
            $updateData['processed_at'] = now();
        }

        $payment->update($updateData);

        Log::info('Payment status synchronized', [
            'payment_id' => $payment->id,
            'old_status' => $payment->getOriginal('status'),
            'new_status' => $newStatus,
            'payu_status' => $payuData['status'] ?? 'unknown'
        ]);
    }

    protected function handlePaymentCompletion(Payment $payment): void
    {
        try {
            // Użyj istniejącej metody z PayURestService
            $this->payuService->completeSubscriptionPayment($payment);

            $this->info("✅ Payment {$payment->id}: Subskrypcja aktywowana");
        } catch (\Exception $e) {
            $this->error("❌ Payment {$payment->id}: Błąd aktywacji subskrypcji - {$e->getMessage()}");
        }
    }
}