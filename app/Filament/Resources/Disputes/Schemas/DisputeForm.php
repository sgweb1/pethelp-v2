<?php

namespace App\Filament\Resources\Disputes\Schemas;

use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Formularz zgłoszeń dla panelu administracyjnego Filament.
 *
 * Wyświetla szczegóły zgłoszenia (tylko odczyt) oraz sekcje zarządzania
 * umożliwiające administratorowi przypisanie sprawy, dodanie notatek
 * i rozwiązanie zgłoszenia.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class DisputeForm
{
    /**
     * Konfiguruje formularz zgłoszenia.
     *
     * Formularz zawiera szczegóły zgłoszenia (tylko odczyt) oraz sekcje
     * zarządzania i rozwiązywania dostępne dla administratorów.
     *
     * @param  Schema  $schema  Schemat formularza do konfiguracji
     * @return Schema Skonfigurowany schemat formularza
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Sekcja: Szczegóły zgłoszenia (tylko odczyt)
                Section::make('Szczegóły zgłoszenia')
                    ->description('Informacje o zgłoszonym problemie')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        Placeholder::make('reporter.name')
                            ->label('Zgłaszający')
                            ->content(fn ($record) => $record?->reporter?->name.' ('.$record?->reporter?->email.')'),

                        Placeholder::make('against_user.name')
                            ->label('Dotyczy użytkownika')
                            ->content(fn ($record) => $record?->againstUser
                                ? $record->againstUser->name.' ('.$record->againstUser->email.')'
                                : 'Nie wskazano'),

                        Placeholder::make('booking_id')
                            ->label('Rezerwacja')
                            ->content(fn ($record) => $record?->booking
                                ? "#{$record->booking->id} - ".($record->booking->service?->name ?? 'Brak usługi')
                                : 'Nie dotyczy rezerwacji'),

                        Placeholder::make('category_label')
                            ->label('Kategoria')
                            ->content(fn ($record) => $record?->category_label ?? 'Nieznana'),

                        TextInput::make('title')
                            ->label('Tytuł')
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Opis problemu')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),

                        Placeholder::make('created_at')
                            ->label('Data zgłoszenia')
                            ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i')),

                        Placeholder::make('status_label')
                            ->label('Status')
                            ->content(fn ($record) => $record?->status_label ?? 'Nieznany'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Sekcja: Zarządzanie przez admina (edytowalne)
                Section::make('Zarządzanie')
                    ->description('Przypisanie i notatki administratora')
                    ->icon(Heroicon::Cog)
                    ->schema([
                        Select::make('status')
                            ->label('Status zgłoszenia')
                            ->options([
                                'new' => 'Nowe',
                                'in_progress' => 'W trakcie',
                                'resolved' => 'Rozwiązane',
                                'rejected' => 'Odrzucone',
                            ])
                            ->required()
                            ->default('new')
                            ->helperText('Zmień status zgłoszenia'),

                        Select::make('assigned_to')
                            ->label('Przypisane do')
                            ->options(
                                User::query()
                                    ->whereHas('profile', function ($query) {
                                        $query->where('role', 'admin');
                                    })
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->placeholder('Wybierz administratora')
                            ->helperText('Przypisz zgłoszenie do konkretnego administratora'),

                        Textarea::make('admin_notes')
                            ->label('Notatki administratora (prywatne)')
                            ->rows(4)
                            ->placeholder('Prywatne notatki widoczne tylko dla administratorów')
                            ->helperText('Te notatki nie są widoczne dla użytkowników')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Sekcja: Rozwiązanie (edytowalne)
                Section::make('Rozwiązanie')
                    ->description('Opis rozwiązania problemu')
                    ->icon(Heroicon::CheckCircle)
                    ->schema([
                        Textarea::make('resolution')
                            ->label('Opis rozwiązania')
                            ->rows(5)
                            ->placeholder('Opisz jak zostało rozwiązane zgłoszenie lub powód odrzucenia')
                            ->helperText('Ta informacja będzie widoczna dla użytkownika, który zgłosił problem')
                            ->columnSpanFull(),

                        Placeholder::make('resolver_info')
                            ->label('Informacje o rozwiązaniu')
                            ->content(function ($record) {
                                if (! $record?->resolved_at) {
                                    return 'Nie rozwiązano';
                                }

                                return sprintf(
                                    'Rozwiązano: %s przez %s',
                                    $record->resolved_at->format('d.m.Y H:i'),
                                    $record->resolver?->name ?? 'Nieznany'
                                );
                            })
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
