<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model faktury.
 *
 * Przechowuje faktury wygenerowane automatycznie przez system
 * lub ręcznie przez administratorów. Obsługuje integrację z inFakt.
 *
 * @property int $id
 * @property int $payment_id
 * @property int|null $booking_id
 * @property int $user_id
 * @property int|null $issuer_id
 * @property string $invoice_number
 * @property string $invoice_type
 * @property string $status
 * @property \Carbon\Carbon $issue_date
 * @property \Carbon\Carbon $sale_date
 * @property \Carbon\Carbon $payment_due_date
 * @property \Carbon\Carbon|null $paid_date
 * @property float $net_amount
 * @property float $tax_amount
 * @property float $gross_amount
 * @property float $paid_amount
 * @property string $currency
 * @property array $line_items
 * @property int|null $infakt_id
 * @property string|null $infakt_number
 *
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_id',
        'booking_id',
        'user_id',
        'issuer_id',
        'invoice_number',
        'invoice_type',
        'status',
        'issue_date',
        'sale_date',
        'payment_due_date',
        'paid_date',
        'net_amount',
        'tax_amount',
        'gross_amount',
        'paid_amount',
        'currency',
        'buyer_name',
        'buyer_tax_id',
        'buyer_address',
        'buyer_postal_code',
        'buyer_city',
        'buyer_country',
        'buyer_email',
        'seller_name',
        'seller_tax_id',
        'seller_address',
        'seller_postal_code',
        'seller_city',
        'seller_country',
        'line_items',
        'payment_method',
        'notes',
        'admin_notes',
        'infakt_id',
        'infakt_number',
        'infakt_pdf_url',
        'infakt_pdf_content',
        'infakt_response',
        'pdf_path',
        'sent_at',
        'cancelled_at',
        'cancelled_reason',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'sale_date' => 'date',
            'payment_due_date' => 'date',
            'paid_date' => 'date',
            'net_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'line_items' => 'array',
            'infakt_response' => 'array',
            'sent_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Płatność powiązana z fakturą.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Zlecenie powiązane z fakturą.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Nabywca faktury (użytkownik).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Wystawca faktury (dla sitterów).
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issuer_id');
    }

    // ==================== SCOPES ====================

    /**
     * Faktury w danym statusie.
     */
    public function scopeStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Faktury wystawione (issued).
     */
    public function scopeIssued(Builder $query): void
    {
        $query->where('status', 'issued');
    }

    /**
     * Faktury opłacone (paid).
     */
    public function scopePaid(Builder $query): void
    {
        $query->where('status', 'paid');
    }

    /**
     * Faktury przeterminowane (overdue).
     */
    public function scopeOverdue(Builder $query): void
    {
        $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'issued')
                    ->where('payment_due_date', '<', now());
            });
    }

    /**
     * Faktury z danego okresu.
     */
    public function scopeDateRange(Builder $query, $from, $to): void
    {
        $query->whereBetween('issue_date', [$from, $to]);
    }

    /**
     * Faktury dla użytkownika.
     */
    public function scopeForUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    // ==================== ACCESSORS & HELPERS ====================

    /**
     * Zwraca etykietę statusu w języku polskim.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Szkic',
            'issued' => 'Wystawiona',
            'sent' => 'Wysłana',
            'paid' => 'Opłacona',
            'cancelled' => 'Anulowana',
            'overdue' => 'Przeterminowana',
            default => 'Nieznany',
        };
    }

    /**
     * Zwraca kolor badge dla statusu.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'issued' => 'blue',
            'sent' => 'indigo',
            'paid' => 'success',
            'cancelled' => 'danger',
            'overdue' => 'warning',
            default => 'gray',
        };
    }

    /**
     * Zwraca etykietę typu faktury.
     */
    public function getInvoiceTypeLabelAttribute(): string
    {
        return match ($this->invoice_type) {
            'vat' => 'Faktura VAT',
            'proforma' => 'Faktura Proforma',
            'correction' => 'Faktura Korygująca',
            'receipt' => 'Paragon',
            default => 'Nieznany',
        };
    }

    /**
     * Sprawdza czy faktura jest opłacona.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid' || $this->paid_amount >= $this->gross_amount;
    }

    /**
     * Sprawdza czy faktura jest przeterminowana.
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'paid'
            && $this->status !== 'cancelled'
            && $this->payment_due_date < now();
    }

    /**
     * Zwraca pozostałą kwotę do zapłaty.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->gross_amount - $this->paid_amount);
    }

    /**
     * Zwraca procent zapłaty.
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->gross_amount == 0) {
            return 0;
        }

        return min(100, ($this->paid_amount / $this->gross_amount) * 100);
    }

    /**
     * Sprawdza czy faktura ma PDF z inFakt.
     */
    public function hasInfaktPdf(): bool
    {
        return !empty($this->infakt_pdf_content) || !empty($this->infakt_pdf_url);
    }

    /**
     * Sprawdza czy faktura została zsynchronizowana z inFakt.
     */
    public function isSyncedWithInfakt(): bool
    {
        return !empty($this->infakt_id);
    }

    // ==================== ACTIONS ====================

    /**
     * Oznacza fakturę jako opłaconą.
     */
    public function markAsPaid(?float $amount = null, ?\Carbon\Carbon $paidDate = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_amount' => $amount ?? $this->gross_amount,
            'paid_date' => $paidDate ?? now(),
        ]);
    }

    /**
     * Anuluje fakturę.
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_reason' => $reason,
        ]);
    }

    /**
     * Wystawia fakturę (zmienia status z draft na issued).
     */
    public function issue(): void
    {
        if ($this->status === 'draft') {
            $this->update([
                'status' => 'issued',
                'issue_date' => $this->issue_date ?? now(),
            ]);
        }
    }

    /**
     * Oznacza fakturę jako wysłaną.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Generuje następny numer faktury.
     */
    public static function generateNextInvoiceNumber(string $prefix = 'PETHELP'): string
    {
        $year = now()->year;
        $month = now()->format('m');

        // Znajdź ostatnią fakturę z tego miesiąca
        $lastInvoice = static::where('invoice_number', 'like', "{$prefix}/{$year}/{$month}/%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Wyciągnij numer sekwencyjny
            $parts = explode('/', $lastInvoice->invoice_number);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s/%d/%s/%04d', $prefix, $year, $month, $nextNumber);
    }

    /**
     * Aktualizuje status na podstawie daty płatności.
     */
    public function updateOverdueStatus(): void
    {
        if ($this->isOverdue() && $this->status === 'issued') {
            $this->update(['status' => 'overdue']);
        }
    }
}
