<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Relation Manager dla opinii otrzymanych przez użytkownika.
 *
 * Wyświetla wszystkie opinie, które użytkownik otrzymał od innych użytkowników
 * (właścicieli zwierząt) po świadczonych usługach. Tabela jest read-only,
 * zarządzanie opiniami odbywa się przez ReviewResource.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ReviewsReceivedRelationManager extends RelationManager
{
    /**
     * Nazwa relacji z modelu User.
     */
    protected static string $relationship = 'reviewsReceived';

    /**
     * Etykieta wyświetlana w interfejsie.
     */
    protected static ?string $title = 'Opinie otrzymane';

    /**
     * Konfiguruje formularz (nieużywany - read-only).
     *
     * @param  Schema  $schema  Schemat formularza
     * @return Schema Skonfigurowany schemat
     */
    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    /**
     * Konfiguruje tabelę wyświetlającą opinie otrzymane przez użytkownika.
     *
     * Tabela pokazuje od kogo otrzymano opinię, ocenę w formie gwiazdek z kolorowaniem
     * oraz treść komentarza z ograniczeniem długości wyświetlania.
     *
     * @param  Table  $table  Obiekt tabeli
     * @return Table Skonfigurowana tabela
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('reviewer.name')
                    ->label('Od kogo')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('rating')
                    ->label('Ocena')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->colors([
                        'danger' => fn ($state) => $state <= 2,
                        'warning' => fn ($state) => $state === 3,
                        'success' => fn ($state) => $state >= 4,
                    ]),

                TextColumn::make('comment')
                    ->label('Komentarz')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Data otrzymania')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Read-only - brak możliwości tworzenia
            ])
            ->recordActions([
                // Read-only - brak akcji
            ])
            ->toolbarActions([
                // Brak bulk actions
            ]);
    }
}
