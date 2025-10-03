<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Relation Manager dla zarządzania rezerwacjami użytkownika jako właściciela.
 *
 * Wyświetla wszystkie rezerwacje złożone przez użytkownika dla swoich zwierząt.
 * Tabela jest read-only, zarządzanie rezerwacjami odbywa się przez BookingResource.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class OwnerBookingsRelationManager extends RelationManager
{
    /**
     * Nazwa relacji z modelu User.
     */
    protected static string $relationship = 'ownerBookings';

    /**
     * Etykieta wyświetlana w interfejsie.
     */
    protected static ?string $title = 'Rezerwacje (jako właściciel)';

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
     * Konfiguruje tabelę wyświetlającą rezerwacje użytkownika.
     *
     * Tabela pokazuje szczegóły rezerwacji z możliwością filtrowania po statusie
     * i zakresie dat. Obsługuje wyświetlanie statusu z odpowiednimi kolorami.
     *
     * @param  Table  $table  Obiekt tabeli
     * @return Table Skonfigurowana tabela
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('service.category.name')
                    ->label('Usługa')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('pet.name')
                    ->label('Zwierzę')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('sitter.name')
                    ->label('Opiekun')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('start_date')
                    ->label('Data rozpoczęcia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Data zakończenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Oczekująca',
                        'confirmed' => 'Potwierdzona',
                        'in_progress' => 'W trakcie',
                        'completed' => 'Zakończona',
                        'cancelled' => 'Anulowana',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('total_price')
                    ->label('Cena')
                    ->money('PLN')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Oczekująca',
                        'confirmed' => 'Potwierdzona',
                        'in_progress' => 'W trakcie',
                        'completed' => 'Zakończona',
                        'cancelled' => 'Anulowana',
                    ]),

                Filter::make('start_date')
                    ->label('Data rozpoczęcia')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Od'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                // Read-only - brak możliwości tworzenia
            ])
            ->recordActions([
                ViewAction::make(),
                // Read-only - brak możliwości edycji lub usuwania
            ])
            ->toolbarActions([
                // Brak bulk actions
            ]);
    }
}
