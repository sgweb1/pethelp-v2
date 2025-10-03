<?php

namespace App\Filament\Resources\Accounting\Schemas;

use Filament\Schemas\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formularz tworzenia i edycji faktur.
 *
 * Obsługuje:
 * - Dane podstawowe faktury
 * - Dane nabywcy i sprzedawcy
 * - Pozycje faktury (line items)
 * - Kwoty i podatki
 * - Powiązania z płatnościami i zleceniami
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class InvoiceForm
{
    /**
     * Konfiguruje schema formularza faktury.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->components([
                // Kolumna 1 - Dane podstawowe
                Section::make('Dane podstawowe')
                    ->columnSpan(2)
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('invoice_number')
                                ->label('Numer faktury')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->default(fn () => \App\Models\Invoice::generateNextInvoiceNumber())
                                ->maxLength(255),

                            Select::make('invoice_type')
                                ->label('Typ faktury')
                                ->options([
                                    'vat' => 'Faktura VAT',
                                    'proforma' => 'Faktura Proforma',
                                    'correction' => 'Faktura Korygująca',
                                    'receipt' => 'Paragon',
                                ])
                                ->default('vat')
                                ->required(),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'draft' => 'Szkic',
                                    'issued' => 'Wystawiona',
                                    'sent' => 'Wysłana',
                                    'paid' => 'Opłacona',
                                    'cancelled' => 'Anulowana',
                                    'overdue' => 'Przeterminowana',
                                ])
                                ->default('draft')
                                ->required(),

                            TextInput::make('currency')
                                ->label('Waluta')
                                ->default('PLN')
                                ->required()
                                ->maxLength(3),
                        ]),

                        Grid::make(3)->components([
                            DatePicker::make('issue_date')
                                ->label('Data wystawienia')
                                ->required()
                                ->default(now()),

                            DatePicker::make('sale_date')
                                ->label('Data sprzedaży')
                                ->required()
                                ->default(now()),

                            DatePicker::make('payment_due_date')
                                ->label('Termin płatności')
                                ->required()
                                ->default(now()->addDays(14)),
                        ]),

                        Grid::make(2)->components([
                            DatePicker::make('paid_date')
                                ->label('Data zapłaty')
                                ->nullable(),

                            Select::make('payment_method')
                                ->label('Metoda płatności')
                                ->options([
                                    'transfer' => 'Przelew bankowy',
                                    'card' => 'Karta płatnicza',
                                    'blik' => 'BLIK',
                                    'cash' => 'Gotówka',
                                ])
                                ->nullable(),
                        ]),
                    ]),

                // Kolumna 2 - Kwoty
                Section::make('Kwoty')
                    ->columnSpan(1)
                    ->components([
                        TextInput::make('net_amount')
                            ->label('Kwota netto (PLN)')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0),

                        TextInput::make('tax_amount')
                            ->label('Kwota VAT (PLN)')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0),

                        TextInput::make('gross_amount')
                            ->label('Kwota brutto (PLN)')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0),

                        TextInput::make('paid_amount')
                            ->label('Zapłacono (PLN)')
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0),
                    ]),
            ]),

            // Powiązania
            Section::make('Powiązania')
                ->columns(3)
                ->components([
                    Select::make('user_id')
                        ->label('Nabywca (użytkownik)')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required(),

                    Select::make('payment_id')
                        ->label('Płatność')
                        ->relationship('payment', 'id')
                        ->searchable()
                        ->nullable(),

                    Select::make('booking_id')
                        ->label('Zlecenie')
                        ->relationship('booking', 'id')
                        ->searchable()
                        ->nullable(),
                ]),

            // Dane nabywcy
            Section::make('Dane nabywcy')
                ->columns(2)
                ->components([
                    TextInput::make('buyer_name')
                        ->label('Nazwa/Imię i nazwisko')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('buyer_tax_id')
                        ->label('NIP')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('buyer_address')
                        ->label('Adres')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('buyer_postal_code')
                        ->label('Kod pocztowy')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('buyer_city')
                        ->label('Miasto')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('buyer_country')
                        ->label('Kraj')
                        ->default('Polska')
                        ->maxLength(255),

                    TextInput::make('buyer_email')
                        ->label('Email')
                        ->email()
                        ->nullable()
                        ->columnSpan(2),
                ]),

            // Dane sprzedawcy (opcjonalne - dla sitterów)
            Section::make('Dane sprzedawcy')
                ->collapsed()
                ->columns(2)
                ->components([
                    Select::make('issuer_id')
                        ->label('Wystawca (sitter)')
                        ->relationship('issuer', 'name')
                        ->searchable()
                        ->nullable()
                        ->columnSpan(2),

                    TextInput::make('seller_name')
                        ->label('Nazwa/Imię i nazwisko')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('seller_tax_id')
                        ->label('NIP')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('seller_address')
                        ->label('Adres')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('seller_postal_code')
                        ->label('Kod pocztowy')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('seller_city')
                        ->label('Miasto')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('seller_country')
                        ->label('Kraj')
                        ->nullable()
                        ->maxLength(255),
                ]),

            // Pozycje faktury
            Section::make('Pozycje faktury')
                ->components([
                    Repeater::make('line_items')
                        ->label('')
                        ->schema([
                            Grid::make(6)->components([
                                TextInput::make('name')
                                    ->label('Nazwa usługi/produktu')
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('quantity')
                                    ->label('Ilość')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('unit_price')
                                    ->label('Cena jedn. netto')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->columnSpan(1),

                                TextInput::make('tax_rate')
                                    ->label('VAT %')
                                    ->numeric()
                                    ->default(23)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('total')
                                    ->label('Razem brutto')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(1),
                            ]),
                        ])
                        ->defaultItems(1)
                        ->addActionLabel('Dodaj pozycję')
                        ->reorderable()
                        ->collapsible(),
                ]),

            // Notatki
            Section::make('Notatki')
                ->columns(2)
                ->components([
                    Textarea::make('notes')
                        ->label('Notatki (widoczne na fakturze)')
                        ->rows(3)
                        ->nullable(),

                    Textarea::make('admin_notes')
                        ->label('Notatki administratora (wewnętrzne)')
                        ->rows(3)
                        ->nullable(),
                ]),

            // Integracja inFakt (tylko do odczytu przy edycji)
            Section::make('Integracja inFakt')
                ->collapsed()
                ->columns(2)
                ->visibleOn(['edit', 'view'])
                ->components([
                    TextInput::make('infakt_id')
                        ->label('ID faktury w inFakt')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('infakt_number')
                        ->label('Numer faktury w inFakt')
                        ->disabled()
                        ->dehydrated(false),
                ]),
        ]);
    }
}
