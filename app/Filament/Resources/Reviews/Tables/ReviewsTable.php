<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Konfiguracja tabeli dla ReviewResource w panelu administracyjnym Filament.
 *
 * Definiuje kolumny, filtry i akcje moderacyjne dla listy recenzji.
 * Tabela zawiera informacje o recenzjach, ich statusie moderacji,
 * ocenach oraz autorach.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ReviewsTable
{
    /**
     * Konfiguruje tabelę recenzji dla Filament Resource.
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

                // Rezerwacja
                TextColumn::make('booking.id')
                    ->label('Rezerwacja')
                    ->formatStateUsing(fn ($state, $record) => "#{$state}")
                    ->url(fn ($record) => $record->booking
                        ? route('filament.admin.resources.bookings.edit', ['record' => $record->booking])
                        : null)
                    ->color('info')
                    ->sortable(),

                // Autor recenzji
                TextColumn::make('reviewer.name')
                    ->label('Autor')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->reviewer?->email),

                // Osoba oceniana
                TextColumn::make('reviewee.name')
                    ->label('Osoba oceniana')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->reviewee?->email),

                // Ocena (gwiazdki)
                TextColumn::make('rating')
                    ->label('Ocena')
                    ->formatStateUsing(fn ($state, $record) => $record->stars)
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    }),

                // Komentarz (skrócony)
                TextColumn::make('comment')
                    ->label('Komentarz')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                // Status moderacji
                BadgeColumn::make('moderation_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Oczekuje',
                        'approved' => 'Zaakceptowana',
                        'rejected' => 'Odrzucona',
                        default => $state,
                    })
                    ->sortable(),

                // Widoczność
                IconColumn::make('is_visible')
                    ->label('Widoczna')
                    ->boolean()
                    ->trueIcon(Heroicon::Eye)
                    ->falseIcon(Heroicon::EyeSlash)
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                // Odpowiedź admina
                IconColumn::make('admin_response')
                    ->label('Odpowiedź')
                    ->boolean()
                    ->trueIcon(Heroicon::ChatBubbleLeftRight)
                    ->falseIcon(Heroicon::MinusCircle)
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->getStateUsing(fn ($record) => ! empty($record->admin_response))
                    ->toggleable(),

                // Moderator
                TextColumn::make('moderator.name')
                    ->label('Moderator')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Data moderacji
                TextColumn::make('moderated_at')
                    ->label('Data moderacji')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Data utworzenia
                TextColumn::make('created_at')
                    ->label('Data utworzenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // Filtr po statusie moderacji
                SelectFilter::make('moderation_status')
                    ->label('Status moderacji')
                    ->options([
                        'pending' => 'Oczekuje',
                        'approved' => 'Zaakceptowana',
                        'rejected' => 'Odrzucona',
                    ])
                    ->placeholder('Wszystkie statusy'),

                // Filtr po widoczności
                TernaryFilter::make('is_visible')
                    ->label('Widoczność')
                    ->placeholder('Wszystkie recenzje')
                    ->trueLabel('Tylko widoczne')
                    ->falseLabel('Tylko ukryte'),

                // Filtr po ocenie
                SelectFilter::make('rating')
                    ->label('Ocena')
                    ->options([
                        '5' => '5 gwiazdek ⭐⭐⭐⭐⭐',
                        '4' => '4 gwiazdki ⭐⭐⭐⭐',
                        '3' => '3 gwiazdki ⭐⭐⭐',
                        '2' => '2 gwiazdki ⭐⭐',
                        '1' => '1 gwiazdka ⭐',
                    ])
                    ->placeholder('Wszystkie oceny'),

                // Filtr po odpowiedzi admina
                TernaryFilter::make('has_response')
                    ->label('Odpowiedź administratora')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('admin_response'),
                        false: fn (Builder $query) => $query->whereNull('admin_response'),
                    )
                    ->placeholder('Wszystkie')
                    ->trueLabel('Z odpowiedzią')
                    ->falseLabel('Bez odpowiedzi'),

                // Filtr po dacie utworzenia
                Filter::make('created_at')
                    ->label('Data utworzenia')
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

                // Custom Action: Zaakceptuj recenzję
                Action::make('approve')
                    ->label('Zaakceptuj')
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->form([
                        Textarea::make('admin_response')
                            ->label('Odpowiedź administratora (opcjonalna)')
                            ->rows(3)
                            ->placeholder('Możesz dodać publiczną odpowiedź na recenzję'),
                    ])
                    ->action(function (Review $record, array $data): void {
                        $record->approve(
                            Auth::id(),
                            $data['admin_response'] ?? null
                        );
                    })
                    ->visible(fn (Review $record): bool => $record->moderation_status !== 'approved')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Recenzja zaakceptowana')
                            ->body('Recenzja została pomyślnie zaakceptowana i jest teraz widoczna.')
                    ),

                // Custom Action: Odrzuć recenzję
                Action::make('reject')
                    ->label('Odrzuć')
                    ->icon(Heroicon::XCircle)
                    ->color('danger')
                    ->form([
                        Textarea::make('admin_response')
                            ->label('Powód odrzucenia (opcjonalny)')
                            ->rows(3)
                            ->placeholder('Możesz dodać informację dlaczego recenzja została odrzucona'),
                    ])
                    ->action(function (Review $record, array $data): void {
                        $record->reject(
                            Auth::id(),
                            $data['admin_response'] ?? null
                        );
                    })
                    ->visible(fn (Review $record): bool => $record->moderation_status !== 'rejected')
                    ->requiresConfirmation()
                    ->modalHeading('Odrzuć recenzję')
                    ->modalDescription('Czy na pewno chcesz odrzucić tę recenzję? Zostanie ona ukryta przed użytkownikami.')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Recenzja odrzucona')
                            ->body('Recenzja została pomyślnie odrzucona i ukryta.')
                    ),

                // Custom Action: Ukryj/Pokaż
                Action::make('toggle_visibility')
                    ->label(fn (Review $record) => $record->is_visible ? 'Ukryj' : 'Pokaż')
                    ->icon(fn (Review $record) => $record->is_visible ? Heroicon::EyeSlash : Heroicon::Eye)
                    ->color(fn (Review $record) => $record->is_visible ? 'warning' : 'success')
                    ->action(function (Review $record): void {
                        if ($record->is_visible) {
                            $record->hide();
                        } else {
                            $record->show();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Widoczność zmieniona')
                            ->body('Widoczność recenzji została pomyślnie zmieniona.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Action: Zaakceptuj zaznaczone
                    BulkAction::make('approve_bulk')
                        ->label('Zaakceptuj zaznaczone')
                        ->icon(Heroicon::CheckCircle)
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn (Review $review) => $review->approve(Auth::id()));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Zaakceptuj zaznaczone recenzje')
                        ->modalDescription('Czy na pewno chcesz zaakceptować wszystkie zaznaczone recenzje?')
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Recenzje zaakceptowane')
                                ->body("{$count} recenzji zostało pomyślnie zaakceptowanych.");
                        }),

                    // Bulk Action: Odrzuć zaznaczone
                    BulkAction::make('reject_bulk')
                        ->label('Odrzuć zaznaczone')
                        ->icon(Heroicon::XCircle)
                        ->color('danger')
                        ->action(function (Collection $records): void {
                            $records->each(fn (Review $review) => $review->reject(Auth::id()));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Odrzuć zaznaczone recenzje')
                        ->modalDescription('Czy na pewno chcesz odrzucić wszystkie zaznaczone recenzje? Zostaną one ukryte.')
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Recenzje odrzucone')
                                ->body("{$count} recenzji zostało pomyślnie odrzuconych.");
                        }),

                    // Bulk Action: Ukryj zaznaczone
                    BulkAction::make('hide_bulk')
                        ->label('Ukryj zaznaczone')
                        ->icon(Heroicon::EyeSlash)
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $records->each(fn (Review $review) => $review->hide());
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Recenzje ukryte')
                                ->body("{$count} recenzji zostało pomyślnie ukrytych.");
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                // Eager loading dla optymalizacji
                return $query->with([
                    'reviewer:id,name,email',
                    'reviewee:id,name,email',
                    'booking:id,service_id',
                    'booking.service:id,title',
                    'moderator:id,name',
                ]);
            });
    }
}
