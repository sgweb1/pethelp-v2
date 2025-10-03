<?php

namespace App\Filament\Resources\PetTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

/**
 * Formularz typów zwierząt dla panelu administracyjnego Filament.
 *
 * Umożliwia tworzenie i edycję typów zwierząt (np. psy, koty, ptaki).
 * Typy służą do kategoryzacji zwierząt i filtrowania usług według akceptowanych gatunków.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PetTypeForm
{
    /**
     * Konfiguruje formularz typów zwierząt.
     *
     * Formularz zawiera podstawowe informacje o typie zwierzęcia (nazwa, slug, opis),
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
                    ->description('Nazwa i opis typu zwierzęcia')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nazwa typu')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // Auto-generuj slug tylko dla nowych rekordów
                                if (! $get('id')) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->placeholder('np. Pies, Kot, Ptak')
                            ->helperText('Podaj nazwę typu zwierzęcia')
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                            ->dehydrated()
                            ->placeholder('np. pies, kot')
                            ->helperText('Automatycznie generowany z nazwy (tylko przy tworzeniu)')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Opis typu')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Opisz charakterystykę tego typu zwierzęcia')
                            ->helperText('Opcjonalny opis pomoże użytkownikom zrozumieć specyfikę typu')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                // Sekcja: Wyświetlanie
                Section::make('Opcje wyświetlania')
                    ->description('Ustawienia wyglądu i kolejności typu')
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
                                'heroicon-o-fire' => 'Ogień (Fire)',
                                'heroicon-o-bolt' => 'Piorun (Bolt)',
                                'heroicon-o-puzzle-piece' => 'Puzzle (Puzzle Piece)',
                                'heroicon-o-cube' => 'Sześcian (Cube)',
                            ])
                            ->searchable()
                            ->placeholder('Wybierz ikonę dla typu zwierzęcia')
                            ->helperText('Ikona będzie wyświetlana obok nazwy typu'),

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
                    ->description('Aktywność typu zwierzęcia')
                    ->icon(Heroicon::CheckCircle)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Typ aktywny')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Tylko aktywne typy zwierząt są widoczne dla użytkowników'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
