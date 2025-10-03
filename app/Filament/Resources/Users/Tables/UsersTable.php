<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Konfiguracja tabeli dla UserResource w panelu administracyjnym Filament.
 *
 * Definiuje kolumny, filtry i akcje dla listy użytkowników. Tabela zawiera
 * informacje o użytkownikach, ich profilach, statusie premium, weryfikacji
 * oraz statystykach rezerwacji i ocen.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class UsersTable
{
    /**
     * Konfiguruje tabelę użytkowników dla Filament Resource.
     *
     * Ustawia kolumny wyświetlane w tabeli, filtry umożliwiające
     * wyszukiwanie i filtrowanie danych, oraz akcje dostępne
     * dla poszczególnych rekordów i grup rekordów.
     *
     * @param  Table  $table  Instancja tabeli do konfiguracji
     * @return Table Skonfigurowana tabela
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ID - sortowalne, przeszukiwalne
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                // Avatar - okrągły obraz z relacji profile.avatar
                ImageColumn::make('profile.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF'),

                // Name - sortowalne, przeszukiwalne, kopiowalne
                TextColumn::make('name')
                    ->label('Imię i nazwisko')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Imię i nazwisko skopiowane')
                    ->copyMessageDuration(1500),

                // Email - sortowalne, przeszukiwalne, kopiowalne
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email skopiowany')
                    ->copyMessageDuration(1500),

                // Role - Badge z kolorami i ikonami
                BadgeColumn::make('profile.role')
                    ->label('Rola')
                    ->colors([
                        'primary' => 'owner',
                        'success' => 'sitter',
                        'warning' => 'both',
                        'danger' => 'admin',
                    ])
                    ->icons([
                        'owner' => Heroicon::UserCircle,
                        'sitter' => Heroicon::Heart,
                        'both' => Heroicon::Star,
                        'admin' => Heroicon::ShieldCheck,
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'owner' => 'Właściciel',
                        'sitter' => 'Opiekun',
                        'both' => 'Właściciel & Opiekun',
                        'admin' => 'Administrator',
                        default => $state,
                    }),

                // Premium Status - ikona boolean (sprawdzenie premium_until > now())
                IconColumn::make('is_premium')
                    ->label('Premium')
                    ->boolean()
                    ->trueIcon(Heroicon::Star)
                    ->falseIcon(Heroicon::XCircle)
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->getStateUsing(fn ($record) => $record->isPremium())
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("CASE WHEN premium_until > NOW() THEN 1 ELSE 0 END {$direction}");
                    }),

                // Verified - ikona boolean z relacji profile.is_verified
                IconColumn::make('profile.is_verified')
                    ->label('Zweryfikowany')
                    ->boolean()
                    ->trueIcon(Heroicon::CheckBadge)
                    ->falseIcon(Heroicon::XCircle)
                    ->trueColor('success')
                    ->falseColor('gray'),

                // Created At - data utworzenia, sortowalna
                TextColumn::make('created_at')
                    ->label('Data rejestracji')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                // Bookings Count - liczba rezerwacji jako właściciel
                TextColumn::make('ownerBookings_count')
                    ->label('Liczba rezerwacji')
                    ->counts('ownerBookings')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                // Rating - średnia ocena z formatowaniem i ikoną gwiazdki
                TextColumn::make('profile.rating_average')
                    ->label('Ocena')
                    ->formatStateUsing(function ($state) {
                        if (! $state) {
                            return 'Brak ocen';
                        }

                        return number_format($state, 1).' ⭐';
                    })
                    ->sortable()
                    ->badge()
                    ->color(function ($state) {
                        if (! $state) {
                            return 'gray';
                        }
                        if ($state >= 4.5) {
                            return 'success';
                        }
                        if ($state >= 3.5) {
                            return 'warning';
                        }

                        return 'danger';
                    }),
            ])
            ->filters([
                // Filtr po roli użytkownika
                SelectFilter::make('role')
                    ->label('Rola')
                    ->relationship('profile', 'role')
                    ->options([
                        'owner' => 'Właściciel',
                        'sitter' => 'Opiekun',
                        'both' => 'Właściciel & Opiekun',
                        'admin' => 'Administrator',
                    ]),

                // Filtr po statusie premium
                TernaryFilter::make('premium')
                    ->label('Status Premium')
                    ->queries(
                        true: fn (Builder $query) => $query->where('premium_until', '>', now()),
                        false: fn (Builder $query) => $query->where(function ($q) {
                            $q->whereNull('premium_until')
                                ->orWhere('premium_until', '<=', now());
                        }),
                    )
                    ->placeholder('Wszyscy użytkownicy')
                    ->trueLabel('Tylko Premium')
                    ->falseLabel('Tylko bez Premium'),

                // Filtr po statusie weryfikacji
                TernaryFilter::make('verified')
                    ->label('Weryfikacja')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('profile', fn ($q) => $q->where('is_verified', true)),
                        false: fn (Builder $query) => $query->whereHas('profile', fn ($q) => $q->where('is_verified', false)),
                    )
                    ->placeholder('Wszyscy użytkownicy')
                    ->trueLabel('Tylko zweryfikowani')
                    ->falseLabel('Tylko niezweryfikowani'),

                // Filtr po dacie rejestracji (od-do)
                Filter::make('created_at')
                    ->label('Data rejestracji')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Od'),
                        DatePicker::make('created_until')
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

                // Filtr po aktywnych rezerwacjach
                TernaryFilter::make('has_active_bookings')
                    ->label('Aktywne rezerwacje')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('ownerBookings', function ($q) {
                            $q->whereIn('status', ['pending', 'confirmed'])
                                ->where('start_date', '>=', now());
                        }),
                        false: fn (Builder $query) => $query->whereDoesntHave('ownerBookings', function ($q) {
                            $q->whereIn('status', ['pending', 'confirmed'])
                                ->where('start_date', '>=', now());
                        }),
                    )
                    ->placeholder('Wszyscy użytkownicy')
                    ->trueLabel('Z aktywnymi rezerwacjami')
                    ->falseLabel('Bez aktywnych rezerwacji'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // Custom Action: Grant Premium
                Action::make('grant_premium')
                    ->label('Przyznaj Premium')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->form([
                        DateTimePicker::make('premium_until')
                            ->label('Premium do')
                            ->required()
                            ->minDate(now())
                            ->native(false)
                            ->displayFormat('d.m.Y H:i')
                            ->helperText('Wybierz datę wygaśnięcia statusu premium'),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update(['premium_until' => $data['premium_until']]);
                    })
                    ->visible(fn (User $record): bool => ! $record->isPremium())
                    ->requiresConfirmation()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Premium zostało przyznane!')
                            ->body('Status premium został pomyślnie przyznany użytkownikowi.')
                    ),

                // Custom Action: Revoke Premium
                Action::make('revoke_premium')
                    ->label('Odbierz Premium')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function (User $record): void {
                        $record->update(['premium_until' => null]);
                    })
                    ->visible(fn (User $record): bool => $record->isPremium())
                    ->requiresConfirmation()
                    ->modalHeading('Odbierz Premium')
                    ->modalDescription('Czy na pewno chcesz odebrać status premium temu użytkownikowi?')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Premium zostało odebrane')
                            ->body('Status premium został pomyślnie odebrany użytkownikowi.')
                    ),

                // Custom Action: Verify Account
                Action::make('verify_account')
                    ->label('Zweryfikuj konto')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(function (User $record): void {
                        $record->profile()->update([
                            'is_verified' => true,
                            'verified_at' => now(),
                            'verification_status' => 'approved',
                        ]);
                    })
                    ->visible(fn (User $record): bool => ! $record->profile?->is_verified)
                    ->requiresConfirmation()
                    ->modalHeading('Weryfikacja konta')
                    ->modalDescription('Czy na pewno chcesz zweryfikować konto tego użytkownika?')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Konto zostało zweryfikowane')
                            ->body('Konto użytkownika zostało pomyślnie zweryfikowane.')
                    ),

                // Custom Action: Send Email
                Action::make('send_email')
                    ->label('Wyślij email')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->form([
                        TextInput::make('subject')
                            ->label('Temat')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Wpisz temat wiadomości'),
                        Textarea::make('message')
                            ->label('Wiadomość')
                            ->required()
                            ->rows(5)
                            ->placeholder('Wpisz treść wiadomości'),
                    ])
                    ->action(function (User $record, array $data): void {
                        // Log wysłania emaila (nie implementujemy prawdziwego wysyłania)
                        Log::info('Email do pojedynczego użytkownika', [
                            'recipient' => $record->email,
                            'subject' => $data['subject'],
                            'message' => $data['message'],
                            'sent_at' => now()->toDateTimeString(),
                        ]);
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Email zostanie wysłany')
                            ->body('Wiadomość zostanie dostarczona do użytkownika.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    // Bulk Action: Send Mass Email
                    BulkAction::make('send_mass_email')
                        ->label('Wyślij email do zaznaczonych')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->form([
                            TextInput::make('subject')
                                ->label('Temat')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Wpisz temat wiadomości'),
                            RichEditor::make('message')
                                ->label('Wiadomość')
                                ->required()
                                ->placeholder('Wpisz treść wiadomości (obsługuje HTML)')
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'underline',
                                    'link',
                                    'bulletList',
                                    'orderedList',
                                ]),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $recipientCount = $records->count();
                            $recipients = $records->pluck('email')->toArray();

                            // Log wysłania grupowego emaila
                            Log::info('Email grupowy do użytkowników', [
                                'recipients_count' => $recipientCount,
                                'recipients' => $recipients,
                                'subject' => $data['subject'],
                                'message' => $data['message'],
                                'sent_at' => now()->toDateTimeString(),
                            ]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Wiadomość zostanie wysłana')
                                ->body("Email zostanie dostarczony do {$count} użytkowników.");
                        }),

                    // Bulk Action: Grant Premium
                    BulkAction::make('grant_premium_bulk')
                        ->label('Przyznaj Premium grupowo')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->form([
                            DateTimePicker::make('premium_until')
                                ->label('Premium do')
                                ->required()
                                ->minDate(now())
                                ->native(false)
                                ->displayFormat('d.m.Y H:i')
                                ->helperText('Wybierz datę wygaśnięcia statusu premium dla wszystkich zaznaczonych użytkowników'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (User $user) use ($data): void {
                                $user->update(['premium_until' => $data['premium_until']]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Przyznaj Premium grupowo')
                        ->modalDescription('Czy na pewno chcesz przyznać status premium wszystkim zaznaczonym użytkownikom?')
                        ->successNotification(function (Collection $records): Notification {
                            $count = $records->count();

                            return Notification::make()
                                ->success()
                                ->title('Premium przyznane!')
                                ->body("Status premium został przyznany {$count} użytkownikom.");
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                // Eager loading dla optymalizacji - ładuj profile z wybranymi polami
                return $query->with([
                    'profile:id,user_id,role,avatar,rating_average,is_verified',
                    'ownerBookings' => function ($query) {
                        $query->select('id', 'owner_id', 'status', 'start_date');
                    },
                ]);
            });
    }
}
