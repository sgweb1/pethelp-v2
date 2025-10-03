<?php

namespace App\Filament\Resources\Accounting\Pages;

use App\Filament\Resources\Accounting\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Strona tworzenia nowej faktury.
 *
 * Umożliwia utworzenie faktury w statusie 'draft' z możliwością
 * późniejszego wystawienia lub wygenerowania w systemie inFakt.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    /**
     * Przekierowanie po utworzeniu faktury.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    /**
     * Akcje wykonywane po utworzeniu faktury.
     */
    protected function afterCreate(): void
    {
        // Możemy tutaj dodać logikę, np. wysłanie powiadomienia
    }
}
