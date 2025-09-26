<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Serwis obsługi płatności PayU REST API.
 *
 * Implementuje nowoczesne REST API PayU dla subskrypcji i płatności.
 * Używa OAuth2 i JSON API zamiast form-based Classic API.
 *
 * @package App\Services
 */
class PayURestService
{
    protected ?string $apiUrl;
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected string $environment;
    protected ?string $accessToken = null;

    public function __construct()
    {
        $this->environment = config('payu.environment', 'sandbox');
        $this->apiUrl = config('payu.api_url.' . $this->environment);
        $this->clientId = config('payu.oauth_client_id');
        $this->clientSecret = config('payu.oauth_client_secret');
    }

    /**
     * Tworzy płatność subskrypcji w systemie PayU REST API.
     *
     * @param User $user Użytkownik kupujący subskrypcję
     * @param SubscriptionPlan $plan Plan subskrypcji
     * @param array $additionalData Dodatkowe dane (dane faktury, zgody prawne)
     * @return array Wynik operacji
     */
    public function createSubscriptionPayment(User $user, SubscriptionPlan $plan, array $additionalData = []): array
    {
        try {
            // Uzyskaj token OAuth2
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'error' => 'Nie udało się uzyskać tokena autoryzacji'];
            }

            $orderId = $this->generateOrderId();
            $amount = $this->calculateAmount($plan->price);

            // Tworzenie wpisu płatności w bazie
            $payment = Payment::create([
                'status' => 'pending',
                'amount' => $plan->price,
                'payment_method' => 'payu',
                'external_id' => $orderId,
                'gateway_response' => [
                    'plan_id' => $plan->id,
                    'plan_slug' => $plan->slug, // Dodaj slug jako stabilny identyfikator
                    'user_id' => $user->id,
                    'billing_period' => $plan->billing_period,
                    'description' => "Subskrypcja {$plan->name} - PetHelp",
                    'invoice_data' => $additionalData['invoice_data'] ?? [],
                    'legal_consents' => $additionalData['legal_consents'] ?? [],
                    'api_type' => 'rest'
                ],
            ]);

            // Przygotuj dane zamówienia dla PayU REST API
            $orderData = $this->buildOrderData($user, $plan, $amount, $orderId, $payment->id);

            // Wyślij żądanie do PayU REST API - WYŁĄCZ automatyczne przekierowania
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->withOptions([
                'allow_redirects' => false, // Kluczowe: wyłącz automatyczne przekierowania
            ])->post($this->apiUrl . 'api/v2_1/orders', $orderData);

            Log::info('PayU REST API request', [
                'url' => $this->apiUrl . 'api/v2_1/orders',
                'order_data' => $orderData,
                'response_status' => $response->status(),
                'response_body' => $response->body()
            ]);

