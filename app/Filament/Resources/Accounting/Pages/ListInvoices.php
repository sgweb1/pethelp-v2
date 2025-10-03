<?php

namespace App\Filament\Resources\Accounting\Pages;

use App\Filament\Resources\Accounting\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

/**
 * Strona listy faktur w panelu księgowym.
 *
 * Wyświetla tabelę wszystkich faktur z możliwością filtrowania,
 * wyszukiwania i wykonywania akcji masowych.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Utwórz fakturę'),
        ];
    }
}
