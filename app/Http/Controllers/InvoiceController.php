<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\InFaktService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Kontroler do zarządzania fakturami.
 *
 * Obsługuje pobieranie faktur, regenerowanie i synchronizację statusów
 * z systemem InFakt.
 *
 * @package App\Http\Controllers
 */
class InvoiceController extends Controller
{
    protected InFaktService $inFaktService;

    public function __construct(InFaktService $inFaktService)
    {
        $this->inFaktService = $inFaktService;
    }

    /**
     * Pobiera PDF faktury dla użytkownika.
     *
     * @param Request $request
     * @param Payment $payment
     * @return Response|JsonResponse
     */
    public function downloadInvoicePdf(Request $request, Payment $payment)
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik ma dostęp do tej faktury
        $gatewayResponse = $payment->gateway_response ?? [];
        if (($gatewayResponse['user_id'] ?? null) !== $user->id) {
            abort(403, 'Brak dostępu do tej faktury');
        }

        $invoiceId = $gatewayResponse['infakt_invoice_id'] ?? null;

        if (!$invoiceId) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Faktura nie została jeszcze wygenerowana'], 404);
            }
            return redirect()->back()->with('error', 'Faktura nie została jeszcze wygenerowana.');
        }

        try {
            // Sprawdź czy PDF jest już zapisane w gateway_response
            $pdfContent = $gatewayResponse['infakt_pdf_content'] ?? null;

            if (!$pdfContent) {
                // Pobierz PDF z InFakt API
                $pdfContent = $this->inFaktService->getInvoicePdfContent($invoiceId);

                if ($pdfContent) {
                    // Zapisz PDF w payment dla przyszłych żądań
                    $payment->update([
                        'gateway_response' => array_merge($gatewayResponse, [
                            'infakt_pdf_content' => $pdfContent
                        ])
                    ]);
                }
            }

            if ($pdfContent) {
                // Dekoduj base64 do binarnego PDF
                $pdfBinary = base64_decode($pdfContent);

                // Sprawdź czy dekodowanie się powiodło
                if ($pdfBinary === false || strlen($pdfBinary) < 100) {
                    Log::error('Błąd dekodowania PDF', [
                        'payment_id' => $payment->id,
                        'invoice_id' => $invoiceId,
                        'pdf_content_length' => strlen($pdfContent)
                    ]);

                    if ($request->expectsJson()) {
                        return response()->json(['error' => 'Błąd dekodowania PDF'], 500);
                    }
                    return redirect()->back()->with('error', 'Błąd dekodowania PDF.');
                }

                // Sprawdź czy to jest prawidłowy PDF
                if (substr($pdfBinary, 0, 4) !== '%PDF') {
                    Log::error('Nieprawidłowy format PDF', [
                        'payment_id' => $payment->id,
                        'invoice_id' => $invoiceId,
                        'header' => bin2hex(substr($pdfBinary, 0, 10))
                    ]);

                    if ($request->expectsJson()) {
                        return response()->json(['error' => 'Nieprawidłowy format PDF'], 500);
                    }
                    return redirect()->back()->with('error', 'Nieprawidłowy format PDF.');
                }

                // Zwróć PDF jako response
                $filename = 'faktura-' . ($gatewayResponse['infakt_invoice_number'] ?? $invoiceId) . '.pdf';
                $isPreview = $request->query('preview', false);

                Log::info('Pobieranie PDF faktury', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoiceId,
                    'filename' => $filename,
                    'size' => strlen($pdfBinary),
                    'preview' => $isPreview
                ]);

                $headers = [
                    'Content-Type' => 'application/pdf',
                    'Content-Length' => strlen($pdfBinary),
                ];

                // Jeśli preview=true, pokaż w przeglądarce, inaczej pobierz
                if ($isPreview) {
                    $headers['Content-Disposition'] = 'inline; filename="' . $filename . '"';
                    $headers['Cache-Control'] = 'private, max-age=3600';
                } else {
                    $headers['Content-Disposition'] = 'attachment; filename="' . $filename . '"';
                    $headers['Cache-Control'] = 'private, max-age=0, must-revalidate';
                    $headers['Pragma'] = 'no-cache';
                }

                return response($pdfBinary, 200, $headers);
            }

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Nie można pobrać PDF faktury'], 500);
            }
            return redirect()->back()->with('error', 'Nie można pobrać PDF faktury.');

        } catch (\Exception $e) {
            Log::error('Błąd pobierania PDF faktury', [
                'payment_id' => $payment->id,
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Błąd serwera'], 500);
            }
            return redirect()->back()->with('error', 'Wystąpił błąd podczas pobierania faktury.');
        }
    }

    /**
     * Regeneruje fakturę dla płatności.
     *
     * @param Request $request
     * @param Payment $payment
     * @return JsonResponse
     */
    public function regenerateInvoice(Request $request, Payment $payment): JsonResponse
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik ma dostęp do tej płatności
        $gatewayResponse = $payment->gateway_response ?? [];
        if (($gatewayResponse['user_id'] ?? null) !== $user->id) {
            return response()->json(['error' => 'Brak dostępu'], 403);
        }

        if ($payment->status !== 'completed') {
            return response()->json(['error' => 'Można regenerować faktury tylko dla opłaconych płatności'], 400);
        }

        try {
            // Znajdź plan na podstawie slug
            $planSlug = $gatewayResponse['plan_slug'] ?? null;
            if (!$planSlug) {
                return response()->json(['error' => 'Brak informacji o planie w płatności'], 400);
            }

            $plan = \App\Models\SubscriptionPlan::where('slug', $planSlug)->first();
            if (!$plan) {
                return response()->json(['error' => 'Plan subskrypcji nie został znaleziony'], 404);
            }

            $result = $this->inFaktService->createInvoiceForSubscription($payment, $user, $plan);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Faktura została wygenerowana pomyślnie',
                    'invoice_number' => $result['invoice_number']
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Nie udało się wygenerować faktury: ' . ($result['error'] ?? 'Nieznany błąd')
            ], 500);

        } catch (\Exception $e) {
            Log::error('Błąd regeneracji faktury', [
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Wystąpił błąd serwera'
            ], 500);
        }
    }

    /**
     * Sprawdza status faktury w InFakt.
     *
     * @param Request $request
     * @param Payment $payment
     * @return JsonResponse
     */
    public function checkInvoiceStatus(Request $request, Payment $payment): JsonResponse
    {
        $user = Auth::user();

        // Sprawdź dostęp
        $gatewayResponse = $payment->gateway_response ?? [];
        if (($gatewayResponse['user_id'] ?? null) !== $user->id) {
            return response()->json(['error' => 'Brak dostępu'], 403);
        }

        $invoiceId = $gatewayResponse['infakt_invoice_id'] ?? null;
        if (!$invoiceId) {
            return response()->json(['error' => 'Faktura nie została jeszcze wygenerowana'], 404);
        }

        try {
            $status = $this->inFaktService->getInvoiceStatus($invoiceId);

            if ($status) {
                return response()->json([
                    'success' => true,
                    'invoice' => $status
                ]);
            }

            return response()->json(['error' => 'Nie można pobrać statusu faktury'], 500);

        } catch (\Exception $e) {
            Log::error('Błąd sprawdzania statusu faktury', [
                'payment_id' => $payment->id,
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Błąd serwera'], 500);
        }
    }

    /**
     * Webhook od InFakt dla synchronizacji statusów faktur.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function inFaktWebhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            $eventType = $payload['event_type'] ?? null;

            Log::info('InFakt webhook otrzymany', [
                'event_type' => $eventType,
                'payload' => $payload
            ]);

            switch ($eventType) {
                case 'invoice.paid':
                    $this->handleInvoicePaid($payload);
                    break;

                case 'invoice.sent':
                    $this->handleInvoiceSent($payload);
                    break;

                default:
                    Log::info('InFakt webhook - nieznany typ eventu', ['event_type' => $eventType]);
                    break;
            }

            return response()->json(['status' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Błąd przetwarzania webhook InFakt', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json(['error' => 'Błąd serwera'], 500);
        }
    }

    /**
     * Obsługuje event "faktura opłacona" od InFakt.
     *
     * @param array $payload
     */
    protected function handleInvoicePaid(array $payload): void
    {
        $invoiceId = $payload['invoice']['id'] ?? null;
        if (!$invoiceId) return;

        // Znajdź płatność z tym invoice_id
        $payment = Payment::whereJsonContains('gateway_response->infakt_invoice_id', $invoiceId)->first();

        if ($payment) {
            Log::info('Synchronizacja statusu płatności z InFakt', [
                'payment_id' => $payment->id,
                'invoice_id' => $invoiceId,
                'status' => 'paid'
            ]);

            // Możesz tutaj dodać dodatkową logikę synchronizacji
        }
    }

    /**
     * Obsługuje event "faktura wysłana" od InFakt.
     *
     * @param array $payload
     */
    protected function handleInvoiceSent(array $payload): void
    {
        $invoiceId = $payload['invoice']['id'] ?? null;
        if (!$invoiceId) return;

        Log::info('Faktura została wysłana przez InFakt', [
            'invoice_id' => $invoiceId,
            'sent_at' => now()->toISOString()
        ]);
    }
}