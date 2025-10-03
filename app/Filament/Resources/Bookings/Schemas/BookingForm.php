<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formularz rezerwacji w panelu administracyjnym.
 *
 * Umożliwia tworzenie i edycję rezerwacji z pełną walidacją
 * i zależnościami między polami formularza.
 */
class BookingForm
{
    /**
     * Konfiguruje schemat formularza rezerwacji.
     *
     * Formularz podzielony na sekcje:
     * - Szczegóły rezerwacji (dane podstawowe)
     * - Ceny & Płatność (finansowe i notatki)
     *
     * @param  Schema  $schema  Schemat formularza do skonfigurowania
     * @return Schema Skonfigurowany schemat
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Szczegóły rezerwacji')
                    ->description('Podstawowe informacje o rezerwacji')
                    ->schema([
                        Select::make('owner_id')
                            ->label('Właściciel zwierzęcia')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Użytkownik składający rezerwację'),

                        Select::make('sitter_id')
                            ->label('Opiekun')
                            ->relationship('sitter', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Opiekun świadczący usługę')
                            ->reactive(),

                        Select::make('service_id')
                            ->label('Usługa')
                            ->relationship('service', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Typ usługi opiekuńczej'),

                        Select::make('pet_id')
                            ->label('Zwierzę')
                            ->relationship('pet', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Zwierzę objęte opieką'),

                        DateTimePicker::make('start_date')
                            ->label('Data rozpoczęcia')
                            ->required()
                            ->displayFormat('d.m.Y H:i')
                            ->seconds(false)
                            ->minDate(now())
                            ->helperText('Kiedy rozpoczyna się opieka'),

                        DateTimePicker::make('end_date')
                            ->label('Data zakończenia')
                            ->required()
                            ->displayFormat('d.m.Y H:i')
                            ->seconds(false)
                            ->after('start_date')
                            ->helperText('Kiedy kończy się opieka'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Oczekująca',
                                'confirmed' => 'Potwierdzona',
                                'in_progress' => 'W trakcie',
                                'completed' => 'Zakończona',
                                'cancelled' => 'Anulowana',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Section::make('Ceny & Płatność')
                    ->description('Szczegóły finansowe rezerwacji')
                    ->schema([
                        TextInput::make('total_price')
                            ->label('Całkowita cena')
                            ->required()
                            ->numeric()
                            ->prefix('PLN')
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Łączna kwota do zapłaty'),

                        Select::make('payment_status')
                            ->label('Status płatności')
                            ->options([
                                'pending' => 'Oczekująca',
                                'processing' => 'Przetwarzana',
                                'completed' => 'Zakończona',
                                'failed' => 'Nieudana',
                                'refunded' => 'Zwrócona',
                            ])
                            ->default('pending')
                            ->native(false)
                            ->helperText('Aktualny status płatności'),

                        Textarea::make('special_instructions')
                            ->label('Specjalne wymagania')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Dodatkowe informacje od właściciela'),

                        Textarea::make('admin_notes')
                            ->label('Notatki administratora')
                            ->rows(3)
                            ->columnSpanFull()
                            ->visible(fn () => auth()->user()?->profile?->role === 'admin')
                            ->helperText('Wewnętrzne notatki - widoczne tylko dla administratorów'),
                    ])
                    ->columns(2),
            ]);
    }
}
