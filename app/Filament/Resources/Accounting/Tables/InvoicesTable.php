<?php

namespace App\Filament\Resources\Accounting\Tables;

use App\Models\Invoice;
use App\Services\InFaktService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;

/**
 * Tabela faktur z filtrowaniem i akcjami.
 *
 * Funkcje:
 * - Wyświetlanie wszystkich faktur
 * - Filtrowanie po statusie, dacie, użytkowniku
 * - Akcje: wystawianie, oznaczanie jako opłacone, pobieranie PDF
 * - Integracja z inFakt
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class InvoicesTable
{
    /**
     * Konfiguruje tabelę faktur.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Numer faktury')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'issued' => 'info',
                        'sent' => 'primary',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        'overdue' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (Invoice $record) => $record->status_label)
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nabywca')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('gross_amount')
                    ->label('Kwota brutto')
                    ->money('PLN')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('paid_amount')
                    ->label('Zapłacono')
                    ->money('PLN')
                    ->sortable()
                    ->color(fn (Invoice $record) => $record->isPaid() ? 'success' : 'gray'),

                TextColumn::make('remaining_amount')
                    ->label('Do zapłaty')
                    ->money('PLN')
                    ->color(fn (Invoice $record) => $record->remaining_amount > 0 ? 'danger' : 'success'),

                TextColumn::make('issue_date')
                    ->label('Data wystawienia')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('payment_due_date')
                    ->label('Termin płatności')
                    ->date('Y-m-d')
                    ->sortable()
                    ->color(fn (Invoice $record) => $record->isOverdue() ? 'danger' : null),

                TextColumn::make('infakt_number')
                    ->label('inFakt')
                    ->badge()
                    ->color('success')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Szkic',
                        'issued' => 'Wystawiona',
                        'sent' => 'Wysłana',
                        'paid' => 'Opłacona',
                        'cancelled' => 'Anulowana',
                        'overdue' => 'Przeterminowana',
                    ])
                    ->multiple(),

                SelectFilter::make('invoice_type')
                    ->label('Typ faktury')
                    ->options([
                        'vat' => 'Faktura VAT',
                        'proforma' => 'Faktura Proforma',
                        'correction' => 'Faktura Korygująca',
                        'receipt' => 'Paragon',
                    ]),

                Filter::make('overdue')
                    ->label('Tylko przeterminowane')
                    ->query(fn (Builder $query) => $query->overdue()),

                Filter::make('unpaid')
                    ->label('Tylko nieopłacone')
                    ->query(fn (Builder $query) => $query->where('status', '!=', 'paid')),

                Filter::make('date_range')
                    ->label('Zakres dat')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Od'),
                        \Filament\Forms\Components\DatePicker::make('to')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('issue_date', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('issue_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make(),

                ActionGroup::make([
                    EditAction::make()
                        ->visible(fn (Invoice $record) => $record->status === 'draft'),

                    // Wystaw fakturę
                    Action::make('issue')
                        ->label('Wystaw')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Invoice $record) => $record->status === 'draft')
                        ->requiresConfirmation()
                        ->action(function (Invoice $record) {
                            $record->issue();
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Faktura wystawiona')
                                ->send();
                        }),

                    // Oznacz jako opłaconą
                    Action::make('mark_paid')
                        ->label('Oznacz jako opłaconą')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->visible(fn (Invoice $record) => ! $record->isPaid())
                        ->requiresConfirmation()
                        ->action(function (Invoice $record) {
                            $record->markAsPaid();
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Faktura oznaczona jako opłacona')
                                ->send();
                        }),

                    // Generuj w inFakt
                    Action::make('generate_infakt')
                        ->label('Generuj w inFakt')
                        ->icon('heroicon-o-document-plus')
                        ->color('primary')
                        ->visible(fn (Invoice $record) => ! $record->isSyncedWithInfakt())
                        ->requiresConfirmation()
                        ->action(function (Invoice $record) {
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
                        ->visible(fn (Invoice $record) => $record->hasInfaktPdf())
                        ->action(function (Invoice $record) {
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
                        ->label('Anuluj')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Invoice $record) => ! in_array($record->status, ['cancelled', 'paid']))
                        ->requiresConfirmation()
                        ->form([
                            \Filament\Forms\Components\Textarea::make('reason')
                                ->label('Powód anulowania')
                                ->required(),
                        ])
                        ->action(function (Invoice $record, array $data) {
                            $record->cancel($data['reason']);
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Faktura anulowana')
                                ->send();
                        }),

                    DeleteAction::make()
                        ->visible(fn (Invoice $record) => in_array($record->status, ['draft', 'cancelled'])),
                ]),
            ])
            ->bulkActions([
                BulkAction::make('mark_paid_bulk')
                    ->label('Oznacz jako opłacone')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->markAsPaid();
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Faktury oznaczone jako opłacone')
                            ->send();
                    }),

                BulkAction::make('export')
                    ->label('Eksportuj do CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        // TODO: Implementacja eksportu do CSV
                        \Filament\Notifications\Notification::make()
                            ->info()
                            ->title('Eksport w przygotowaniu')
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh co 30 sekund
    }
}
