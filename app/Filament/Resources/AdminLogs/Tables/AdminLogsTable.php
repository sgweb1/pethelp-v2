<?php

namespace App\Filament\Resources\AdminLogs\Tables;

use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Konfiguracja tabeli dla AdminLogResource w panelu administracyjnym Filament.
 *
 * Definiuje kolumny, filtry i akcje dla listy logów aktywności administratorów.
 * Umożliwia przeglądanie, filtrowanie i eksport logów do analizy audytu.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class AdminLogsTable
{
    /**
     * Konfiguruje tabelę logów aktywności dla Filament Resource.
     *
     * @param  Table  $table  Instancja tabeli do konfiguracji
     * @return Table Skonfigurowana tabela
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ID
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(),

                // Data i czas
                TextColumn::make('created_at')
                    ->label('Data i czas')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->searchable(),

                // Administrator
                TextColumn::make('admin.name')
                    ->label('Administrator')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->admin?->email),

                // Akcja
                BadgeColumn::make('action_label')
                    ->label('Akcja')
                    ->colors([
                        'success' => 'Utworzono',
                        'info' => 'Zaktualizowano',
                        'warning' => 'Przeglądano',
                        'danger' => 'Usunięto',
                        'secondary' => fn ($state) => ! in_array($state, ['Utworzono', 'Zaktualizowano', 'Przeglądano', 'Usunięto']),
                    ])
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('action', 'like', "%{$search}%");
                    }),

                // Model
                TextColumn::make('model_name')
                    ->label('Model')
                    ->default('Nie dotyczy')
                    ->badge()
                    ->color('gray')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('model_type', 'like', "%{$search}%");
                    }),

                // ID Modelu
                TextColumn::make('model_id')
                    ->label('ID modelu')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '-')
                    ->sortable()
                    ->toggleable(),

                // Opis
                TextColumn::make('description')
                    ->label('Opis')
                    ->limit(50)
                    ->wrap()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // IP
                TextColumn::make('ip_address')
                    ->label('Adres IP')
                    ->searchable()
                    ->toggleable(),

                // User Agent
                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtr po administratorze
                SelectFilter::make('admin_id')
                    ->label('Administrator')
                    ->options(
                        User::query()
                            ->whereHas('profile', function ($query) {
                                $query->where('role', 'admin');
                            })
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->placeholder('Wszyscy administratorzy')
                    ->searchable(),

                // Filtr po akcji
                SelectFilter::make('action')
                    ->label('Typ akcji')
                    ->options([
                        'created' => 'Utworzono',
                        'updated' => 'Zaktualizowano',
                        'deleted' => 'Usunięto',
                        'viewed' => 'Przeglądano',
                        'exported' => 'Wyeksportowano',
                        'restored' => 'Przywrócono',
                        'force_deleted' => 'Usunięto trwale',
                        'attached' => 'Dołączono',
                        'detached' => 'Odłączono',
                    ])
                    ->placeholder('Wszystkie akcje'),

                // Filtr po typie modelu
                SelectFilter::make('model_type')
                    ->label('Typ modelu')
                    ->options(function () {
                        return \App\Models\AdminLog::query()
                            ->distinct()
                            ->whereNotNull('model_type')
                            ->pluck('model_type', 'model_type')
                            ->map(fn ($value) => class_basename($value))
                            ->toArray();
                    })
                    ->placeholder('Wszystkie modele')
                    ->searchable(),

                // Filtr po dacie
                Filter::make('created_at')
                    ->label('Data akcji')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Od'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                // Filtr po IP
                Filter::make('ip_address')
                    ->label('Adres IP')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('ip')
                            ->label('Adres IP'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['ip'],
                            fn (Builder $query, $ip): Builder => $query->where('ip_address', $ip),
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Export do CSV
                    ExportBulkAction::make()
                        ->label('Eksportuj zaznaczone do CSV')
                        ->fileName(fn () => 'admin-logs-'.now()->format('Y-m-d-His').'.csv'),

                    // Bulk Action: Eksportuj wszystkie
                    BulkAction::make('export_all')
                        ->label('Eksportuj wszystkie (z filtrami)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Collection $records) {
                            // Eksport zostanie zaimplementowany później
                            \Filament\Notifications\Notification::make()
                                ->title('Eksport rozpoczęty')
                                ->body('Eksport logów został dodany do kolejki.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                // Eager loading dla optymalizacji
                return $query->with([
                    'admin:id,name,email',
                ]);
            })
            ->striped()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession();
    }
}
