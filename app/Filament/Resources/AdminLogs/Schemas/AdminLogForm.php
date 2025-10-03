<?php

namespace App\Filament\Resources\AdminLogs\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Formularz szczegółów logu aktywności administratora.
 *
 * Wyświetla kompletne informacje o akcji wykonanej przez administratora
 * w trybie tylko do odczytu. Zawiera szczegóły administratora, akcji,
 * modelu, zmian oraz informacje techniczne.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class AdminLogForm
{
    /**
     * Konfiguruje formularz logu aktywności.
     *
     * @param  Schema  $schema  Schemat formularza do konfiguracji
     * @return Schema Skonfigurowany schemat formularza
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Sekcja: Szczegóły akcji
                Section::make('Szczegóły akcji')
                    ->description('Podstawowe informacje o wykonanej akcji')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        Placeholder::make('admin.name')
                            ->label('Administrator')
                            ->content(fn ($record) => $record?->admin?->name.' ('.$record?->admin?->email.')'),

                        Placeholder::make('action_label')
                            ->label('Typ akcji')
                            ->content(fn ($record) => $record?->action_label ?? 'Nieznana'),

                        Placeholder::make('created_at')
                            ->label('Data i czas')
                            ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i:s')),

                        Placeholder::make('description')
                            ->label('Opis')
                            ->content(fn ($record) => $record?->description ?? 'Brak opisu')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Sekcja: Informacje o modelu
                Section::make('Informacje o modelu')
                    ->description('Model którego dotyczy akcja')
                    ->icon(Heroicon::CubeTransparent)
                    ->schema([
                        Placeholder::make('model_name')
                            ->label('Typ modelu')
                            ->content(fn ($record) => $record?->model_name ?? 'Nie dotyczy'),

                        Placeholder::make('model_id')
                            ->label('ID rekordu')
                            ->content(fn ($record) => $record?->model_id ? "#{$record->model_id}" : 'Nie dotyczy'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Sekcja: Zmiany danych
                Section::make('Szczegóły zmian')
                    ->description('Porównanie wartości przed i po zmianie')
                    ->icon(Heroicon::ArrowPathRoundedSquare)
                    ->schema([
                        Placeholder::make('old_values_display')
                            ->label('Poprzednie wartości')
                            ->content(function ($record) {
                                if (! $record?->old_values || empty($record->old_values)) {
                                    return 'Brak danych (nowy rekord)';
                                }

                                return view('filament.components.json-display', [
                                    'data' => $record->old_values,
                                ]);
                            })
                            ->columnSpanFull(),

                        Placeholder::make('new_values_display')
                            ->label('Nowe wartości')
                            ->content(function ($record) {
                                if (! $record?->new_values || empty($record->new_values)) {
                                    return 'Brak danych';
                                }

                                return view('filament.components.json-display', [
                                    'data' => $record->new_values,
                                ]);
                            })
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Sekcja: Informacje techniczne
                Section::make('Informacje techniczne')
                    ->description('Dane techniczne dotyczące akcji')
                    ->icon(Heroicon::ServerStack)
                    ->schema([
                        Placeholder::make('ip_address')
                            ->label('Adres IP')
                            ->content(fn ($record) => $record?->ip_address ?? 'Nieznany'),

                        Placeholder::make('user_agent')
                            ->label('User Agent')
                            ->content(fn ($record) => $record?->user_agent ?? 'Nieznany')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
