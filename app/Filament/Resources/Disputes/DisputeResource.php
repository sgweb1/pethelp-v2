<?php

namespace App\Filament\Resources\Disputes;

use App\Filament\Resources\Disputes\Pages\CreateDispute;
use App\Filament\Resources\Disputes\Pages\EditDispute;
use App\Filament\Resources\Disputes\Pages\ListDisputes;
use App\Filament\Resources\Disputes\Schemas\DisputeForm;
use App\Filament\Resources\Disputes\Tables\DisputesTable;
use App\Models\Dispute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * DisputeResource - Resource zarządzający zgłoszeniami i sporami.
 *
 * Umożliwia administratorom przeglądanie i rozwiązywanie zgłoszeń
 * problemów związanych z rezerwacjami, przypisywanie spraw do adminów
 * oraz prowadzenie historii komunikacji i decyzji.
 *
 * @package App\Filament\Resources\Disputes
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class DisputeResource extends Resource
{
    protected static ?string $model = Dispute::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'Zgłoszenia i spory';

    protected static ?string $modelLabel = 'Zgłoszenie';

    protected static ?string $pluralModelLabel = 'Zgłoszenia';

    protected static string|UnitEnum|null $navigationGroup = 'Moderacja';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return DisputeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisputesTable::configure($table);
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
            'index' => ListDisputes::route('/'),
            'create' => CreateDispute::route('/create'),
            'edit' => EditDispute::route('/{record}/edit'),
        ];
    }
}
