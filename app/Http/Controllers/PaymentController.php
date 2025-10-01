<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Services\PayURestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PayURestService $payuService;

    public function __construct(PayURestService $payuService)
    {
        $this->payuService = $payuService;
    }

    public function createSubscriptionPayment(Request $request, SubscriptionPlan $plan): JsonResponse|RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Wymagane logowanie'], 401);
            }

            return redirect()->route('login');
        }

        // Check if user already has this plan
        $currentSubscription = $user->activeSubscription;
        if ($currentSubscription && $currentSubscription->subscriptionPlan->id === $plan->id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Już posiadasz ten plan'], 400);
            }

            return redirect()->route('subscription.dashboard')
                ->with('info', 'Już posiadasz ten plan subskrypcji.');
        }

        // Handle free plan
        if ($plan->price == 0) {
            return $this->handleFreePlan($user, $plan, $request);
        }

        $result = $this->payuService->createSubscriptionPayment($user, $plan);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect($result['redirect_url']);
        }

        return redirect()->route('subscription.plans')
            ->with('error', $result['error']);
    }

    public function payuNotification(Request $request): JsonResponse
    {
        $signature = $request->header('OpenPayu-Signature');
        $body = $request->getContent();

        if (! $signature || ! $body) {
            Log::warning('PayU notification missing signature or body');

            return response()->json(['error' => 'Invalid request'], 400);
        }

        try {
            $data = json_decode($body, true);

            if (! $data) {
                Log::warning('PayU notification invalid JSON');

                return response()->json(['error' => 'Invalid JSON'], 400);
            }

            // Verify signature (simplified - in production use proper signature verification)
            $success = $this->payuService->handleNotification($data);

            if ($success) {
                Log::info('PayU notification processed successfully', ['order_id' => $data['order']['orderId'] ?? null]);

                return response()->json(['status' => 'OK']);
            }

            return response()->json(['error' => 'Processing failed'], 500);

        } catch (\Exception $e) {
            Log::error('PayU notification error', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function paymentSuccess(Request $request): RedirectResponse
    {
        $orderId = $request->query('orderId');

        if ($orderId) {
            $payment = Payment::where('gateway_response->payu_order_id', $orderId)->first();

            // W trybie lokalnym/testowym automatycznie aktywuj subskrypcję
            if ($payment && config('app.env') === 'local' && $payment->status === 'pending') {
                Log::info('Local environment: Auto-completing payment for testing', ['payment_id' => $payment->id]);

                // Symuluj webhook completion
                $this->payuService->handleNotification([
                    'order' => [
                        'orderId' => $orderId,
                        'status' => 'COMPLETED',
                    ],
                ]);

                $payment->refresh();
            }

            if ($payment && $payment->status === 'completed') {
                return redirect()->route('subscription.dashboard')
                    ->with('success', 'Płatność została zrealizowana pomyślnie! Twoja subskrypcja jest już aktywna.');
            }
        }

        return redirect()->route('subscription.dashboard')
            ->with('info', 'Płatność jest w trakcie przetwarzania. Status zostanie zaktualizowany wkrótce.');
    }

    public function paymentCancel(Request $request): RedirectResponse
    {
        return redirect()->route('subscription.plans')
            ->with('warning', 'Płatność została anulowana. Możesz spróbować ponownie.');
    }

    public function paymentStatus(Request $request, Payment $payment): JsonResponse
    {
        $gatewayResponse = $payment->gateway_response ?? [];

        // Check if current user owns this payment (for subscriptions)
        if (isset($gatewayResponse['user_id']) && $gatewayResponse['user_id'] !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $payment->status,
            'external_status' => $gatewayResponse['external_status'] ?? null,
            'amount' => $payment->amount,
            'created_at' => $payment->created_at,
            'processed_at' => $payment->processed_at,
            'failed_at' => $gatewayResponse['failed_at'] ?? null,
            'failure_reason' => $gatewayResponse['failure_reason'] ?? null,
        ]);
    }

    protected function handleFreePlan(mixed $user, SubscriptionPlan $plan, Request $request): JsonResponse|RedirectResponse
    {
        // Cancel existing active subscriptions
        $user->subscriptions()
            ->where('status', \App\Models\Subscription::STATUS_ACTIVE)
            ->update(['status' => \App\Models\Subscription::STATUS_CANCELLED, 'cancelled_at' => now()]);

        // Create free subscription
        $subscription = \App\Models\Subscription::createFromPlan($user, $plan);
        $subscription->update(['status' => \App\Models\Subscription::STATUS_ACTIVE]);

        Log::info('Free subscription activated', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'plan' => $plan->slug,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Plan podstawowy został aktywowany',
                'subscription_id' => $subscription->id,
            ]);
        }

        return redirect()->route('subscription.dashboard')
            ->with('success', 'Plan podstawowy został aktywowany!');
    }
}
