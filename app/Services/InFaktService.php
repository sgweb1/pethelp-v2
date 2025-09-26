<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Serwis integracji z InFakt - system fakturowania.
 *
 * Automatycznie generuje faktury po udanej płatności subskrypcji
 * i synchronizuje statusy płatności z InFakt.
 *
 * @package App\Services
 */
class InFaktService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $companyId;
    protected string $environment;
    protected array $config;

    public function __construct()
    {
        $this->config = config('infakt');
        $this->environment = $this->config['environment'];

        $this->apiUrl = $this->config['api_url'][$this->environment];
        $this->apiKey = $this->config['api_key'];
        $this->companyId = $this->config['company_id'] ?? null; // Opcjonalne w nowej wersji API

        if (!$this->apiKey) {
            throw new \Exception('InFakt API key nie został skonfigurowany');
        }
    }

    /**
     * Testuje połączenie z API InFakt.
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            // Test endpoint faktur - działa lepiej niż /profile.json
            $response = Http::withHeaders([
                'X-inFakt-ApiKey' => $this->apiKey,
                'Accept' => 'application/json'
            ])->get($this->apiUrl . '/invoices.json?limit=1');

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'message' => 'Połączenie z InFakt API działa poprawnie',
                    'environment' => $this->environment,
                    'api_endpoint' => $this->apiUrl,
                    'invoices_count' => $data['metainfo']['total_count'] ?? 0
                ];
            }

            return [
                'success' => false,
                'error' => 'Błąd API: ' . $response->status() . ' - ' . $response->body(),
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Błąd połączenia: ' . $e->getMessage(),
                'config_check' => [
                    'api_key_set' => !empty($this->apiKey) && $this->apiKey !== 'your_api_key_here',
                    'environment' => $this->environment
                ]
            ];
        }
    }

    /**
     * Tworzy fakturę po udanej płatności subskrypcji.
     *
     * @param Payment $payment
     * @param User $user
     * @param SubscriptionPlan $plan
     * @return array
     */
    public function createInvoiceForSubscription(Payment $payment, User $user, SubscriptionPlan $plan): array
    {
        try {
            $invoiceData = $this->buildInvoiceData($payment, $user, $plan);

            Log::info('Tworzenie faktury InFakt', [
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'plan_slug' => $plan->slug
            ]);

            $response = Http::withHeaders([
                'X-inFakt-ApiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->apiUrl . "/invoices.json", $invoiceData);

            if ($response->successful()) {
                $invoiceResponse = $response->json();
                $invoiceId = $invoiceResponse['id'];

                // Wystawiaj fakturę (zmień status z draft na issued)
                $this->issueInvoice($invoiceId);

                // Oznacz fakturę jako opłaconą jeśli płatność PayU jest completed
                if ($payment->status === 'completed' && $this->config['auto_mark_paid']) {
                    $this->markInvoiceAsPaid($invoiceId, $payment->amount);
                }

                // Pobierz PDF content po wystawieniu faktury
                $pdfContent = $this->getInvoicePdfContent($invoiceId);

                // Wyślij email jeśli włączone
                if ($this->config['auto_send_email']) {
                    $this->sendInvoiceByEmail($invoiceId);
                }

                Log::info('Faktura InFakt utworzona pomyślnie', [
                    'invoice_id' => $invoiceId,
                    'invoice_number' => $invoiceResponse['number'],
                    'payment_id' => $payment->id,
                    'pdf_available' => !empty($pdfContent)
                ]);

                // Zaktualizuj płatność o dane faktury
                $payment->update([
                    'gateway_response' => array_merge($payment->gateway_response ?? [], [
                        'infakt_invoice_id' => $invoiceId,
                        'infakt_invoice_number' => $invoiceResponse['number'],
                        'infakt_pdf_content' => $pdfContent, // Base64 encoded PDF
                        'invoice_created_at' => now()->toISOString()
                    ])
                ]);

                return [
                    'success' => true,
                    'invoice_id' => $invoiceId,
                    'invoice_number' => $invoiceResponse['number'],
                    'pdf_content' => $pdfContent
                ];
            }

            $errorData = $response->json();
            Log::error('Błąd tworzenia faktury InFakt', [
                'status_code' => $response->status(),
                'error' => $errorData,
                'payment_id' => $payment->id
            ]);

            return [
                'success' => false,
                'error' => $errorData['errors'] ?? 'Nieznany błąd InFakt'
            ];

        } catch (\Exception $e) {
            Log::error('Wyjątek podczas tworzenia faktury InFakt', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Buduje dane faktury dla API InFakt.
     *
     * @param Payment $payment
     * @param User $user
     * @param SubscriptionPlan $plan
     * @return array
     */
    protected function buildInvoiceData(Payment $payment, User $user, SubscriptionPlan $plan): array
    {
        $invoiceData = $payment->gateway_response['invoice_data'] ?? [];
        $isCompany = !empty($invoiceData['tax_id']);

        // Przygotuj dane nabywcy
        $buyer = [
            'name' => $isCompany ? ($invoiceData['company_name'] ?? $user->name) : $user->name,
            'email' => $user->email,
        ];

        // Jeśli firma - dodaj dane firmowe
        if ($isCompany) {
            $buyer['tax_id'] = $invoiceData['tax_id'];
            $buyer['post_code'] = $invoiceData['postal_code'] ?? '';
            $buyer['city'] = $invoiceData['city'] ?? '';
            $buyer['street'] = $invoiceData['address'] ?? '';
            $buyer['country'] = $invoiceData['country'] ?? 'Polska';
        }

        // Przygotuj pozycję faktury
        $serviceName = $this->config['service_descriptions'][$plan->slug] ?? "Subskrypcja {$plan->name}";

        // Oblicz ceny - InFakt oczekuje kwot w groszach (jako liczby całkowite)
        $grossPricePLN = max((float) $plan->price, 0.01); // Minimum 1 grosz w PLN
        $taxRate = $this->config['invoice_defaults']['tax_rate'];

        // Konwertuj na grosze (InFakt API oczekuje kwot w groszach)
        $grossPrice = (int) round($grossPricePLN * 100); // 49.00 PLN = 4900 groszy
        $netPrice = (int) round($grossPrice / (1 + $taxRate / 100));
        $taxPrice = $grossPrice - $netPrice;

        // Przygotuj tax_values - wymagane przez InFakt API (w groszach)
        $taxValues = [
            (string) $taxRate => [
                'net' => $netPrice,
                'tax' => $taxPrice,
                'gross' => $grossPrice
            ]
        ];

        return [
            'invoice' => [
                'number' => null, // InFakt wygeneruje automatycznie
                'currency' => $this->config['invoice_defaults']['currency'],
                'language' => $this->config['invoice_defaults']['language'],
                'paid_date' => null, // Ustawimy później przez markAsPaid
                'invoice_date' => Carbon::now()->format('Y-m-d'), // Poprawione z issue_date
                'sale_date' => Carbon::now()->format('Y-m-d'),
                'payment_date' => Carbon::now()->addDays($this->config['invoice_defaults']['payment_date'])->format('Y-m-d'),
                'payment_method' => $this->config['invoice_defaults']['payment_method'],
                'description' => "Subskrypcja {$plan->name} - opłata za dostęp do platformy PetHelp",

                // tax_values - wymagane przez API InFakt
                'tax_values' => $taxValues,

                // Dane wystawcy (wymagane przez InFakt)
                'bank_name' => $this->config['invoice_defaults']['bank_name'],
                'bank_account' => $this->config['invoice_defaults']['bank_account'],

                // Dane klienta
                'client_company_name' => $buyer['name'],
                'client_street' => $buyer['street'] ?? '',
                'client_city' => $buyer['city'] ?? '',
                'client_post_code' => $buyer['post_code'] ?? '',
                'client_tax_id' => $buyer['tax_id'] ?? '',
                'client_country' => $buyer['country'] ?? 'Polska',
                'client_email' => $buyer['email'],

                // Pozycje faktury - poprawiona struktura dla InFakt API (wartości w groszach)
                'services' => [
                    [
                        'name' => $serviceName,
                        'net_price' => $netPrice, // Cena netto w groszach
                        'unit_net_price' => $netPrice, // Cena netto jednostkowa w groszach
                        'tax_symbol' => $taxRate, // Stawka VAT jako liczba
                        'quantity' => 1,
                        'unit' => 'szt.',
                    ]
                ]
            ]
        ];
    }

    /**
     * Oznacza fakturę jako opłaconą w InFakt.
     *
     * @param int $invoiceId
     * @param float $amount
     * @return bool
     */
    public function markInvoiceAsPaid(int $invoiceId, float $amount): bool
    {
        try {
            $response = Http::withHeaders([
                'X-inFakt-ApiKey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->patch($this->apiUrl . "/invoices/{$invoiceId}.json", [
                'invoice' => [
                    'paid_date' => Carbon::now()->format('Y-m-d'),
                    'paid_price' => $amount
                ]
            ]);

            if ($response->successful()) {
                Log::info('Faktura oznaczona jako opłacona', [
                    'invoice_id' => $invoiceId,
                    'amount' => $amount
                ]);
                return true;
            }

            Log::warning('Nie udało się oznaczyć faktury jako opłaconej', [
                'invoice_id' => $invoiceId,
                'status_code' => $response->status(),
                'error' => $response->json()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Błąd podczas oznaczania faktury jako opłaconej', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Wysyła fakturę email do klienta.
     *
     * @param int $invoiceId
     * @return bool
     */
    public function sendInvoiceByEmail(int $invoiceId): bool
    {
        try {
            $response = Http::withHeaders([
                'X-inFakt-ApiKey' => $this->apiKey
            ])->post($this->apiUrl . "/invoices/{$invoiceId}/email.json");

            if ($response->successful()) {
                Log::info('Faktura wysłana email', ['invoice_id' => $invoiceId]);
                return true;
            }

            Log::warning('Nie udało się wysłać faktury email', [
                'invoice_id' => $invoiceId,
                'status_code' => $response->status()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Błąd wysyłania faktury email', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Pobiera PDF faktury z InFakt.
     *
     * @param int $invoiceId
     * @param string $documentType Typ dokumentu (original|copy)
     * @return string|null Base64 encoded PDF content lub null
     */
    public function getInvoicePdfContent(int $invoiceId, string $documentType = 'original'): ?string
    {
        try {
            // Najpierw sprawdź czy faktura nie jest draft - jeśli tak, wystawiaj
            $invoiceResponse = Http::withHeaders([
                'X-inFakt-ApiKey' => $this->apiKey
            ])->get($this->apiUrl . "/invoices/{$invoiceId}.json");

            if ($invoiceResponse->successful()) {
                $invoice = $invoiceResponse->json();

                // Jeśli faktura ma status draft, wystawiaj ją
                if ($invoice['status'] === 'draft') {
                    $this->issueInvoice($invoiceId);
                }
            }

            // Pobierz PDF przez dedykowany endpoint - InFakt zwraca binary PDF
            $response = Http::withHeaders([
                'X-inFakt-ApiKey' => $this->apiKey
            ])->get($this->apiUrl . "/invoices/{$invoiceId}/pdf.json?document_type={$documentType}");

            if ($response->successful()) {
                // InFakt zwraca PDF jako binary content
                $pdfBinary = $response->body();

                if (!empty($pdfBinary)) {
                    // Konwertuj na base64 dla łatwiejszego przechowywania/przesyłania
                    return base64_encode($pdfBinary);
                }
            }

            Log::warning('Nie udało się pobrać PDF faktury', [
                'invoice_id' => $invoiceId,
                'status_code' => $response->status(),
                'content_type' => $response->header('Content-Type'),
                'body_length' => strlen($response->body())
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Błąd pobierania PDF faktury', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Pobiera URL do PDF faktury (backward compatibility).
     *
     * @param int $invoiceId
     * @param string $documentType
     * @return string|null
     * @deprecated Użyj getInvoicePdfContent()
     */
    public function getInvoicePdfUrl(int $invoiceId, string $documentType = 'original'): ?string
    {
        return $this->getInvoicePdfContent($invoiceId, $documentType);
    }

    /**
     * Wystawia fakturę (zmienia status z draft na issued).
     *
     * @param int $invoiceId
     * @return bool
     */
    public function issueInvoice(int $invoiceId): bool
    {
        try {
            $response = Http::withHeaders([
                'X-inFakt-ApiKey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->patch($this->apiUrl . "/invoices/{$invoiceId}.json", [
                'invoice' => [
                    'status' => 'issued'
                ]
            ]);

            if ($response->successful()) {
                Log::info('Faktura wystawiona', ['invoice_id' => $invoiceId]);
                return true;
            }

            Log::warning('Nie udało się wystawić faktury', [
                'invoice_id' => $invoiceId,
                'status_code' => $response->status(),
                'error' => $response->json()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Błąd wystawiania faktury', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Sprawdza status faktury w InFakt.
     *
     * @param int $invoiceId
     * @return array|null
     */
    public function getInvoiceStatus(int $invoiceId): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-inFakt-ApiKey' => $this->apiKey
            ])->get($this->apiUrl . "/invoices/{$invoiceId}.json");

            if ($response->successful()) {
                $invoice = $response->json();
                return [
                    'id' => $invoice['id'],
                    'number' => $invoice['number'],
                    'status' => $invoice['status'],
                    'paid_date' => $invoice['paid_date'],
                    'paid_price' => $invoice['paid_price'],
                    'pdf_url' => $invoice['pdf_url']
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Błąd sprawdzania statusu faktury', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

}