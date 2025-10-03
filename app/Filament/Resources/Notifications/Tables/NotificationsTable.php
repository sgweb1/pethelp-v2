<?php

namespace App\Filament\Resources\Notifications\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Konfiguracja tabeli dla NotificationResource w panelu administracyjnym Filament.
 *
 * Definiuje kolumny, filtry i akcje dla listy powiadomień systemowych.
 * Umożliwia przeglądanie powiadomień, statystyki otwarć oraz masowe wysyłanie.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class NotificationsTable
{
    /**
     * Konfiguruje tabelę powiadomień dla Filament Resource.
     *
     * @param  Table  $table  Instancja tabeli do konfiguracji
     * @return Table Skonfigurowana tabela
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ID
                TextColumn::make('id')
                    ->label('ID')
                    ->limit(8)
                    ->sortable()
                    ->toggleable(),

                // Typ powiadomienia
                TextColumn::make('type')
                    ->label('Typ')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable()
                    ->sortable(),

                // Odbiorca
                TextColumn::make('notifiable_id')
                    ->label('Odbiorca')
                    ->formatStateUsing(function ($record) {
                        if ($record->notifiable_type === User::class) {
                            $user = User::find($record->notifiable_id);

                            return $user ? $user->name.' ('.$user->email.')' : "User #{$record->notifiable_id}";
                        }

                        return "#{$record->notifiable_id}";
                    })
                    ->searchable()
                    ->wrap(),

                // Treść (z data JSON)
                TextColumn::make('data')
                    ->label('Treść')
                    ->formatStateUsing(function ($state) {
                        $data = is_string($state) ? json_decode($state, true) : $state;

                        return $data['message'] ?? $data['title'] ?? json_encode($data);
                    })
                    ->limit(50)
                    ->wrap()
                    ->searchable(),

                // Status przeczytania
                IconColumn::make('read_at')
                    ->label('Przeczytane')
                    ->boolean()
                    ->trueIcon(Heroicon::CheckCircle)
                    ->falseIcon(Heroicon::Clock)
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->getStateUsing(fn ($record) => $record->read_at !== null)
                    ->sortable(),

                // Data przeczytania
                TextColumn::make('read_at')
                    ->label('Data przeczytania')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Data utworzenia
                TextColumn::make('created_at')
                    ->label('Data wysłania')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // Filtr po statusie przeczytania
                TernaryFilter::make('read')
                    ->label('Status przeczytania')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('read_at'),
                        false: fn (Builder $query) => $query->whereNull('read_at'),
                    )
                    ->placeholder('Wszystkie powiadomienia')
                    ->trueLabel('Tylko przeczytane')
                    ->falseLabel('Tylko nieprzeczytane'),

                // Filtr po typie powiadomienia
                SelectFilter::make('type')
                    ->label('Typ powiadomienia')
                    ->options(function () {
                        return DatabaseNotification::query()
                            ->distinct()
                            ->pluck('type', 'type')
                            ->mapWithKeys(fn ($type) => [$type => class_basename($type)])
                            ->toArray();
                    })
                    ->placeholder('Wszystkie typy'),

                // Filtr po dacie utworzenia
                Filter::make('created_at')
                    ->label('Data wysłania')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Od'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),

                // Custom Action: Oznacz jako przeczytane
                Action::make('mark_as_read')
                    ->label('Oznacz jako przeczytane')
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->action(function (DatabaseNotification $record): void {
                        $record->markAsRead();
                    })
                    ->visible(fn (DatabaseNotification $record): bool => $record->read_at === null)
                    ->successNotification(
                        FilamentNotification::make()
                            ->success()
                            ->title('Powiadomienie oznaczone')
                            ->body('Powiadomienie zostało oznaczone jako przeczytane.')
                    ),

                // Custom Action: Oznacz jako nieprzeczytane
                Action::make('mark_as_unread')
                    ->label('Oznacz jako nieprzeczytane')
                    ->icon(Heroicon::Clock)
                    ->color('warning')
                    ->action(function (DatabaseNotification $record): void {
                        $record->update(['read_at' => null]);
                    })
                    ->visible(fn (DatabaseNotification $record): bool => $record->read_at !== null)
                    ->successNotification(
                        FilamentNotification::make()
                            ->success()
                            ->title('Powiadomienie oznaczone')
                            ->body('Powiadomienie zostało oznaczone jako nieprzeczytane.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Action: Oznacz jako przeczytane
                    BulkAction::make('mark_as_read_bulk')
                        ->label('Oznacz jako przeczytane')
                        ->icon(Heroicon::CheckCircle)
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each->markAsRead();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(function (Collection $records): FilamentNotification {
                            $count = $records->count();

                            return FilamentNotification::make()
                                ->success()
                                ->title('Powiadomienia oznaczone')
                                ->body("{$count} powiadomień zostało oznaczonych jako przeczytane.");
                        }),

                    // Bulk Action: Oznacz jako nieprzeczytane
                    BulkAction::make('mark_as_unread_bulk')
                        ->label('Oznacz jako nieprzeczytane')
                        ->icon(Heroicon::Clock)
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->update(['read_at' => null]));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(function (Collection $records): FilamentNotification {
                            $count = $records->count();

                            return FilamentNotification::make()
                                ->success()
                                ->title('Powiadomienia oznaczone')
                                ->body("{$count} powiadomień zostało oznaczonych jako nieprzeczytane.");
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
