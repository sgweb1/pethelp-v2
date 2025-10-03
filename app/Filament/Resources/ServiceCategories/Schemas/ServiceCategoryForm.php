<?php

namespace App\Filament\Resources\ServiceCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

/**
 * Formularz kategorii usług dla panelu administracyjnego Filament.
 *
 * Umożliwia tworzenie i edycję kategorii usług opieki nad zwierzętami.
 * Kategorie służą do organizacji i grupowania usług oferowanych przez opiekunów.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ServiceCategoryForm
{
    /**
     * Konfiguruje formularz kategorii usług.
     *
     * Formularz zawiera podstawowe informacje o kategorii (nazwa, slug, opis),
     * opcje wyświetlania (ikona, kolejność sortowania) oraz status aktywności.
     *
     * @param  Schema  $schema  Schemat formularza do konfiguracji
     * @return Schema Skonfigurowany schemat formularza
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Sekcja: Podstawowe informacje
                Section::make('Podstawowe informacje')
                    ->description('Nazwa i opis kategorii usług')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nazwa kategorii')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // Auto-generuj slug tylko dla nowych rekordów
                                if (! $get('id')) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->placeholder('np. Spacery z psami, Opieka całodobowa')
                            ->helperText('Podaj nazwę kategorii usług')
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                            ->dehydrated()
                            ->placeholder('np. spacery-z-psami')
                            ->helperText('Automatycznie generowany z nazwy (tylko przy tworzeniu)')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Opis kategorii')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Opisz czym charakteryzują się usługi w tej kategorii')
                            ->helperText('Opcjonalny opis pomoże użytkownikom zrozumieć cel kategorii')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                // Sekcja: Wyświetlanie
                Section::make('Opcje wyświetlania')
                    ->description('Ustawienia wyglądu i kolejności kategorii')
                    ->icon(Heroicon::Eye)
                    ->schema([
                        Select::make('icon')
                            ->label('Ikona')
                            ->options([
                                'heroicon-o-home' => 'Dom (Home)',
                                'heroicon-o-heart' => 'Serce (Heart)',
                                'heroicon-o-star' => 'Gwiazda (Star)',
                                'heroicon-o-cake' => 'Tort (Cake)',
                                'heroicon-o-sparkles' => 'Błysk (Sparkles)',
                                'heroicon-o-beaker' => 'Kolba (Beaker)',
                                'heroicon-o-calendar' => 'Kalendarz (Calendar)',
                                'heroicon-o-clock' => 'Zegar (Clock)',
                                'heroicon-o-map-pin' => 'Pinezka (Map Pin)',
                                'heroicon-o-truck' => 'Samochód (Truck)',
                                'heroicon-o-academic-cap' => 'Czapka (Academic Cap)',
                                'heroicon-o-scissors' => 'Nożyczki (Scissors)',
                                'heroicon-o-sun' => 'Słońce (Sun)',
                                'heroicon-o-moon' => 'Księżyc (Moon)',
                            ])
                            ->searchable()
                            ->placeholder('Wybierz ikonę dla kategorii')
                            ->helperText('Ikona będzie wyświetlana obok nazwy kategorii'),

                        TextInput::make('sort_order')
                            ->label('Kolejność sortowania')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(999)
                            ->required()
                            ->helperText('Niższe wartości = wyższe pozycje na liście')
                            ->placeholder('0'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Sekcja: Status
                Section::make('Status')
                    ->description('Aktywność kategorii')
                    ->icon(Heroicon::CheckCircle)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Kategoria aktywna')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Tylko aktywne kategorie są widoczne dla użytkowników'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
