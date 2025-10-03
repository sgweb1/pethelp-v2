<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Formularz recenzji dla panelu administracyjnego Filament.
 *
 * Umożliwia przeglądanie recenzji i moderację przez administratorów.
 * Większość pól jest tylko do odczytu - administrator może dodać odpowiedź
 * i zmienić status moderacji.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ReviewForm
{
    /**
     * Konfiguruje formularz recenzji.
     *
     * Formularz zawiera dane recenzji (tylko odczyt) oraz sekcję moderacji
     * gdzie administrator może zaakceptować/odrzucić recenzję i dodać odpowiedź.
     *
     * @param  Schema  $schema  Schemat formularza do konfiguracji
     * @return Schema Skonfigurowany schemat formularza
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Sekcja: Informacje o recenzji (tylko odczyt)
                Section::make('Szczegóły recenzji')
                    ->description('Informacje o recenzji i autorach')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        Placeholder::make('reviewer.name')
                            ->label('Autor recenzji')
                            ->content(fn ($record) => $record?->reviewer?->name.' ('.$record?->reviewer?->email.')'),

                        Placeholder::make('reviewee.name')
                            ->label('Osoba oceniana')
                            ->content(fn ($record) => $record?->reviewee?->name.' ('.$record?->reviewee?->email.')'),

                        Placeholder::make('booking.id')
                            ->label('Rezerwacja')
                            ->content(fn ($record) => $record?->booking
                                ? "#{$record->booking->id} - ".($record->booking->service?->title ?? 'Brak usługi')
                                : 'Brak powiązanej rezerwacji'),

                        Placeholder::make('rating')
                            ->label('Ocena')
                            ->content(fn ($record) => $record ? $record->stars.' ('.$record->rating.'/5)' : ''),

                        Textarea::make('comment')
                            ->label('Komentarz')
                            ->disabled()
                            ->rows(4)
                            ->columnSpanFull(),

                        Placeholder::make('created_at')
                            ->label('Data utworzenia')
                            ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Sekcja: Moderacja (edytowalne)
                Section::make('Moderacja')
                    ->description('Akcje moderacyjne i odpowiedź administratora')
                    ->icon(Heroicon::ShieldCheck)
                    ->schema([
                        Select::make('moderation_status')
                            ->label('Status moderacji')
                            ->options([
                                'pending' => 'Oczekuje',
                                'approved' => 'Zaakceptowana',
                                'rejected' => 'Odrzucona',
                            ])
                            ->required()
                            ->default('pending')
                            ->helperText('Status moderacji recenzji'),

                        Toggle::make('is_visible')
                            ->label('Widoczna publicznie')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Czy recenzja jest widoczna dla użytkowników'),

                        RichEditor::make('admin_response')
                            ->label('Odpowiedź administratora')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->placeholder('Opcjonalna odpowiedź na recenzję')
                            ->helperText('Możesz dodać publiczną odpowiedź na recenzję')
                            ->columnSpanFull(),

                        Placeholder::make('moderator_info')
                            ->label('Informacje o moderacji')
                            ->content(function ($record) {
                                if (! $record?->moderated_at) {
                                    return 'Nie moderowano';
                                }

                                return sprintf(
                                    'Moderowano: %s przez %s',
                                    $record->moderated_at->format('d.m.Y H:i'),
                                    $record->moderator?->name ?? 'Nieznany'
                                );
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
