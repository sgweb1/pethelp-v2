<?php

namespace App\Filament\Resources\Notifications;

use App\Filament\Resources\Notifications\Pages\CreateNotification;
use App\Filament\Resources\Notifications\Pages\EditNotification;
use App\Filament\Resources\Notifications\Pages\ListNotifications;
use App\Filament\Resources\Notifications\Schemas\NotificationForm;
use App\Filament\Resources\Notifications\Tables\NotificationsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Notifications\DatabaseNotification;
use UnitEnum;

/**
 * NotificationResource - Resource zarządzający powiadomieniami systemowymi.
 *
 * Umożliwia administratorom przeglądanie wszystkich powiadomień wysłanych
 * przez system do użytkowników, filtrowanie po typie i statusie oraz
 * zarządzanie przeczytaniem powiadomień.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class NotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?string $navigationLabel = 'Powiadomienia';

    protected static ?string $modelLabel = 'Powiadomienie';

    protected static ?string $pluralModelLabel = 'Powiadomienia';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return NotificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotifications::route('/'),
            'create' => CreateNotification::route('/create'),
            'edit' => EditNotification::route('/{record}/edit'),
        ];
    }
}
