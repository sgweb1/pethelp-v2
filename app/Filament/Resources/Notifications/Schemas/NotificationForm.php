<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Formularz powiadomień dla panelu administracyjnego Filament.
 *
 * Wyświetla szczegóły powiadomienia systemowego (tylko odczyt).
 * Powiadomienia są tworzone automatycznie lub masowo przez specjalną akcję.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class NotificationForm
{
    /**
     * Konfiguruje formularz powiadomienia.
     *
     * Formularz zawiera informacje o powiadomieniu (tylko odczyt).
     *
     * @param  Schema  $schema  Schemat formularza do konfiguracji
     * @return Schema Skonfigurowany schemat formularza
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Sekcja: Szczegóły powiadomienia (tylko odczyt)
                Section::make('Szczegóły powiadomienia')
                    ->description('Informacje o powiadomieniu systemowym')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        Placeholder::make('id')
                            ->label('ID Powiadomienia')
                            ->content(fn ($record) => $record?->id),

                        Placeholder::make('type')
                            ->label('Typ powiadomienia')
                            ->content(fn ($record) => class_basename($record?->type ?? 'Nieznany')),

                        Placeholder::make('notifiable_type')
                            ->label('Typ odbiorcy')
                            ->content(fn ($record) => class_basename($record?->notifiable_type ?? 'Nieznany')),

                        Placeholder::make('notifiable_id')
                            ->label('ID Odbiorcy')
                            ->content(fn ($record) => "#{$record?->notifiable_id}"),

                        Textarea::make('data')
                            ->label('Dane powiadomienia (JSON)')
                            ->disabled()
                            ->rows(8)
                            ->formatStateUsing(fn ($state) => json_encode(
                                is_string($state) ? json_decode($state) : $state,
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                            ))
                            ->columnSpanFull(),

                        Placeholder::make('read_at')
                            ->label('Status przeczytania')
                            ->content(fn ($record) => $record?->read_at
                                ? '✓ Przeczytane ('.$record->read_at->format('d.m.Y H:i').')'
                                : '✗ Nieprzeczytane'),

                        Placeholder::make('created_at')
                            ->label('Data utworzenia')
                            ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
