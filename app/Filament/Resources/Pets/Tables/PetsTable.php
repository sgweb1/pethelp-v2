<?php

namespace App\Filament\Resources\Pets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Konfiguracja tabeli dla listy zwierząt.
 *
 * Definiuje kolumny, filtry, akcje i zachowanie tabeli wyświetlającej
 * listę zwierząt w panelu administracyjnym.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PetsTable
{
    /**
     * Konfiguruje tabelę zwierząt.
     *
     * Ustawia kolumny wyświetlające podstawowe informacje o zwierzętach,
     * filtry do przeszukiwania i akcje zarządzania rekordami.
     *
     * @param  Table  $table  Pusty obiekt tabeli do konfiguracji
     * @return Table Skonfigurowana tabela
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Zdjęcie profilowe
                ImageColumn::make('photo_url')
                    ->label('Zdjęcie')
                    ->circular()
                    ->defaultImageUrl(asset('images/pet-placeholder.png')),

                // Imię zwierzęcia
                TextColumn::make('name')
                    ->label('Imię')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // Typ zwierzęcia
                TextColumn::make('petType.name')
                    ->label('Typ')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                // Właściciel
                TextColumn::make('owner.name')
                    ->label('Właściciel')
                    ->searchable()
                    ->sortable(),

                // Wiek
                TextColumn::make('age')
                    ->label('Wiek')
                    ->state(function ($record): string {
                        if (! $record->age) {
                            return 'Nieznany';
                        }

                        return $record->age.' '.($record->age === 1 ? 'rok' : ($record->age < 5 ? 'lata' : 'lat'));
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) {$direction}");
                    }),

                // Waga
                TextColumn::make('weight')
                    ->label('Waga')
                    ->suffix(' kg')
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),

                // Specjalne potrzeby
                IconColumn::make('has_special_needs')
                    ->label('Spec. potrzeby')
                    ->boolean()
                    ->state(function ($record): bool {
                        return $record->hasSpecialNeeds();
                    })
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success'),

                // Status aktywności
                ToggleColumn::make('is_active')
                    ->label('Aktywny')
                    ->sortable(),

                // Data utworzenia
                TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtr po typie zwierzęcia
                SelectFilter::make('pet_type_id')
                    ->label('Typ zwierzęcia')
                    ->relationship('petType', 'name')
                    ->preload(),

                // Filtr po właścicielu
                SelectFilter::make('owner_id')
                    ->label('Właściciel')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                // Filtr po grupie wiekowej
                SelectFilter::make('age_group')
                    ->label('Grupa wiekowa')
                    ->options([
                        'young' => 'Młody (0-3 lata)',
                        'adult' => 'Dorosły (3-8 lat)',
                        'senior' => 'Senior (8+ lat)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'young' => $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 3'),
                            'adult' => $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 3 AND 7'),
                            'senior' => $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 8'),
                            default => $query,
                        };
                    }),

                // Filtr po specjalnych potrzebach
                TernaryFilter::make('has_special_needs')
                    ->label('Ma specjalne potrzeby')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('special_needs')
                            ->where(function ($q) {
                                $q->whereRaw('JSON_LENGTH(special_needs) > 0')
                                    ->orWhereRaw("special_needs != '[]'");
                            }),
                        false: fn (Builder $query) => $query->where(function ($q) {
                            $q->whereNull('special_needs')
                                ->orWhereRaw('JSON_LENGTH(special_needs) = 0')
                                ->orWhereRaw("special_needs = '[]'");
                        }),
                    ),

                // Filtr po statusie aktywności
                TernaryFilter::make('is_active')
                    ->label('Aktywny profil'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
