<?php

namespace App\Filament\Resources\Messages\Tables;

use App\Models\Message;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Konfiguracja tabeli dla MessageResource w panelu administracyjnym Filament.
 *
 * Definiuje kolumny, filtry i akcje moderacyjne dla listy wiadomości.
 * Umożliwia przeglądanie konwersacji i moderację niewłaściwych treści.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class MessagesTable
{
    /**
     * Konfiguruje tabelę wiadomości dla Filament Resource.
     *
     * Ustawia kolumny wyświetlane w tabeli, filtry umożliwiające
     * wyszukiwanie i filtrowanie danych, oraz akcje moderacyjne
     * dostępne dla administratorów.
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

                // ID Konwersacji (z linkiem)
                TextColumn::make('conversation_id')
                    ->label('Konwersacja')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->sortable(),

                // Nadawca
                TextColumn::make('sender.name')
                    ->label('Nadawca')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->sender?->email),

                // Treść wiadomości (skrócona)
                TextColumn::make('message')
                    ->label('Wiadomość')
                    ->limit(60)
                    ->wrap()
                    ->searchable(),

                // Status przeczytania
                IconColumn::make('is_read')
                    ->label('Przeczytane')
                    ->boolean()
                    ->trueIcon(Heroicon::CheckCircle)
                    ->falseIcon(Heroicon::Clock)
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),

                // Data przeczytania
                TextColumn::make('read_at')
                    ->label('Przeczytano')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Status ukrycia
                IconColumn::make('is_hidden')
                    ->label('Ukryta')
                    ->boolean()
                    ->trueIcon(Heroicon::EyeSlash)
                    ->falseIcon(Heroicon::Eye)
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),

                // Moderator
                TextColumn::make('moderator.name')
                    ->label('Moderator')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Data ukrycia
                TextColumn::make('hidden_at')
                    ->label('Data ukrycia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Data utworzenia
                TextColumn::make('created_at')
                    ->label('Data wysłania')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // Filtr po statusie przeczytania
                TernaryFilter::make('is_read')
                    ->label('Status przeczytania')
                    ->placeholder('Wszystkie wiadomości')
                    ->trueLabel('Tylko przeczytane')
                    ->falseLabel('Tylko nieprzeczytane'),

                // Filtr po statusie ukrycia
                TernaryFilter::make('is_hidden')
                    ->label('Status ukrycia')
                    ->placeholder('Wszystkie wiadomości')
                    ->trueLabel('Tylko ukryte')
                    ->falseLabel('Tylko widoczne'),

                // Filtr po dacie utworzenia
                Filter::make('created_at')
                    ->label('Data wysłania')
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

                // Custom Action: Ukryj wiadomość
                Action::make('hide')
                    ->label('Ukryj')
                    ->icon(Heroicon::EyeSlash)
                    ->color('warning')
                    ->form([
                        Textarea::make('hidden_reason')
                            ->label('Powód ukrycia')
                            ->rows(3)
                            ->placeholder('Opcjonalny powód (spam, obraźliwa treść, itp.)'),
                    ])
                    ->action(function (Message $record, array $data): void {
                        $record->hide(
                            Auth::id(),
                            $data['hidden_reason'] ?? null
                        );
                    })
                    ->visible(fn (Message $record): bool => ! $record->is_hidden)
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Wiadomość ukryta')
                            ->body('Wiadomość została pomyślnie ukryta.')
                    ),

                // Custom Action: Odkryj wiadomość
                Action::make('unhide')
                    ->label('Odkryj')
                    ->icon(Heroicon::Eye)
                    ->color('success')
                    ->action(function (Message $record): void {
                        $record->unhide();
                    })
                    ->visible(fn (Message $record): bool => $record->is_hidden)
                    ->requiresConfirmation()
                    ->modalHeading('Odkryj wiadomość')
                    ->modalDescription('Czy na pewno chcesz odkryć tę wiadomość?')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Wiadomość odkryta')
                            ->body('Wiadomość została pomyślnie odkryta.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Action: Ukryj zaznaczone
                    BulkAction::make('hide_bulk')
                        ->label('Ukryj zaznaczone')
                        ->icon(Heroicon::EyeSlash)
                        ->color('warning')
                        ->form([
                            Textarea::make('hidden_reason')
                                ->label('Powód ukrycia')
                                ->rows(3)
                                ->placeholder('Opcjonalny powód ukrycia'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn (Message $message) => $message->hide(Auth::id(), $data['hidden_reason'] ?? null)
                            );
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Ukryj zaznaczone wiadomości')
                        ->modalDescription('Czy na pewno chcesz ukryć wszystkie zaznaczone wiadomości?')
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Wiadomości ukryte')
                                ->body("{$count} wiadomości zostało pomyślnie ukrytych.");
                        }),

                    // Bulk Action: Odkryj zaznaczone
                    BulkAction::make('unhide_bulk')
                        ->label('Odkryj zaznaczone')
                        ->icon(Heroicon::Eye)
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn (Message $message) => $message->unhide());
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Odkryj zaznaczone wiadomości')
                        ->modalDescription('Czy na pewno chcesz odkryć wszystkie zaznaczone wiadomości?')
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Wiadomości odkryte')
                                ->body("{$count} wiadomości zostało pomyślnie odkrytych.");
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                // Eager loading dla optymalizacji
                return $query->with([
                    'sender:id,name,email',
                    'conversation:id,booking_id',
                    'conversation.booking:id',
                    'moderator:id,name',
                ]);
            });
    }
}
