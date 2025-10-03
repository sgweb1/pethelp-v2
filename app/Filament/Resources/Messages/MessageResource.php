<?php

namespace App\Filament\Resources\Messages;

use App\Filament\Resources\Messages\Pages\CreateMessage;
use App\Filament\Resources\Messages\Pages\EditMessage;
use App\Filament\Resources\Messages\Pages\ListMessages;
use App\Filament\Resources\Messages\Schemas\MessageForm;
use App\Filament\Resources\Messages\Tables\MessagesTable;
use App\Models\Message;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * MessageResource - Resource zarządzający wiadomościami w panelu administracyjnym.
 *
 * Umożliwia przeglądanie konwersacji między użytkownikami,
 * moderację wiadomości (ukrywanie niewłaściwych treści) oraz
 * przegląd historii komunikacji związanej z rezerwacjami.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Wiadomości';

    protected static ?string $modelLabel = 'Wiadomość';

    protected static ?string $pluralModelLabel = 'Wiadomości';

    protected static ?string $navigationGroup = 'Moderacja';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MessagesTable::configure($table);
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
            'index' => ListMessages::route('/'),
            'create' => CreateMessage::route('/create'),
            'edit' => EditMessage::route('/{record}/edit'),
        ];
    }
}
