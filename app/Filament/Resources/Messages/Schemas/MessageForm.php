<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Formularz wiadomości dla panelu administracyjnego Filament.
 *
 * Wyświetla szczegóły wiadomości (tylko odczyt) oraz sekcję moderacji
 * umożliwiającą administratorowi ukrycie niewłaściwej treści.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class MessageForm
{
    /**
     * Konfiguruje formularz wiadomości.
     *
     * Formularz zawiera informacje o konwersacji, nadawcy i treści wiadomości
     * (tylko odczyt) oraz sekcję moderacji gdzie administrator może ukryć
     * wiadomość z podaniem powodu.
     *
     * @param  Schema  $schema  Schemat formularza do konfiguracji
     * @return Schema Skonfigurowany schemat formularza
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Sekcja: Szczegóły wiadomości (tylko odczyt)
                Section::make('Szczegóły wiadomości')
                    ->description('Informacje o wiadomości i nadawcy')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        Placeholder::make('conversation_id')
                            ->label('ID Konwersacji')
                            ->content(fn ($record) => "#{$record?->conversation_id}"),

                        Placeholder::make('sender.name')
                            ->label('Nadawca')
                            ->content(fn ($record) => $record?->sender?->name.' ('.$record?->sender?->email.')'),

                        Placeholder::make('conversation.booking_id')
                            ->label('Rezerwacja')
                            ->content(fn ($record) => $record?->conversation?->booking
                                ? "#{$record->conversation->booking->id}"
                                : 'Brak powiązanej rezerwacji'),

                        Textarea::make('message')
                            ->label('Treść wiadomości')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),

                        Placeholder::make('is_read')
                            ->label('Status przeczytania')
                            ->content(fn ($record) => $record?->is_read
                                ? '✓ Przeczytane ('.$record->read_at?->format('d.m.Y H:i').')'
                                : '✗ Nieprzeczytane'),

                        Placeholder::make('created_at')
                            ->label('Data wysłania')
                            ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Sekcja: Moderacja (edytowalne)
                Section::make('Moderacja')
                    ->description('Ukrywanie niewłaściwych wiadomości')
                    ->icon(Heroicon::ShieldCheck)
                    ->schema([
                        Toggle::make('is_hidden')
                            ->label('Ukryta przez moderatora')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Czy wiadomość jest ukryta przed użytkownikami'),

                        Textarea::make('hidden_reason')
                            ->label('Powód ukrycia')
                            ->rows(3)
                            ->placeholder('Opcjonalny powód ukrycia wiadomości (np. spam, obraźliwa treść)')
                            ->helperText('Powód będzie widoczny tylko dla administratorów')
                            ->visible(fn ($get) => $get('is_hidden'))
                            ->columnSpanFull(),

                        Placeholder::make('moderator_info')
                            ->label('Informacje o moderacji')
                            ->content(function ($record) {
                                if (! $record?->hidden_at) {
                                    return 'Nie moderowano';
                                }

                                return sprintf(
                                    'Ukryto: %s przez %s',
                                    $record->hidden_at->format('d.m.Y H:i'),
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
