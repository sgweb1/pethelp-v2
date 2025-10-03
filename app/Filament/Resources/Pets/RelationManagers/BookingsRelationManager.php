<?php

namespace App\Filament\Resources\Pets\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Relation Manager dla rezerwacji zwierzęcia.
 *
 * Wyświetla listę wszystkich rezerwacji dla danego zwierzęcia w trybie read-only.
 * Zarządzanie rezerwacjami odbywa się przez BookingResource.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    /**
     * Ten relation manager jest read-only - formularz nie jest używany.
     *
     * @param  Schema  $schema  Schemat formularza
     * @return Schema Pusty schemat
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Read-only - zarządzanie przez BookingResource
            ]);
    }

    /**
     * Konfiguruje tabelę rezerwacji.
     *
     * Wyświetla podstawowe informacje o rezerwacjach w formacie read-only.
     *
     * @param  Table  $table  Obiekt tabeli do konfiguracji
     * @return Table Skonfigurowana tabela
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                // ID rezerwacji
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                // Usługa
                TextColumn::make('service.title')
                    ->label('Usługa')
                    ->searchable()
                    ->limit(30),

                // Opiekun
                TextColumn::make('sitter.name')
                    ->label('Opiekun')
                    ->searchable(),

                // Data rozpoczęcia
                TextColumn::make('start_date')
                    ->label('Od')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                // Data zakończenia
                TextColumn::make('end_date')
                    ->label('Do')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                // Status
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Oczekująca',
                        'confirmed' => 'Potwierdzona',
                        'completed' => 'Zakończona',
                        'cancelled' => 'Anulowana',
                        default => $state,
                    }),
            ])
            ->filters([
                // Brak filtrów - prosta lista
            ])
            ->headerActions([
                // Brak akcji - read-only view
            ])
            ->recordActions([
                // Brak akcji - zarządzanie przez BookingResource
            ])
            ->toolbarActions([
                // Brak akcji - read-only view
            ])
            ->defaultSort('start_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
