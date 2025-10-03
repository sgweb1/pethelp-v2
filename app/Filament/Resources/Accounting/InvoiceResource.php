<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\Pages\CreateInvoice;
use App\Filament\Resources\Accounting\Pages\EditInvoice;
use App\Filament\Resources\Accounting\Pages\ListInvoices;
use App\Filament\Resources\Accounting\Pages\ViewInvoice;
use App\Filament\Resources\Accounting\Schemas\InvoiceForm;
use App\Filament\Resources\Accounting\Tables\InvoicesTable;
use App\Models\Invoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource zarządzający fakturami - Panel Księgowy.
 *
 * Kompleksowy system zarządzania fakturami z integracją inFakt:
 * - Przeglądanie wszystkich faktur
 * - Tworzenie nowych faktur (ręcznie lub automatycznie)
 * - Edycja i anulowanie faktur
 * - Oznaczanie jako opłacone
 * - Generowanie faktur w inFakt
 * - Pobieranie PDF
 * - Raporty księgowe
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Faktury';

    protected static ?string $modelLabel = 'faktura';

    protected static ?string $pluralModelLabel = 'faktury';

    protected static string|UnitEnum|null $navigationGroup = 'Księgowość';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'view' => ViewInvoice::route('/{record}'),
            'edit' => EditInvoice::route('/{record}/edit'),
        ];
    }

    /**
     * Określa czy można edytować fakturę.
     */
    public static function canEdit($record): bool
    {
        // Można edytować tylko faktury w statusie draft
        return $record->status === 'draft';
    }

    /**
     * Określa czy można usuwać fakturę.
     */
    public static function canDelete($record): bool
    {
        // Można usuwać tylko faktury draft lub cancelled
        return in_array($record->status, ['draft', 'cancelled']);
    }

    /**
     * Liczba rekordów wyświetlanych w nawigacji (badge).
     */
    public static function getNavigationBadge(): ?string
    {
        $overdueCount = Invoice::overdue()->count();

        return $overdueCount > 0 ? (string) $overdueCount : null;
    }

    /**
     * Kolor badge w nawigacji.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger'; // Czerwony dla przeterminowanych
    }
}