            // PayU REST API może zwrócić różne response codes
            Log::info('PayU response details', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'content_type' => $response->header('Content-Type'),
                'body_preview' => substr($response->body(), 0, 500)
            ]);

            // Sprawdź czy to przekierowanie 302 z Location header
            if ($response->status() === 302) {
                $location = $response->header('Location');
                if ($location) {
                    Log::info('PayU REST payment created - redirect via header', [
                        'payment_id' => $payment->id,
                        'order_id' => $orderId,
                        'redirect_uri' => $location,
                        'status_code' => $response->status()
                    ]);

                    return [
                        'success' => true,
                        'payment_id' => $payment->id,
                        'redirect_url' => $location,
                        'order_id' => $orderId,
                    ];
                }
            }

            // Spróbuj JSON response
            if ($response->successful() || $response->status() === 302) {
                try {
                    $responseData = $response->json();

                    if (isset($responseData['redirectUri'])) {
                        // Zapisz PayU Order ID do gateway_response
                        $payuOrderId = $responseData['orderId'] ?? null;
                        if ($payuOrderId) {
                            $gatewayResponse = $payment->gateway_response ?? [];
                            $gatewayResponse['payu_order_id'] = $payuOrderId;
                            $payment->update(['gateway_response' => $gatewayResponse]);
                        }

                        Log::info('PayU REST payment created - JSON response', [
                            'payment_id' => $payment->id,
                            'order_id' => $orderId,
                            'payu_order_id' => $payuOrderId,
                            'redirect_uri' => $responseData['redirectUri'],
                            'status_code' => $response->status()
                        ]);

                        return [
                            'success' => true,
                            'payment_id' => $payment->id,
                            'redirect_url' => $responseData['redirectUri'],
                            'order_id' => $orderId,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning('PayU response is not JSON', [
                        'error' => $e->getMessage(),
                        'response_preview' => substr($response->body(), 0, 1000)
                    ]);
                }
            }

            // Obsługa błędów
            $errorData = $response->json();
            Log::error('PayU REST payment creation failed', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'response_status' => $response->status(),
                'error_data' => $errorData,
            ]);

            $payment->update([
                'status' => 'failed',
                'gateway_response' => [
                    'error' => $errorData,
                    'failed_at' => now()->toISOString(),
                    'api_type' => 'rest'
                ],
            ]);

            return [
                'success' => false,
                'error' => $errorData['status']['statusDesc'] ?? 'Nie udało się utworzyć płatności',
            ];

        } catch (\Exception $e) {
            Log::error('PayU REST payment creation exception', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Wystąpił błąd podczas przetwarzania płatności',
            ];
        }
    }

    /**
     * Uzyskuje token OAuth2 dla PayU REST API.
     *
     * @return string|null
     */
    protected function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $response = Http::asForm()->post($this->apiUrl . 'pl/standard/user/oauth/authorize', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'] ?? null;

                Log::info('PayU OAuth token obtained', [
                    'expires_in' => $data['expires_in'] ?? null
                ]);

                return $this->accessToken;
            }

            Log::error('PayU OAuth token request failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error('PayU OAuth token exception', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Buduje dane zamówienia dla PayU REST API.
     *
     * @param User $user
     * @param SubscriptionPlan $plan
     * @param int $amount
     * @param string $orderId
     * @param int $paymentId
     * @return array
     */
    protected function buildOrderData(User $user, SubscriptionPlan $plan, int $amount, string $orderId, int $paymentId): array
    {
        return [
            'notifyUrl' => config('payu.notify_url'),
            'continueUrl' => config('payu.continue_url'),
            'customerIp' => request()->ip() === '127.0.0.1' ? '8.8.8.8' : request()->ip(),
            'merchantPosId' => $this->clientId,
            'description' => "Subskrypcja {$plan->name} - PetHelp",
            'currencyCode' => 'PLN',
            'totalAmount' => $amount,
            'extOrderId' => $orderId,
            'buyer' => [
                'email' => $user->email,
                'firstName' => explode(' ', $user->name)[0] ?? '',
                'lastName' => explode(' ', $user->name, 2)[1] ?? '',
                'language' => 'pl',
            ],
            'products' => [
                [
                    'name' => "Subskrypcja {$plan->name}",
                    'unitPrice' => $amount,
                    'quantity' => 1,
                ]
            ],
        ];
    }

    /**
     * Obsługuje notyfikację zwrotną z PayU REST API.
     *
     * @param array $data Dane otrzymane z PayU
     * @return bool Czy notyfikacja została poprawnie obsłużona
     */
    public function handleNotification(array $data): bool
    {
        try {
            $orderId = $data['order']['extOrderId'] ?? null;
            $status = $data['order']['status'] ?? null;

            if (!$orderId || !$status) {
                Log::warning('PayU REST notification missing required data', $data);
                return false;
            }

            $payment = Payment::where('external_id', $orderId)->first();

            if (!$payment) {
                Log::warning('PayU REST notification for unknown order', ['order_id' => $orderId]);
                return false;
            }

            $this->updatePaymentStatus($payment, $status, $data);

            if ($status === 'COMPLETED') {
                $this->completeSubscriptionPayment($payment);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('PayU REST notification handling failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Aktualizuje status płatności w bazie danych.
     *
     * @param Payment $payment
     * @param string $status
     * @param array $data
     */
    protected function updatePaymentStatus(Payment $payment, string $status, array $data): void
    {
        $statusMap = [
            'NEW' => 'pending',
            'PENDING' => 'pending',
            'WAITING_FOR_CONFIRMATION' => 'pending',
            'COMPLETED' => 'completed',
            'CANCELED' => 'failed',
            'REJECTED' => 'failed',
        ];

        $newStatus = $statusMap[$status] ?? 'pending';

        $gatewayResponse = $payment->gateway_response ?? [];
        $updateData = [
            'gateway_response' => array_merge($gatewayResponse, [
                'external_status' => $status,
                'last_notification' => now()->toISOString(),
                'payu_data' => $data,
                'api_type' => 'rest'
            ]),
        ];

        if ($newStatus === 'completed') {
            $updateData['status'] = 'completed';
            $updateData['processed_at'] = now();
        } elseif ($newStatus === 'failed') {
            $updateData['status'] = 'failed';
            $updateData['gateway_response']['failure_reason'] = $data['order']['status'] ?? 'Payment failed';
            $updateData['gateway_response']['failed_at'] = now()->toISOString();
        }

        $payment->update($updateData);
    }

    /**
     * Finalizuje płatność subskrypcji - aktywuje subskrypcję.
     *
     * @param Payment $payment
     */
    protected function completeSubscriptionPayment(Payment $payment): void
    {
        $gatewayResponse = $payment->gateway_response ?? [];
        if (isset($gatewayResponse['subscription_processed']) && $gatewayResponse['subscription_processed']) {
            return; // Już przetworzone
        }

        // Próbuj użyć plan_slug, w razie braku fallback na plan_id
        $planSlug = $gatewayResponse['plan_slug'] ?? null;
        $planId = $gatewayResponse['plan_id'] ?? null;
        $userId = $gatewayResponse['user_id'] ?? null;

        if (!$planSlug && !$planId) {
            Log::error('Payment missing plan_slug and plan_id', ['payment_id' => $payment->id]);
            return;
        }

        if (!$userId) {
            Log::error('Payment missing user_id', ['payment_id' => $payment->id]);
            return;
        }

        // Preferuj wyszukiwanie po slug (stabilny), fallback na ID
        $plan = $planSlug ? SubscriptionPlan::where('slug', $planSlug)->first() : null;
        if (!$plan && $planId) {
            $plan = SubscriptionPlan::find($planId);
        }

        $user = User::find($userId);

        if (!$plan || !$user) {
            Log::error('Plan or User not found for payment', [
                'payment_id' => $payment->id,
                'plan_slug' => $planSlug,
                'plan_id' => $planId,
                'user_id' => $userId
            ]);
            return;
        }

        // Anuluj istniejące aktywne subskrypcje
        $user->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->update(['status' => Subscription::STATUS_CANCELLED, 'cancelled_at' => now()]);

        // Utwórz nową subskrypcję
        $subscription = Subscription::createFromPlan($user, $plan);
        $subscription->update(['status' => Subscription::STATUS_ACTIVE]);

        // Oznacz płatność jako przetworzoną
        $gatewayResponse['subscription_processed'] = true;
        $gatewayResponse['subscription_id'] = $subscription->id;
        $payment->update(['gateway_response' => $gatewayResponse]);

        Log::info('Subscription activated via PayU REST payment', [
            'user_id' => $userId,
            'subscription_id' => $subscription->id,
            'payment_id' => $payment->id,
        ]);

        // Automatyczne generowanie faktury InFakt dla płatnych planów
        if ($plan->price > 0) {
            try {
                $inFaktService = app(\App\Services\InFaktService::class);
                $invoiceResult = $inFaktService->createInvoiceForSubscription($payment, $user, $plan);

                if ($invoiceResult['success']) {
                    Log::info('Faktura InFakt utworzona automatycznie', [
                        'payment_id' => $payment->id,
                        'invoice_id' => $invoiceResult['invoice_id'],
                        'invoice_number' => $invoiceResult['invoice_number']
                    ]);
                } else {
                    Log::warning('Nie udało się utworzyć faktury InFakt', [
                        'payment_id' => $payment->id,
                        'error' => $invoiceResult['error']
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Błąd podczas tworzenia faktury InFakt', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Przelicza cenę z PLN na grosze.
     *
     * @param float $price Cena w PLN
     * @return int Cena w groszach
     */
    protected function calculateAmount(float $price): int
    {
        return (int) round($price * 100);
    }

    /**
     * Generuje unikalny ID zamówienia.
     *
     * @return string
     */
    protected function generateOrderId(): string
    {
        return 'PETHELP_' . time() . '_' . Str::upper(Str::random(8));
    }

    /**
     * Test połączenia PayU REST API.
     *
     * @return array
     */
    public function testConnection(): array
    {
        $token = $this->getAccessToken();

        return [
            'success' => $token !== null,
            'message' => $token ? 'PayU REST API - połączenie prawidłowe' : 'PayU REST API - błąd autoryzacji',
            'environment' => $this->environment,
            'client_id' => $this->clientId,
            'api_url' => $this->apiUrl,
            'api_type' => 'rest',
            'has_token' => $token !== null
        ];
    }

    /**
     * Test PayU REST API z oficjalnymi credentialami sandbox.
     *
     * @return array
     */
    public function testSandboxConnection(): array
    {
        try {
            // Oficjalne credentials sandbox z dokumentacji PayU
            $response = Http::asForm()->post($this->apiUrl . 'pl/standard/user/oauth/authorize', [
                'grant_type' => 'client_credentials',
                'client_id' => '145227',
                'client_secret' => '12f071174cb7eb79d4aac5bc2f07563f',
            ]);

            Log::info('PayU sandbox credentials test', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $testToken = $data['access_token'] ?? null;

                if ($testToken) {
                    // Test wysyłania prostego zamówienia
                    $testResponse = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $testToken,
                        'Content-Type' => 'application/json',
                    ])->post($this->apiUrl . 'api/v2_1/orders', [
                        'customerIp' => '127.0.0.1',
                        'merchantPosId' => '145227',
                        'description' => 'Test order',
                        'currencyCode' => 'PLN',
                        'totalAmount' => '100',
                        'extOrderId' => 'TEST_' . time(),
                        'products' => [
                            [
                                'name' => 'Test product',
                                'unitPrice' => '100',
                                'quantity' => '1'
                            ]
                        ]
                    ]);

                    Log::info('PayU sandbox order test', [
                        'status' => $testResponse->status(),
                        'response' => $testResponse->body()
                    ]);

                    return [
                        'success' => true,
                        'message' => 'PayU sandbox credentials działają - problem z naszymi credentials',
                        'test_token_obtained' => true,
                        'test_order_status' => $testResponse->status(),
                        'test_order_response' => $testResponse->body()
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Test credentials sandbox też nie działają - problem z PayU API',
                'status' => $response->status(),
                'response' => $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception podczas testowania sandbox',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Sprawdź status płatności w PayU API.
     *
     * @param string $payuOrderId PayU Order ID
     * @return array|null Status płatności lub null w przypadku błędu
     */
    public function checkPaymentStatus(string $payuOrderId): ?array
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::error('Nie udało się uzyskać tokena OAuth do sprawdzenia statusu');
                return null;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->get($this->apiUrl . 'api/v2_1/orders/' . $payuOrderId);

            Log::info('PayU status check', [
                'payu_order_id' => $payuOrderId,
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $orders = $data['orders'] ?? [];

                if (!empty($orders)) {
                    $order = $orders[0];
                    return [
                        'success' => true,
                        'status' => $order['status'] ?? 'UNKNOWN',
                        'totalAmount' => $order['totalAmount'] ?? null,
                        'buyer_email' => $order['buyer']['email'] ?? null,
                        'created' => $order['orderCreateDate'] ?? null,
                        'full_data' => $order
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Nie znaleziono zamówienia',
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('Błąd sprawdzania statusu PayU', [
                'payu_order_id' => $payuOrderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}