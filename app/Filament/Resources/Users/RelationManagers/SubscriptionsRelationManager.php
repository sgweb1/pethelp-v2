<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\Subscription;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Relation Manager dla zarządzania subskrypcjami użytkownika.
 *
 * Wyświetla historię i aktywne subskrypcje użytkownika z informacjami
 * o planach, statusach i datach ważności. Tabela jest read-only,
 * zarządzanie subskrypcjami odbywa się przez SubscriptionResource.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class SubscriptionsRelationManager extends RelationManager
{
    /**
     * Nazwa relacji z modelu User.
     */
    protected static string $relationship = 'subscriptions';

    /**
     * Etykieta wyświetlana w interfejsie.
     */
    protected static ?string $title = 'Subskrypcje';

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
     * Konfiguruje tabelę wyświetlającą subskrypcje użytkownika.
     *
     * Tabela pokazuje plan subskrypcji, status z kolorowaniem, daty
     * ważności oraz cenę. Wyświetla również datę utworzenia subskrypcji.
     *
     * @param  Table  $table  Obiekt tabeli
     * @return Table Skonfigurowana tabela
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('subscriptionPlan.name')
                    ->label('Plan')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Subscription::STATUS_ACTIVE => 'Aktywna',
                        Subscription::STATUS_CANCELLED => 'Anulowana',
                        Subscription::STATUS_EXPIRED => 'Wygasła',
                        Subscription::STATUS_PAUSED => 'Wstrzymana',
                        Subscription::STATUS_PENDING => 'Oczekująca',
                        default => $state,
                    })
                    ->colors([
                        'success' => Subscription::STATUS_ACTIVE,
                        'danger' => Subscription::STATUS_CANCELLED,
                        'gray' => Subscription::STATUS_EXPIRED,
                        'warning' => Subscription::STATUS_PAUSED,
                        'info' => Subscription::STATUS_PENDING,
                    ]),

                TextColumn::make('starts_at')
                    ->label('Data rozpoczęcia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Data zakończenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Cena')
                    ->money('PLN')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Data utworzenia')
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
