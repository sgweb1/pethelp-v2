<?php

namespace App\Filament\Resources\Disputes\Tables;

use App\Models\Dispute;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Konfiguracja tabeli dla DisputeResource w panelu administracyjnym Filament.
 *
 * Definiuje kolumny, filtry i akcje zarządzające dla listy zgłoszeń.
 * Umożliwia przeglądanie zgłoszeń, przypisywanie do adminów oraz rozwiązywanie sporów.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class DisputesTable
{
    /**
     * Konfiguruje tabelę zgłoszeń dla Filament Resource.
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

                // Status
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'new',
                        'info' => 'in_progress',
                        'success' => 'resolved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'new' => 'Nowe',
                        'in_progress' => 'W trakcie',
                        'resolved' => 'Rozwiązane',
                        'rejected' => 'Odrzucone',
                        default => $state,
                    })
                    ->sortable(),

                // Kategoria
                TextColumn::make('category')
                    ->label('Kategoria')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cancellation' => 'Anulowanie',
                        'payment' => 'Płatność',
                        'behavior' => 'Zachowanie',
                        'quality' => 'Jakość usługi',
                        'other' => 'Inne',
                        default => $state,
                    })
                    ->badge()
                    ->sortable(),

                // Tytuł
                TextColumn::make('title')
                    ->label('Tytuł')
                    ->limit(40)
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                // Zgłaszający
                TextColumn::make('reporter.name')
                    ->label('Zgłaszający')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->reporter?->email),

                // Rezerwacja
                TextColumn::make('booking_id')
                    ->label('Rezerwacja')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '-')
                    ->sortable()
                    ->toggleable(),

                // Przypisane do
                TextColumn::make('assignedAdmin.name')
                    ->label('Przypisane do')
                    ->default('Nie przypisano')
                    ->sortable(),

                // Data zgłoszenia
                TextColumn::make('created_at')
                    ->label('Data zgłoszenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                // Data rozwiązania
                TextColumn::make('resolved_at')
                    ->label('Data rozwiązania')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtr po statusie
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new' => 'Nowe',
                        'in_progress' => 'W trakcie',
                        'resolved' => 'Rozwiązane',
                        'rejected' => 'Odrzucone',
                    ])
                    ->placeholder('Wszystkie statusy'),

                // Filtr po kategorii
                SelectFilter::make('category')
                    ->label('Kategoria')
                    ->options([
                        'cancellation' => 'Anulowanie',
                        'payment' => 'Płatność',
                        'behavior' => 'Zachowanie',
                        'quality' => 'Jakość usługi',
                        'other' => 'Inne',
                    ])
                    ->placeholder('Wszystkie kategorie'),

                // Filtr po przypisaniu
                SelectFilter::make('assigned_to')
                    ->label('Przypisane do')
                    ->options(
                        User::query()
                            ->whereHas('profile', function ($query) {
                                $query->where('role', 'admin');
                            })
                            ->pluck('name', 'id')
                            ->prepend('Nie przypisano', 0)
                            ->toArray()
                    )
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            if ($data['value'] === '0') {
                                $query->whereNull('assigned_to');
                            } else {
                                $query->where('assigned_to', $data['value']);
                            }
                        }
                    })
                    ->placeholder('Wszystkie'),

                // Filtr po dacie utworzenia
                Filter::make('created_at')
                    ->label('Data zgłoszenia')
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // Custom Action: Przypisz do mnie
                Action::make('assign_to_me')
                    ->label('Przypisz do mnie')
                    ->icon(Heroicon::UserPlus)
                    ->color('info')
                    ->action(function (Dispute $record): void {
                        $record->assignTo(Auth::id());
                    })
                    ->visible(fn (Dispute $record): bool => $record->assigned_to !== Auth::id())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Zgłoszenie przypisane')
                            ->body('Zgłoszenie zostało przypisane do Ciebie.')
                    ),

                // Custom Action: Rozwiąż
                Action::make('resolve')
                    ->label('Rozwiąż')
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->form([
                        Textarea::make('resolution')
                            ->label('Opis rozwiązania')
                            ->required()
                            ->rows(5)
                            ->placeholder('Opisz jak rozwiązano problem'),
                    ])
                    ->action(function (Dispute $record, array $data): void {
                        $record->resolve(Auth::id(), $data['resolution']);
                    })
                    ->visible(fn (Dispute $record): bool => ! $record->isResolved() && ! $record->isRejected())
                    ->requiresConfirmation()
                    ->modalHeading('Rozwiąż zgłoszenie')
                    ->modalDescription('Podaj opis rozwiązania problemu.')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Zgłoszenie rozwiązane')
                            ->body('Zgłoszenie zostało pomyślnie rozwiązane.')
                    ),

                // Custom Action: Odrzuć
                Action::make('reject')
                    ->label('Odrzuć')
                    ->icon(Heroicon::XCircle)
                    ->color('danger')
                    ->form([
                        Textarea::make('reason')
                            ->label('Powód odrzucenia')
                            ->required()
                            ->rows(4)
                            ->placeholder('Wyjaśnij dlaczego zgłoszenie zostało odrzucone'),
                    ])
                    ->action(function (Dispute $record, array $data): void {
                        $record->reject(Auth::id(), $data['reason']);
                    })
                    ->visible(fn (Dispute $record): bool => ! $record->isResolved() && ! $record->isRejected())
                    ->requiresConfirmation()
                    ->modalHeading('Odrzuć zgłoszenie')
                    ->modalDescription('Czy na pewno chcesz odrzucić to zgłoszenie?')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Zgłoszenie odrzucone')
                            ->body('Zgłoszenie zostało odrzucone.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Action: Przypisz do admina
                    BulkAction::make('assign_bulk')
                        ->label('Przypisz do admina')
                        ->icon(Heroicon::UserPlus)
                        ->color('info')
                        ->form([
                            Select::make('admin_id')
                                ->label('Administrator')
                                ->options(
                                    User::query()
                                        ->whereHas('profile', function ($query) {
                                            $query->where('role', 'admin');
                                        })
                                        ->pluck('name', 'id')
                                        ->toArray()
                                )
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn (Dispute $dispute) => $dispute->assignTo($data['admin_id']));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Zgłoszenia przypisane')
                                ->body("{$count} zgłoszeń zostało przypisanych do administratora.");
                        }),

                    // Bulk Action: Zmień status
                    BulkAction::make('change_status_bulk')
                        ->label('Zmień status')
                        ->icon(Heroicon::Cog)
                        ->color('warning')
                        ->form([
                            Select::make('status')
                                ->label('Nowy status')
                                ->options([
                                    'new' => 'Nowe',
                                    'in_progress' => 'W trakcie',
                                    'resolved' => 'Rozwiązane',
                                    'rejected' => 'Odrzucone',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn (Dispute $dispute) => $dispute->update(['status' => $data['status']]));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Status zmieniony')
                                ->body("{$count} zgłoszeń zmieniło status.");
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                // Eager loading dla optymalizacji
                return $query->with([
                    'reporter:id,name,email',
                    'againstUser:id,name,email',
                    'booking:id',
                    'assignedAdmin:id,name',
                    'resolver:id,name',
                ]);
            });
    }
}
