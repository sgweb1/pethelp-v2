<?php

namespace App\Filament\Resources\Accounting\Pages;

use App\Filament\Resources\Accounting\InvoiceResource;
use App\Services\InFaktService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Response;

/**
 * Strona podglądu faktury.
 *
 * Wyświetla szczegółowe informacje o fakturze wraz z akcjami
 * takimi jak wystawienie, oznaczenie jako opłacona, generowanie w inFakt.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edytuj')
                ->visible(fn ($record) => $record->status === 'draft'),

            // Wystaw fakturę
            Action::make('issue')
                ->label('Wystaw fakturę')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'draft')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->issue();
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Faktura wystawiona')
                        ->body("Faktura {$record->invoice_number} została wystawiona.")
                        ->send();
                }),

            // Oznacz jako opłaconą
            Action::make('mark_paid')
                ->label('Oznacz jako opłaconą')
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->visible(fn ($record) => ! $record->isPaid())
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->markAsPaid();
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Faktura opłacona')
                        ->body("Faktura {$record->invoice_number} została oznaczona jako opłacona.")
                        ->send();
                }),

            // Generuj w inFakt
            Action::make('generate_infakt')
                ->label('Generuj w inFakt')
                ->icon('heroicon-o-document-plus')
                ->color('primary')
                ->visible(fn ($record) => ! $record->isSyncedWithInfakt())
                ->requiresConfirmation()
                ->action(function ($record) {
                    $infaktService = app(InFaktService::class);

                    // TODO: Implementacja generowania faktury w inFakt
                    \Filament\Notifications\Notification::make()
                        ->warning()
                        ->title('Funkcja w przygotowaniu')
                        ->body('Generowanie faktur w inFakt będzie dostępne wkrótce')
                        ->send();
                }),

            // Pobierz PDF
            Action::make('download_pdf')
                ->label('Pobierz PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->visible(fn ($record) => $record->hasInfaktPdf())
                ->action(function ($record) {
                    if ($record->infakt_pdf_content) {
                        $pdfContent = base64_decode($record->infakt_pdf_content);
                        $filename = "faktura_{$record->invoice_number}.pdf";

                        return Response::streamDownload(function () use ($pdfContent) {
                            echo $pdfContent;
                        }, $filename, ['Content-Type' => 'application/pdf']);
                    }

                    \Filament\Notifications\Notification::make()
                        ->warning()
                        ->title('PDF niedostępny')
                        ->send();
                }),

            // Anuluj fakturę
            Action::make('cancel')
                ->label('Anuluj fakturę')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record) => ! in_array($record->status, ['cancelled', 'paid']))
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Powód anulowania')
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    $record->cancel($data['reason']);
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Faktura anulowana')
                        ->body("Faktura {$record->invoice_number} została anulowana.")
                        ->send();
                }),

            DeleteAction::make()
                ->label('Usuń')
                ->visible(fn ($record) => in_array($record->status, ['draft', 'cancelled'])),
        ];
    }
}
