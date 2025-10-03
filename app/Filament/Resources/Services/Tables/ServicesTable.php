<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Konfiguracja tabeli dla listy usług.
 *
 * Definiuje kolumny, filtry, akcje i zachowanie tabeli wyświetlającej
 * listę usług oferowanych przez opiekunów.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ServicesTable
{
    /**
     * Konfiguruje tabelę usług.
     *
     * Ustawia kolumny wyświetlające informacje o usługach, filtry do przeszukiwania
     * i custom actions do zarządzania usługami.
     *
     * @param  Table  $table  Pusty obiekt tabeli do konfiguracji
     * @return Table Skonfigurowana tabela
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ID
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Tytuł
                TextColumn::make('title')
                    ->label('Tytuł')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                // Kategoria
                TextColumn::make('category.name')
                    ->label('Kategoria')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                // Opiekun
                TextColumn::make('sitter.name')
                    ->label('Opiekun')
                    ->searchable()
                    ->sortable(),

                // Cena za godzinę
                TextColumn::make('price_per_hour')
                    ->label('Cena/godz')
                    ->money('PLN')
                    ->sortable(),

                // Cena za dzień
                TextColumn::make('price_per_day')
                    ->label('Cena/dzień')
                    ->money('PLN')
                    ->sortable(),

                // Promień usług
                TextColumn::make('service_radius')
                    ->label('Zasięg')
                    ->suffix(' km')
                    ->sortable(),

                // Status aktywności
                ToggleColumn::make('is_active')
                    ->label('Aktywna')
                    ->sortable(),

                // Liczba rezerwacji
                TextColumn::make('bookings_count')
                    ->label('Rezerwacje')
                    ->counts('bookings')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // Średnia ocena
                TextColumn::make('average_rating')
                    ->label('Ocena')
                    ->state(function ($record): string {
                        $rating = $record->average_rating;
                        if (! $rating) {
                            return 'Brak';
                        }

                        return number_format($rating, 1).' ⭐';
                    })
                    ->badge()
                    ->color(function ($record): string {
                        $rating = $record->average_rating;
                        if (! $rating) {
                            return 'gray';
                        }

                        return match (true) {
                            $rating >= 4.0 => 'success',
                            $rating >= 3.0 => 'warning',
                            default => 'danger',
                        };
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->withAvg('reviews', 'rating')
                            ->orderBy('reviews_avg_rating', $direction);
                    }),

                // Data utworzenia
                TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtr po kategorii
                SelectFilter::make('category_id')
                    ->label('Kategoria')
                    ->relationship('category', 'name')
                    ->preload(),

                // Filtr po opiekunie
                SelectFilter::make('sitter_id')
                    ->label('Opiekun')
                    ->relationship('sitter', 'name')
                    ->searchable()
                    ->preload(),

                // Filtr po statusie aktywności
                TernaryFilter::make('is_active')
                    ->label('Status aktywności'),

                // Filtr po przedziale cenowym
                SelectFilter::make('price_range')
                    ->label('Przedział cenowy (za godzinę)')
                    ->options([
                        '0-50' => 'do 50 PLN',
                        '50-100' => '50-100 PLN',
                        '100-200' => '100-200 PLN',
                        '200+' => 'powyżej 200 PLN',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            '0-50' => $query->where('price_per_hour', '<=', 50),
                            '50-100' => $query->whereBetween('price_per_hour', [50, 100]),
                            '100-200' => $query->whereBetween('price_per_hour', [100, 200]),
                            '200+' => $query->where('price_per_hour', '>', 200),
                            default => $query,
                        };
                    }),

                // Filtr po zasięgu
                SelectFilter::make('service_radius')
                    ->label('Zasięg usług')
                    ->options([
                        '0-5' => 'do 5 km',
                        '5-10' => '5-10 km',
                        '10-20' => '10-20 km',
                        '20+' => 'powyżej 20 km',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            '0-5' => $query->where('service_radius', '<=', 5),
                            '5-10' => $query->whereBetween('service_radius', [5, 10]),
                            '10-20' => $query->whereBetween('service_radius', [10, 20]),
                            '20+' => $query->where('service_radius', '>', 20),
                            default => $query,
                        };
                    }),

                // Filtr po typach zwierząt
                SelectFilter::make('pet_types')
                    ->label('Typ zwierzęcia')
                    ->options([
                        'dog' => 'Pies',
                        'cat' => 'Kot',
                        'bird' => 'Ptak',
                        'fish' => 'Ryba',
                        'rodent' => 'Gryzoń',
                        'reptile' => 'Gad',
                        'other' => 'Inne',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->whereJsonContains('pet_types', $data['value']);
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // Custom Action: Aktywuj usługę
                Action::make('activate')
                    ->label('Aktywuj')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['is_active' => true]);

                        Notification::make()
                            ->title('Usługa została aktywowana')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record): bool => ! $record->is_active),

                // Custom Action: Dezaktywuj usługę
                Action::make('deactivate')
                    ->label('Dezaktywuj')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['is_active' => false]);

                        Notification::make()
                            ->title('Usługa została dezaktywowana')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record): bool => $record->is_active),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    // Bulk Action: Aktywuj wiele
                    BulkAction::make('activate_multiple')
                        ->label('Aktywuj zaznaczone')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_active' => true]);

                            Notification::make()
                                ->title('Usługi zostały aktywowane')
                                ->body("Aktywowano {$records->count()} usług")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Action: Dezaktywuj wiele
                    BulkAction::make('deactivate_multiple')
                        ->label('Dezaktywuj zaznaczone')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_active' => false]);

                            Notification::make()
                                ->title('Usługi zostały dezaktywowane')
                                ->body("Dezaktywowano {$records->count()} usług")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
