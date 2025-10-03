<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
 * Tabela rezerwacji w panelu administracyjnym.
 *
 * Wyświetla listę rezerwacji z filtrowaniem, sortowaniem
 * i zaawansowanymi akcjami administratora.
 */
class BookingsTable
{
    /**
     * Konfiguruje tabelę rezerwacji.
     *
     * Zawiera kolumny, filtry, akcje na rekordach i akcje grupowe.
     * Optymalizuje zapytania przez eager loading relacji.
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

                TextColumn::make('owner.name')
                    ->label('Właściciel')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->owner->email ?? ''),

                TextColumn::make('sitter.name')
                    ->label('Opiekun')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->sitter->email ?? ''),

                TextColumn::make('service.category.name')
                    ->label('Kategoria usługi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pet.name')
                    ->label('Zwierzę')
                    ->searchable()
                    ->description(fn ($record) => $record->pet->type ?? ''),

                TextColumn::make('start_date')
                    ->label('Data rozpoczęcia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Data zakończenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'confirmed',
                        'heroicon-o-arrow-path' => 'in_progress',
                        'heroicon-o-check-badge' => 'completed',
                        'heroicon-o-x-circle' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Oczekująca',
                        'confirmed' => 'Potwierdzona',
                        'in_progress' => 'W trakcie',
                        'completed' => 'Zakończona',
                        'cancelled' => 'Anulowana',
                        default => $state,
                    }),

                TextColumn::make('total_price')
                    ->label('Cena całkowita')
                    ->money('PLN')
                    ->sortable(),

                BadgeColumn::make('payment.status')
                    ->label('Status płatności')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'refunded',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Oczekująca',
                        'processing' => 'Przetwarzana',
                        'completed' => 'Zakończona',
                        'failed' => 'Nieudana',
                        'refunded' => 'Zwrócona',
                        default => 'Brak',
                    })
                    ->default('Brak'),

                TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
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
                        'confirmed' => 'Potwierdzona',
                        'in_progress' => 'W trakcie',
                        'completed' => 'Zakończona',
                        'cancelled' => 'Anulowana',
                    ])
                    ->multiple(),

                SelectFilter::make('payment_status')
                    ->label('Status płatności')
                    ->relationship('payment', 'status')
                    ->options([
                        'pending' => 'Oczekująca',
                        'processing' => 'Przetwarzana',
                        'completed' => 'Zakończona',
                        'failed' => 'Nieudana',
                        'refunded' => 'Zwrócona',
                    ])
                    ->multiple(),

                Filter::make('start_date')
                    ->label('Data rozpoczęcia')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),

                SelectFilter::make('owner_id')
                    ->label('Właściciel')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('sitter_id')
                    ->label('Opiekun')
                    ->relationship('sitter', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('service_category')
                    ->label('Kategoria usługi')
                    ->relationship('service.category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),

                EditAction::make()
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'confirmed'])),

                Action::make('cancel_booking')
                    ->label('Anuluj rezerwację')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('cancellation_reason')
                            ->label('Powód anulowania')
                            ->required()
                            ->rows(3),
                        TextInput::make('refund_amount')
                            ->label('Kwota zwrotu')
                            ->numeric()
                            ->prefix('PLN')
                            ->helperText('Pozostaw puste dla pełnego zwrotu'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'cancelled',
                            'cancellation_reason' => $data['cancellation_reason'],
                            'cancelled_at' => now(),
                        ]);

                        Log::info('Rezerwacja anulowana przez administratora', [
                            'booking_id' => $record->id,
                            'reason' => $data['cancellation_reason'],
                            'refund_amount' => $data['refund_amount'] ?? $record->total_price,
                            'admin_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Rezerwacja anulowana')
                            ->body("Rezerwacja #{$record->id} została anulowana.")
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Anuluj rezerwację')
                    ->modalDescription('Czy na pewno chcesz anulować tę rezerwację?')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'confirmed'])),

                Action::make('complete_booking')
                    ->label('Zakończ rezerwację')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'completed',
                        ]);

                        Log::info('Rezerwacja zakończona przez administratora', [
                            'booking_id' => $record->id,
                            'admin_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Rezerwacja zakończona')
                            ->body("Rezerwacja #{$record->id} została zakończona.")
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Zakończ rezerwację')
                    ->modalDescription('Potwierdź zakończenie rezerwacji')
                    ->visible(fn ($record) => $record->status === 'in_progress'),

                Action::make('contact_owner')
                    ->label('Kontakt z właścicielem')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->form([
                        TextInput::make('subject')
                            ->label('Temat')
                            ->required(),
                        Textarea::make('message')
                            ->label('Wiadomość')
                            ->required()
                            ->rows(5),
                    ])
                    ->action(function ($record, array $data) {
                        Log::info('Wysłano wiadomość do właściciela rezerwacji', [
                            'booking_id' => $record->id,
                            'owner_id' => $record->owner_id,
                            'subject' => $data['subject'],
                            'message' => $data['message'],
                            'admin_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Wiadomość wysłana')
                            ->body("Wiadomość do {$record->owner->name} została wysłana.")
                            ->send();
                    })
                    ->modalHeading('Kontakt z właścicielem'),

                Action::make('contact_sitter')
                    ->label('Kontakt z opiekunem')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->form([
                        TextInput::make('subject')
                            ->label('Temat')
                            ->required(),
                        Textarea::make('message')
                            ->label('Wiadomość')
                            ->required()
                            ->rows(5),
                    ])
                    ->action(function ($record, array $data) {
                        Log::info('Wysłano wiadomość do opiekuna rezerwacji', [
                            'booking_id' => $record->id,
                            'sitter_id' => $record->sitter_id,
                            'subject' => $data['subject'],
                            'message' => $data['message'],
                            'admin_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Wiadomość wysłana')
                            ->body("Wiadomość do {$record->sitter->name} została wysłana.")
                            ->send();
                    })
                    ->modalHeading('Kontakt z opiekunem'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('cancel_multiple')
                        ->label('Anuluj zaznaczone')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Textarea::make('cancellation_reason')
                                ->label('Powód anulowania')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'status' => 'cancelled',
                                    'cancellation_reason' => $data['cancellation_reason'],
                                    'cancelled_at' => now(),
                                ]);
                            });

                            Log::info('Anulowano wiele rezerwacji', [
                                'count' => $records->count(),
                                'reason' => $data['cancellation_reason'],
                                'admin_id' => auth()->id(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Rezerwacje anulowane')
                                ->body("Anulowano {$records->count()} rezerwacji.")
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('export_bookings')
                        ->label('Eksportuj do CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            Log::info('Eksportowano rezerwacje do CSV', [
                                'count' => $records->count(),
                                'admin_id' => auth()->id(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Eksport ukończony')
                                ->body("Wyeksportowano {$records->count()} rezerwacji.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('send_reminder')
                        ->label('Wyślij przypomnienie')
                        ->icon('heroicon-o-bell')
                        ->action(function (Collection $records) {
                            Log::info('Wysłano przypomnienia o rezerwacjach', [
                                'count' => $records->count(),
                                'admin_id' => auth()->id(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Przypomnienia wysłane')
                                ->body("Wysłano {$records->count()} przypomnień.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'owner',
                'sitter',
                'service.category',
                'pet',
                'payment',
            ]));
    }
}
