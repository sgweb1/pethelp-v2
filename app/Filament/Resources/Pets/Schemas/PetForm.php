<?php

namespace App\Filament\Resources\Pets\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Schemat formularza dla zarządzania zwierzętami.
 *
 * Definiuje wszystkie pola formularza do tworzenia i edycji profili zwierząt,
 * w tym dane podstawowe, informacje medyczne, cechy behawioralne i potrzeby specjalne.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PetForm
{
    /**
     * Konfiguruje schemat formularza dla zwierząt.
     *
     * Tworzy kompletny formularz z polami podstawowymi oraz zaawansowanymi
     * sekcjami dla informacji medycznych, behawioralnych i kontaktów awaryjnych.
     *
     * @param  Schema  $schema  Pusty schemat do konfiguracji
     * @return Schema Skonfigurowany schemat formularza
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sekcja: Dane podstawowe
                Section::make('Dane podstawowe')
                    ->description('Podstawowe informacje o zwierzęciu')
                    ->schema([
                        Select::make('owner_id')
                            ->label('Właściciel')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Select::make('pet_type_id')
                            ->label('Typ zwierzęcia')
                            ->relationship('petType', 'name')
                            ->searchable()
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('name')
                            ->label('Imię')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('breed')
                            ->label('Rasa')
                            ->maxLength(255)
                            ->columnSpan(1),

                        DatePicker::make('birth_date')
                            ->label('Data urodzenia')
                            ->maxDate(today())
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('gender')
                            ->label('Płeć')
                            ->options([
                                'male' => 'Samiec',
                                'female' => 'Samica',
                                'unknown' => 'Nieznana',
                            ])
                            ->columnSpan(1),

                        TextInput::make('weight')
                            ->label('Waga')
                            ->numeric()
                            ->step(0.1)
                            ->suffix('kg')
                            ->columnSpan(1),

                        Toggle::make('is_active')
                            ->label('Profil aktywny')
                            ->default(true)
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label('Opis')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Sekcja: Zdjęcie
                Section::make('Zdjęcie')
                    ->description('Zdjęcie profilowe zwierzęcia')
                    ->schema([
                        FileUpload::make('photo_url')
                            ->label('Zdjęcie')
                            ->image()
                            ->maxSize(2048) // 2MB
                            ->directory('pets/photos')
                            ->columnSpanFull(),
                    ]),

                // Sekcja: Specjalne potrzeby
                Section::make('Specjalne potrzeby')
                    ->description('Wybierz wszystkie specjalne potrzeby zwierzęcia')
                    ->schema([
                        CheckboxList::make('special_needs')
                            ->label('Potrzeby')
                            ->options([
                                'medication' => 'Leki',
                                'exercise' => 'Specjalne ćwiczenia',
                                'diet' => 'Specjalna dieta',
                                'elderly' => 'Opieka senioralna',
                                'medical' => 'Opieka medyczna',
                                'training' => 'Trening',
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),

                // Sekcja: Informacje medyczne
                Section::make('Informacje medyczne')
                    ->description('Schorzenia, leki i dane weterynarza')
                    ->schema([
                        Repeater::make('medical_info')
                            ->label('Historia medyczna')
                            ->schema([
                                TextInput::make('condition')
                                    ->label('Schorzenie/Stan')
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('medication')
                                    ->label('Leki')
                                    ->columnSpan(1),

                                TextInput::make('vet_contact')
                                    ->label('Kontakt do weterynarza')
                                    ->tel()
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),

                // Sekcja: Cechy behawioralne
                Section::make('Cechy behawioralne')
                    ->description('Zachowania i temperament zwierzęcia')
                    ->schema([
                        Repeater::make('behavior_traits')
                            ->label('Cechy')
                            ->schema([
                                TextInput::make('trait')
                                    ->label('Cecha')
                                    ->required()
                                    ->placeholder('np. Przyjazny dla innych psów')
                                    ->columnSpan(1),

                                Textarea::make('description')
                                    ->label('Opis')
                                    ->rows(2)
                                    ->columnSpan(2),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),

                // Sekcja: Kontakty awaryjne
                Section::make('Kontakty awaryjne')
                    ->description('Osoby do kontaktu w nagłych wypadkach')
                    ->schema([
                        Repeater::make('emergency_contacts')
                            ->label('Kontakty')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Imię i nazwisko')
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('phone')
                                    ->label('Telefon')
                                    ->tel()
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('relation')
                                    ->label('Relacja')
                                    ->placeholder('np. Sąsiad, Rodzina')
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
