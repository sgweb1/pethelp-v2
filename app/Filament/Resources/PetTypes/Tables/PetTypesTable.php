<?php

namespace App\Filament\Resources\PetTypes\Tables;

use App\Models\PetType;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Konfiguracja tabeli dla PetTypeResource w panelu administracyjnym Filament.
 *
 * Definiuje kolumny, filtry i akcje dla listy typów zwierząt. Tabela zawiera
 * informacje o typach zwierząt, ich statusie, liczbie zarejestrowanych zwierząt
 * danego typu oraz kolejności sortowania.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PetTypesTable
{
    /**
     * Konfiguruje tabelę typów zwierząt dla Filament Resource.
     *
     * Ustawia kolumny wyświetlane w tabeli, filtry umożliwiające
     * wyszukiwanie i filtrowanie danych, oraz akcje dostępne
     * dla poszczególnych rekordów i grup rekordów.
     *
     * @param  Table  $table  Instancja tabeli do konfiguracji
     * @return Table Skonfigurowana tabela
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ID - sortowalne
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(),

                // Nazwa typu - sortowalna, przeszukiwalna, kopiowalna
                TextColumn::make('name')
                    ->label('Nazwa typu')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nazwa skopiowana')
                    ->copyMessageDuration(1500)
                    ->weight('bold')
                    ->description(fn ($record) => $record->description ? \Illuminate\Support\Str::limit($record->description, 60) : null),

                // Slug - sortowalne, przeszukiwalne, kopiowalne
                TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Slug skopiowany')
                    ->copyMessageDuration(1500)
                    ->color('gray')
                    ->toggleable(),

                // Ikona - wyświetlana jako tekst
                TextColumn::make('icon')
                    ->label('Ikona')
                    ->formatStateUsing(fn ($state) => $state ?: 'Brak ikony')
                    ->color('gray')
                    ->toggleable(),

                // Liczba zwierząt - badge z licznikiem
                TextColumn::make('pets_count')
                    ->label('Liczba zwierząt')
                    ->counts('pets')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 0 => 'gray',
                        $state < 10 => 'warning',
                        $state >= 10 => 'success',
                        default => 'info',
                    }),

                // Kolejność sortowania - sortowalna
                TextColumn::make('sort_order')
                    ->label('Kolejność')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                // Status aktywności - ikona boolean
                IconColumn::make('is_active')
                    ->label('Aktywny')
                    ->boolean()
                    ->trueIcon(Heroicon::CheckCircle)
                    ->falseIcon(Heroicon::XCircle)
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                // Data utworzenia - ukryta domyślnie
                TextColumn::make('created_at')
                    ->label('Data utworzenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Data aktualizacji - ukryta domyślnie
                TextColumn::make('updated_at')
                    ->label('Ostatnia aktualizacja')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtr po statusie aktywności
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                    )
                    ->placeholder('Wszystkie typy')
                    ->trueLabel('Tylko aktywne')
                    ->falseLabel('Tylko nieaktywne'),

                // Filtr po liczbie zwierząt
                SelectFilter::make('pets_count')
                    ->label('Liczba zwierząt')
                    ->options([
                        'none' => 'Bez zwierząt (0)',
                        'few' => 'Mało zwierząt (1-9)',
                        'many' => 'Dużo zwierząt (10+)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'none',
                            fn (Builder $q) => $q->doesntHave('pets')
                        )->when(
                            $data['value'] === 'few',
                            fn (Builder $q) => $q->has('pets', '>=', 1)->has('pets', '<=', 9)
                        )->when(
                            $data['value'] === 'many',
                            fn (Builder $q) => $q->has('pets', '>=', 10)
                        );
                    }),
            ])
            ->recordActions([
                EditAction::make(),

                // Custom Action: Aktywuj typ
                Action::make('activate')
                    ->label('Aktywuj')
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->action(fn (PetType $record) => $record->update(['is_active' => true]))
                    ->visible(fn (PetType $record): bool => ! $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Aktywuj typ zwierzęcia')
                    ->modalDescription('Czy na pewno chcesz aktywować ten typ?')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Typ aktywowany')
                            ->body('Typ zwierzęcia został pomyślnie aktywowany.')
                    ),

                // Custom Action: Dezaktywuj typ
                Action::make('deactivate')
                    ->label('Dezaktywuj')
                    ->icon(Heroicon::XCircle)
                    ->color('warning')
                    ->action(fn (PetType $record) => $record->update(['is_active' => false]))
                    ->visible(fn (PetType $record): bool => $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Dezaktywuj typ zwierzęcia')
                    ->modalDescription('Czy na pewno chcesz dezaktywować ten typ? Zwierzęta tego typu pozostaną w systemie.')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Typ dezaktywowany')
                            ->body('Typ zwierzęcia został pomyślnie dezaktywowany.')
                    ),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    // Bulk Action: Aktywuj zaznaczone typy
                    BulkAction::make('activate_bulk')
                        ->label('Aktywuj zaznaczone')
                        ->icon(Heroicon::CheckCircle)
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn (PetType $type) => $type->update(['is_active' => true]));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Aktywuj zaznaczone typy')
                        ->modalDescription('Czy na pewno chcesz aktywować wszystkie zaznaczone typy zwierząt?')
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Typy aktywowane')
                                ->body("{$count} typów zostało pomyślnie aktywowanych.");
                        }),

                    // Bulk Action: Dezaktywuj zaznaczone typy
                    BulkAction::make('deactivate_bulk')
                        ->label('Dezaktywuj zaznaczone')
                        ->icon(Heroicon::XCircle)
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $records->each(fn (PetType $type) => $type->update(['is_active' => false]));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Dezaktywuj zaznaczone typy')
                        ->modalDescription('Czy na pewno chcesz dezaktywować wszystkie zaznaczone typy zwierząt?')
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Typy dezaktywowane')
                                ->body("{$count} typów zostało pomyślnie dezaktywowanych.");
                        }),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->modifyQueryUsing(function (Builder $query) {
                // Eager loading dla optymalizacji - ładuj liczbę zwierząt
                return $query->withCount('pets');
            });
    }
}
