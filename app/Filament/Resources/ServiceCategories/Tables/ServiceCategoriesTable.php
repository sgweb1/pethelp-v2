<?php

namespace App\Filament\Resources\ServiceCategories\Tables;

use App\Models\ServiceCategory;
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
 * Konfiguracja tabeli dla ServiceCategoryResource w panelu administracyjnym Filament.
 *
 * Definiuje kolumny, filtry i akcje dla listy kategorii usług. Tabela zawiera
 * informacje o kategoriach, ich statusie, liczbie przypisanych usług oraz
 * kolejności sortowania.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ServiceCategoriesTable
{
    /**
     * Konfiguruje tabelę kategorii usług dla Filament Resource.
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

                // Nazwa kategorii - sortowalna, przeszukiwalna, kopiowalna
                TextColumn::make('name')
                    ->label('Nazwa kategorii')
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

                // Liczba usług - badge z licznikiem
                TextColumn::make('services_count')
                    ->label('Liczba usług')
                    ->counts('services')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 0 => 'gray',
                        $state < 5 => 'warning',
                        $state >= 5 => 'success',
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
                    ->label('Aktywna')
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
                    ->placeholder('Wszystkie kategorie')
                    ->trueLabel('Tylko aktywne')
                    ->falseLabel('Tylko nieaktywne'),

                // Filtr po liczbie usług
                SelectFilter::make('services_count')
                    ->label('Liczba usług')
                    ->options([
                        'none' => 'Bez usług (0)',
                        'few' => 'Mało usług (1-4)',
                        'many' => 'Dużo usług (5+)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'none',
                            fn (Builder $q) => $q->doesntHave('services')
                        )->when(
                            $data['value'] === 'few',
                            fn (Builder $q) => $q->has('services', '>=', 1)->has('services', '<=', 4)
                        )->when(
                            $data['value'] === 'many',
                            fn (Builder $q) => $q->has('services', '>=', 5)
                        );
                    }),
            ])
            ->recordActions([
                EditAction::make(),

                // Custom Action: Aktywuj kategorię
                Action::make('activate')
                    ->label('Aktywuj')
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->action(fn (ServiceCategory $record) => $record->update(['is_active' => true]))
                    ->visible(fn (ServiceCategory $record): bool => ! $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Aktywuj kategorię')
                    ->modalDescription('Czy na pewno chcesz aktywować tę kategorię?')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Kategoria aktywowana')
                            ->body('Kategoria została pomyślnie aktywowana.')
                    ),

                // Custom Action: Dezaktywuj kategorię
                Action::make('deactivate')
                    ->label('Dezaktywuj')
                    ->icon(Heroicon::XCircle)
                    ->color('warning')
                    ->action(fn (ServiceCategory $record) => $record->update(['is_active' => false]))
                    ->visible(fn (ServiceCategory $record): bool => $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Dezaktywuj kategorię')
                    ->modalDescription('Czy na pewno chcesz dezaktywować tę kategorię? Usługi w tej kategorii pozostaną dostępne.')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Kategoria dezaktywowana')
                            ->body('Kategoria została pomyślnie dezaktywowana.')
                    ),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    // Bulk Action: Aktywuj zaznaczone kategorie
                    BulkAction::make('activate_bulk')
                        ->label('Aktywuj zaznaczone')
                        ->icon(Heroicon::CheckCircle)
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn (ServiceCategory $category) => $category->update(['is_active' => true]));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Aktywuj zaznaczone kategorie')
                        ->modalDescription('Czy na pewno chcesz aktywować wszystkie zaznaczone kategorie?')
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Kategorie aktywowane')
                                ->body("{$count} kategorii zostało pomyślnie aktywowanych.");
                        }),

                    // Bulk Action: Dezaktywuj zaznaczone kategorie
                    BulkAction::make('deactivate_bulk')
                        ->label('Dezaktywuj zaznaczone')
                        ->icon(Heroicon::XCircle)
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $records->each(fn (ServiceCategory $category) => $category->update(['is_active' => false]));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Dezaktywuj zaznaczone kategorie')
                        ->modalDescription('Czy na pewno chcesz dezaktywować wszystkie zaznaczone kategorie?')
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Kategorie dezaktywowane')
                                ->body("{$count} kategorii zostało pomyślnie dezaktywowanych.");
                        }),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->modifyQueryUsing(function (Builder $query) {
                // Eager loading dla optymalizacji - ładuj liczbę usług
                return $query->withCount('services');
            });
    }
}
