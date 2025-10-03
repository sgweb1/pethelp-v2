<?php

namespace App\Filament\Resources\Accounting\Pages;

use App\Filament\Resources\Accounting\InvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

/**
 * Strona edycji faktury.
 *
 * Umożliwia edycję faktur w statusie 'draft'.
 * Faktury już wystawione nie mogą być edytowane (tylko anulowane).
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Podgląd'),
            DeleteAction::make()
                ->label('Usuń')
                ->visible(fn ($record) => in_array($record->status, ['draft', 'cancelled'])),
        ];
    }

    /**
     * Przekierowanie po zapisaniu zmian.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    /**
     * Akcje wykonywane przed zapisaniem.
     */
    protected function beforeSave(): void
    {
        // Sprawdź czy faktura może być edytowana
        if ($this->record->status !== 'draft') {
            $this->halt();
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Nie można edytować faktury')
                ->body('Można edytować tylko faktury w statusie szkicu.')
                ->send();
        }
    }
}
