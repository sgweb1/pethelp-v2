<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class PaymentProcess extends Component
{
    public Booking $booking;
    public ?Payment $payment = null;
    public string $paymentMethod = 'card';
    public string $step = 'select_method'; // select_method, processing, success, failed
    public bool $processing = false;
    public string $errorMessage = '';

    protected PaymentService $paymentService;

    public function boot(PaymentService $paymentService): void
    {
        $this->paymentService = $paymentService;
    }

    public function mount(Booking $booking): void
    {
        // Sprawdź czy użytkownik ma prawo do tej rezerwacji
        if ($booking->owner_id !== Auth::id()) {
            abort(403, 'Brak uprawnień do tej rezerwacji');
        }

        // Sprawdź czy rezerwacja może być opłacona
        if ($booking->status !== 'pending') {
            abort(422, 'Ta rezerwacja nie może być już opłacona');
        }

        $this->booking = $booking;

        // Sprawdź czy już istnieje płatność
        $this->payment = $booking->payment;

        if ($this->payment && $this->payment->isCompleted()) {
            $this->step = 'success';
        } elseif ($this->payment && $this->payment->isFailed()) {
            $this->step = 'failed';
        }
    }

    public function processPayment(): void
    {
        $this->processing = true;
        $this->step = 'processing';
        $this->errorMessage = '';

        try {
            // Stwórz płatność jeśli nie istnieje
            if (!$this->payment) {
                $this->payment = $this->paymentService->createPayment(
                    $this->booking,
                    $this->paymentMethod
                );
            }

            // Przetwórz płatność
            $success = $this->paymentService->processPayment(
                $this->payment,
                $this->paymentMethod
            );

            if ($success) {
                $this->step = 'success';
                session()->flash('success', 'Płatność została pomyślnie przetworzona!');
            } else {
                $this->step = 'failed';
                $this->errorMessage = 'Płatność nie powiodła się. Spróbuj ponownie.';
            }

        } catch (\Exception $e) {
            $this->step = 'failed';
            $this->errorMessage = 'Wystąpił błąd podczas przetwarzania płatności.';
            logger()->error('Payment processing error', [
                'booking_id' => $this->booking->id,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->processing = false;

            // Odśwież dane
            $this->booking->refresh();
            $this->payment?->refresh();
        }
    }

    public function retryPayment(): void
    {
        $this->step = 'select_method';
        $this->errorMessage = '';
    }

    public function getPaymentMethodsProperty(): array
    {
        return $this->paymentService->getPaymentMethods();
    }

    public function getCommissionAmountProperty(): float
    {
        return $this->paymentService->calculateCommission($this->booking->total_price);
    }

    public function getSitterAmountProperty(): float
    {
        return $this->paymentService->getSitterAmount($this->booking->total_price);
    }

    public function render()
    {
        return view('livewire.payment-process');
    }
}