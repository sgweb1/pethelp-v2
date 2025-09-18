<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentService
{
    private const COMMISSION_RATE = 0.10; // 10% commission

    public function createPayment(Booking $booking, string $paymentMethod): Payment
    {
        $commission = $booking->total_price * self::COMMISSION_RATE;

        return Payment::create([
            'booking_id' => $booking->id,
            'status' => 'pending',
            'amount' => $booking->total_price,
            'commission' => $commission,
            'payment_method' => $paymentMethod,
            'external_id' => $this->generateExternalId(),
        ]);
    }

    public function processPayment(Payment $payment, string $paymentMethod): bool
    {
        // Symulacja płatności - w prawdziwej aplikacji byłaby tu integracja z PayU, Stripe, itp.
        $payment->update([
            'status' => 'processing',
            'payment_method' => $paymentMethod
        ]);

        // Symulacja opóźnienia przetwarzania
        sleep(1);

        // Symulacja sukcesu/porażki (90% sukces)
        $success = rand(1, 100) <= 90;

        if ($success) {
            $payment->update([
                'status' => 'completed',
                'processed_at' => now(),
                'gateway_response' => [
                    'transaction_id' => Str::random(20),
                    'status' => 'success',
                    'processed_at' => now()->toISOString()
                ]
            ]);

            // Automatycznie potwierdź rezerwację po udanej płatności
            $payment->booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now()
            ]);

            // Wyślij powiadomienia
            $notificationService = app(NotificationService::class);
            $notificationService->notifyPaymentCompleted($payment);
            $notificationService->notifyBookingConfirmed($payment->booking);

            return true;
        } else {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => [
                    'error' => 'Płatność nieudana',
                    'error_code' => 'PAYMENT_FAILED',
                    'processed_at' => now()->toISOString()
                ]
            ]);

            // Wyślij powiadomienie o nieudanej płatności
            $notificationService = app(NotificationService::class);
            $notificationService->notifyPaymentFailed($payment);

            return false;
        }
    }

    public function refundPayment(Payment $payment, string $reason = ''): bool
    {
        if (!$payment->canBeRefunded()) {
            return false;
        }

        $payment->update([
            'status' => 'refunded',
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'refund_reason' => $reason,
                'refunded_at' => now()->toISOString()
            ])
        ]);

        // Anuluj rezerwację po zwrocie
        $payment->booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Zwrot płatności: ' . $reason
        ]);

        return true;
    }

    public function calculateCommission(float $amount): float
    {
        return $amount * self::COMMISSION_RATE;
    }

    public function getSitterAmount(float $totalAmount): float
    {
        return $totalAmount - $this->calculateCommission($totalAmount);
    }

    public function getPaymentMethods(): array
    {
        return [
            'card' => 'Karta płatnicza',
            'blik' => 'BLIK',
            'transfer' => 'Przelew bankowy'
        ];
    }

    private function generateExternalId(): string
    {
        return 'PH_' . now()->format('YmdHis') . '_' . Str::random(8);
    }

    public function getPaymentStatus(Payment $payment): array
    {
        return [
            'status' => $payment->status,
            'status_label' => $payment->status_label,
            'amount' => $payment->amount,
            'commission' => $payment->commission,
            'sitter_amount' => $payment->sitter_amount,
            'payment_method' => $payment->payment_method,
            'payment_method_label' => $payment->payment_method_label,
            'processed_at' => $payment->processed_at,
            'can_be_refunded' => $payment->canBeRefunded()
        ];
    }
}