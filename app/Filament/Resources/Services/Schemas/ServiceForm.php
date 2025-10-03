<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

/**
 * Schemat formularza dla zarządzania usługami opiekunów.
 *
 * Definiuje wszystkie pola formularza do tworzenia i edycji usług,
 * w tym informacje podstawowe, cennik, szczegóły usługi i status.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ServiceForm
{
    /**
     * Konfiguruje schemat formularza dla usług.
     *
     * Tworzy formularz z zakładkami dla różnych kategorii informacji
     * o usłudze oferowanej przez opiekuna.
     *
     * @param  Schema  $schema  Pusty schemat do konfiguracji
     * @return Schema Skonfigurowany schemat formularza
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Informacje o usłudze')
                    ->tabs([
                        // Tab: Podstawowe informacje
                        Tabs\Tab::make('Podstawowe informacje')
                            ->schema([
                                Select::make('sitter_id')
                                    ->label('Opiekun')
                                    ->relationship('sitter', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('category_id')
                                    ->label('Kategoria')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('title')
                                    ->label('Tytuł usługi')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                TextInput::make('slug')
                                    ->label('Slug (URL)')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Generowany automatycznie z tytułu')
                                    ->columnSpan(2),

                                RichEditor::make('description')
                                    ->label('Opis usługi')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'bulletList',
                                        'orderedList',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        // Tab: Cennik
                        Tabs\Tab::make('Cennik')
                            ->schema([
                                TextInput::make('price_per_hour')
                                    ->label('Cena za godzinę')
                                    ->numeric()
                                    ->prefix('PLN')
                                    ->step(0.01)
                                    ->columnSpan(1),

                                TextInput::make('price_per_day')
                                    ->label('Cena za dzień')
                                    ->numeric()
                                    ->prefix('PLN')
                                    ->step(0.01)
                                    ->columnSpan(1),

                                TextInput::make('price_per_visit')
                                    ->label('Cena za wizytę')
                                    ->numeric()
                                    ->prefix('PLN')
                                    ->step(0.01)
                                    ->columnSpan(1),

                                TextInput::make('price_per_week')
                                    ->label('Cena za tydzień')
                                    ->numeric()
                                    ->prefix('PLN')
                                    ->step(0.01)
                                    ->columnSpan(1),

                                TextInput::make('price_per_month')
                                    ->label('Cena za miesiąc')
                                    ->numeric()
                                    ->prefix('PLN')
                                    ->step(0.01)
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        // Tab: Szczegóły usługi
                        Tabs\Tab::make('Szczegóły usługi')
                            ->schema([
                                CheckboxList::make('pet_types')
                                    ->label('Akceptowane typy zwierząt')
                                    ->options([
                                        'dog' => 'Pies',
                                        'cat' => 'Kot',
                                        'bird' => 'Ptak',
                                        'fish' => 'Ryba',
                                        'rodent' => 'Gryzoń',
                                        'reptile' => 'Gad',
                                        'other' => 'Inne',
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),

                                CheckboxList::make('pet_sizes')
                                    ->label('Akceptowane rozmiary zwierząt')
                                    ->options([
                                        'small' => 'Mały',
                                        'medium' => 'Średni',
                                        'large' => 'Duży',
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),

                                Toggle::make('home_service')
                                    ->label('Usługa u klienta')
                                    ->default(false)
                                    ->columnSpan(1),

                                Toggle::make('sitter_home')
                                    ->label('Usługa u opiekuna')
                                    ->default(false)
                                    ->columnSpan(1),

                                TextInput::make('max_pets')
                                    ->label('Maksymalna liczba zwierząt jednocześnie')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->default(1)
                                    ->columnSpan(1),

                                TextInput::make('service_radius')
                                    ->label('Promień świadczenia usług')
                                    ->numeric()
                                    ->suffix('km')
                                    ->minValue(1)
                                    ->maxValue(50)
                                    ->columnSpan(1),

                                Toggle::make('requires_consultation')
                                    ->label('Wymaga konsultacji wstępnej')
                                    ->default(false)
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        // Tab: Status
                        Tabs\Tab::make('Status')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Usługa aktywna')
                                    ->default(true)
                                    ->columnSpan(1),

                                Placeholder::make('stats_divider')
                                    ->label('Statystyki usługi')
                                    ->columnSpanFull(),

                                Placeholder::make('bookings_count')
                                    ->label('Liczba rezerwacji')
                                    ->content(fn ($record): string => $record?->bookings()->count() ?? '0')
                                    ->columnSpan(1),

                                Placeholder::make('average_rating')
                                    ->label('Średnia ocena')
                                    ->content(fn ($record): string => $record?->average_rating
                                        ? number_format($record->average_rating, 1).' ⭐'
                                        : 'Brak ocen')
                                    ->columnSpan(1),

                                Placeholder::make('reviews_count')
                                    ->label('Liczba opinii')
                                    ->content(fn ($record): string => $record?->reviews_count ?? '0')
                                    ->columnSpan(1),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
