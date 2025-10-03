<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Relation Manager dla zarządzania zwierzętami użytkownika.
 *
 * Umożliwia administratorom przeglądanie, tworzenie, edytowanie i usuwanie
 * zwierząt należących do danego użytkownika. Wyświetla podstawowe informacje
 * o zwierzętach wraz z ich specjalnymi potrzebami i danymi medycznymi.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PetsRelationManager extends RelationManager
{
    /**
     * Nazwa relacji z modelu User.
     */
    protected static string $relationship = 'pets';

    /**
     * Konfiguruje formularz tworzenia/edycji zwierzęcia.
     *
     * Formularz zawiera wszystkie niezbędne pola do rejestracji zwierzęcia,
     * w tym informacje podstawowe, medyczne i specjalne potrzeby.
     *
     * @param  Schema  $schema  Schemat formularza
     * @return Schema Skonfigurowany schemat
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Imię zwierzęcia')
                    ->required()
                    ->maxLength(255),

                Select::make('pet_type_id')
                    ->label('Typ zwierzęcia')
                    ->relationship('petType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('age')
                    ->label('Wiek (lata)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(50),

                TextInput::make('weight')
                    ->label('Waga (kg)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(200)
                    ->step(0.1),

                Textarea::make('special_needs')
                    ->label('Specjalne potrzeby')
                    ->rows(3)
                    ->maxLength(500),

                Textarea::make('medical_info')
                    ->label('Informacje medyczne')
                    ->rows(3)
                    ->maxLength(1000)
                    ->helperText('Choroby, leki, alergie, szczepienia itp.'),
            ]);
    }

    /**
     * Konfiguruje tabelę wyświetlającą zwierzęta użytkownika.
     *
     * Tabela pokazuje podstawowe informacje o zwierzętach wraz z informacją
     * o specjalnych potrzebach i datą dodania profilu.
     *
     * @param  Table  $table  Obiekt tabeli
     * @return Table Skonfigurowana tabela
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Imię')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('petType.name')
                    ->label('Typ zwierzęcia')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('age')
                    ->label('Wiek')
                    ->sortable()
                    ->suffix(' lat')
                    ->default('—'),

                IconColumn::make('special_needs')
                    ->label('Specjalne potrzeby')
                    ->boolean()
                    ->trueIcon(Heroicon::SolidExclamationCircle)
                    ->falseIcon(Heroicon::SolidCheckCircle)
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->getStateUsing(fn ($record) => ! empty($record->special_needs)),

                TextColumn::make('created_at')
                    ->label('Data dodania')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Dodaj zwierzę'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
