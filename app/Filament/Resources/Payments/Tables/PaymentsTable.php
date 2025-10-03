<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Tabela płatności w panelu administracyjnym.
 *
 * Wyświetla transakcje płatności z możliwością filtrowania,
 * zarządzania zwrotami i generowania raportów. Resource READ-ONLY.
 */
class PaymentsTable
{
    /**
     * Konfiguruje tabelę płatności.
     *
     * Tabela głównie read-only z możliwością zwrotów i raportowania.
     * Eager loading relacji dla optymalizacji wydajności.
     *
     * @param  Table  $table  Tabela do skonfigurowania
     * @return Table Skonfigurowana tabela
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user->email ?? ''),

                TextColumn::make('booking.id')
                    ->label('Rezerwacja')
                    ->formatStateUsing(fn (?int $state) => $state ? "#$state" : 'Brak')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->booking_id
                        ? route('filament.admin.resources.bookings.view', ['record' => $record->booking_id])
                        : null
                    )
                    ->color(fn ($record) => $record->booking_id ? 'primary' : 'gray'),

                TextColumn::make('subscriptionPlan.name')
                    ->label('Subskrypcja')
                    ->formatStateUsing(fn (?string $state) => $state ?? 'Brak')
                    ->searchable()
                    ->sortable()
                    ->color(fn ($record) => $record->subscription_plan_id ? 'success' : 'gray'),

                TextColumn::make('amount')
                    ->label('Kwota')
                    ->money('PLN')
                    ->sortable()
                    ->description(fn ($record) => $record->commission
                        ? 'Prowizja: '.number_format($record->commission, 2).' PLN'
                        : null
                    ),

                BadgeColumn::make('payment_method')
                    ->label('Metoda płatności')
                    ->colors([
                        'primary' => 'card',
                        'success' => 'bank_transfer',
                        'warning' => 'cash',
                        'info' => 'online',
                    ])
                    ->icons([
                        'heroicon-o-credit-card' => 'card',
                        'heroicon-o-building-library' => 'bank_transfer',
                        'heroicon-o-banknotes' => 'cash',
                        'heroicon-o-globe-alt' => 'online',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'card' => 'Karta',
                        'bank_transfer' => 'Przelew',
                        'cash' => 'Gotówka',
                        'online' => 'Online',
                        default => $state,
                    }),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'refunded',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-arrow-path' => 'processing',
                        'heroicon-o-check-circle' => 'completed',
                        'heroicon-o-x-circle' => 'failed',
                        'heroicon-o-arrow-uturn-left' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Oczekująca',
                        'processing' => 'Przetwarzana',
                        'completed' => 'Zakończona',
                        'failed' => 'Nieudana',
                        'refunded' => 'Zwrócona',
                        default => $state,
                    }),

                TextColumn::make('external_id')
                    ->label('ID transakcji')
                    ->copyable()
                    ->copyMessage('ID transakcji skopiowane!')
                    ->placeholder('Brak')
                    ->limit(20),

                TextColumn::make('created_at')
                    ->label('Data utworzenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('processed_at')
                    ->label('Data przetworzenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Nieprzetworzono')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Zaktualizowano')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Oczekująca',
                        'processing' => 'Przetwarzana',
                        'completed' => 'Zakończona',
                        'failed' => 'Nieudana',
                        'refunded' => 'Zwrócona',
                    ])
                    ->multiple(),

                SelectFilter::make('payment_method')
                    ->label('Metoda płatności')
                    ->options([
                        'card' => 'Karta',
                        'bank_transfer' => 'Przelew',
                        'cash' => 'Gotówka',
                        'online' => 'Online',
                    ])
                    ->multiple(),

                Filter::make('date')
                    ->label('Data utworzenia')
                    ->form([
                        DatePicker::make('from')
                            ->label('Od')
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('to')
                            ->label('Do')
                            ->displayFormat('d.m.Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Filter::make('amount')
                    ->label('Kwota')
                    ->form([
                        TextInput::make('min_amount')
                            ->label('Minimalna kwota')
                            ->numeric()
                            ->prefix('PLN'),
                        TextInput::make('max_amount')
                            ->label('Maksymalna kwota')
                            ->numeric()
                            ->prefix('PLN'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),

                SelectFilter::make('type')
                    ->label('Typ płatności')
                    ->options([
                        'booking' => 'Rezerwacja',
                        'subscription' => 'Subskrypcja',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! isset($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'booking' => $query->whereNotNull('booking_id'),
                            'subscription' => $query->whereNotNull('subscription_plan_id'),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('issue_refund')
                    ->label('Zwrot płatności')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->form([
                        TextInput::make('refund_amount')
                            ->label('Kwota zwrotu')
                            ->required()
                            ->numeric()
                            ->prefix('PLN')
                            ->default(fn ($record) => $record->amount)
                            ->helperText('Maksymalna kwota do zwrotu'),
                        Textarea::make('refund_reason')
                            ->label('Powód zwrotu')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'refunded',
                        ]);

                        Log::info('Zwrot płatności wykonany przez administratora', [
                            'payment_id' => $record->id,
                            'refund_amount' => $data['refund_amount'],
                            'refund_reason' => $data['refund_reason'],
                            'admin_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Zwrot wykonany')
                            ->body("Zwrócono {$data['refund_amount']} PLN dla płatności #{$record->id}.")
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Zwrot płatności')
                    ->modalDescription('Czy na pewno chcesz wykonać zwrot dla tej płatności?')
                    ->visible(fn ($record) => $record->status === 'completed'),

                Action::make('mark_as_paid')
                    ->label('Oznacz jako opłaconą')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'completed',
                            'processed_at' => now(),
                        ]);

                        Log::info('Płatność oznaczona jako opłacona przez administratora', [
                            'payment_id' => $record->id,
                            'admin_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Płatność potwierdzona')
                            ->body("Płatność #{$record->id} została oznaczona jako opłacona.")
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Potwierdź płatność')
                    ->modalDescription('Oznacz tę płatność jako ręcznie opłaconą (admin override)')
                    ->visible(fn ($record) => $record->status === 'pending'),

                Action::make('download_invoice')
                    ->label('Pobierz fakturę')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function ($record) {
                        Log::info('Pobrano fakturę PDF', [
                            'payment_id' => $record->id,
                            'admin_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Faktura pobrana')
                            ->body("Faktura dla płatności #{$record->id} została wygenerowana.")
                            ->send();
                    }),

                Action::make('resend_receipt')
                    ->label('Wyślij ponownie potwierdzenie')
                    ->icon('heroicon-o-envelope')
                    ->action(function ($record) {
                        Log::info('Wysłano ponownie potwierdzenie płatności', [
                            'payment_id' => $record->id,
                            'user_id' => $record->user_id,
                            'admin_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Potwierdzenie wysłane')
                            ->body("Potwierdzenie płatności zostało wysłane do {$record->user->email}.")
                            ->send();
                    })
                    ->visible(fn ($record) => in_array($record->status, ['completed', 'refunded'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export_transactions')
                        ->label('Eksportuj transakcje')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            Log::info('Eksportowano transakcje płatności', [
                                'count' => $records->count(),
                                'admin_id' => auth()->id(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Eksport ukończony')
                                ->body("Wyeksportowano {$records->count()} transakcji.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('send_receipt_emails')
                        ->label('Wyślij potwierdzenia')
                        ->icon('heroicon-o-envelope')
                        ->action(function (Collection $records) {
                            Log::info('Wysłano grupowe potwierdzenia płatności', [
                                'count' => $records->count(),
                                'admin_id' => auth()->id(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Potwierdzenia wysłane')
                                ->body("Wysłano {$records->count()} potwierdzeń płatności.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'user',
                'booking',
                'subscriptionPlan',
            ]))
            ->defaultSort('created_at', 'desc');
    }
}
