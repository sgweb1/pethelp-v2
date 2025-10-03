<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Relation Manager dla zarządzania usługami opiekuna.
 *
 * Umożliwia administratorom przeglądanie i edytowanie usług oferowanych
 * przez użytkownika w roli opiekuna zwierząt. Wyświetla informacje o cenach,
 * kategoriach usług, promieniu działania i statusie aktywności.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ServicesRelationManager extends RelationManager
{
    /**
     * Nazwa relacji z modelu User.
     */
    protected static string $relationship = 'services';

    /**
     * Konfiguruje formularz edycji usługi.
     *
     * Formularz umożliwia edycję podstawowych parametrów usługi takich jak
     * kategoria, cena, promień działania, status i opis.
     *
     * @param  Schema  $schema  Schemat formularza
     * @return Schema Skonfigurowany schemat
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategoria usługi')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('price_per_hour')
                    ->label('Cena za godzinę')
                    ->numeric()
                    ->prefix('PLN')
                    ->minValue(0)
                    ->step(0.01),

                TextInput::make('price_per_day')
                    ->label('Cena za dzień')
                    ->numeric()
                    ->prefix('PLN')
                    ->minValue(0)
                    ->step(0.01),

                TextInput::make('service_radius')
                    ->label('Promień działania')
                    ->numeric()
                    ->suffix('km')
                    ->minValue(0)
                    ->maxValue(100)
                    ->helperText('Maksymalny dystans od lokalizacji opiekuna'),

                Select::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Aktywna',
                        false => 'Nieaktywna',
                    ])
                    ->required()
                    ->default(true),

                Textarea::make('description')
                    ->label('Opis usługi')
                    ->rows(4)
                    ->maxLength(2000),
            ]);
    }

    /**
     * Konfiguruje tabelę wyświetlającą usługi opiekuna.
     *
     * Tabela pokazuje wszystkie usługi użytkownika z możliwością filtrowania
     * po statusie i kategorii. Nie ma możliwości usuwania usług, tylko zmiana
     * statusu na nieaktywny (soft delete).
     *
     * @param  Table  $table  Obiekt tabeli
     * @return Table Skonfigurowana tabela
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('category.name')
                    ->label('Kategoria')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price_per_hour')
                    ->label('Cena/godz.')
                    ->money('PLN')
                    ->sortable()
                    ->default('—'),

                TextColumn::make('price_per_day')
                    ->label('Cena/dzień')
                    ->money('PLN')
                    ->sortable()
                    ->default('—'),

                TextColumn::make('service_radius')
                    ->label('Promień')
                    ->sortable()
                    ->suffix(' km')
                    ->default('—'),

                BadgeColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktywna' : 'Nieaktywna')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ]),

                TextColumn::make('created_at')
                    ->label('Data utworzenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Aktywne',
                        false => 'Nieaktywne',
                    ]),

                SelectFilter::make('category_id')
                    ->label('Kategoria')
                    ->relationship('category', 'name'),
            ])
            ->headerActions([
                // Brak możliwości tworzenia - usługi tworzone przez użytkownika
            ])
            ->recordActions([
                EditAction::make(),
                // Brak DeleteAction - zarządzanie przez status is_active
            ])
            ->toolbarActions([
                // Brak bulk actions
            ]);
    }
}
