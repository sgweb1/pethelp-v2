<?php

namespace App\Filament\Resources\AdminLogs;

use App\Filament\Resources\AdminLogs\Pages\ListAdminLogs;
use App\Filament\Resources\AdminLogs\Pages\ViewAdminLog;
use App\Filament\Resources\AdminLogs\Schemas\AdminLogForm;
use App\Filament\Resources\AdminLogs\Tables\AdminLogsTable;
use App\Models\AdminLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Resource zarządzający logami aktywności administratorów.
 *
 * Umożliwia przeglądanie i analizę wszystkich akcji wykonywanych przez
 * administratorów w panelu Filament. Resource tylko do odczytu - logi
 * są tworzone automatycznie przez system.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class AdminLogResource extends Resource
{
    protected static ?string $model = AdminLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Logi aktywności';

    protected static ?string $modelLabel = 'log aktywności';

    protected static ?string $pluralModelLabel = 'logi aktywności';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    public static function form(Schema $schema): Schema
    {
        return AdminLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminLogsTable::configure($table);
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
            'index' => ListAdminLogs::route('/'),
            'view' => ViewAdminLog::route('/{record}'),
        ];
    }

    /**
     * Określa czy można edytować rekordy.
     *
     * @return bool Zawsze false - logi są tylko do odczytu
     */
    public static function canEdit($record): bool
    {
        return false;
    }

    /**
     * Określa czy można tworzyć nowe rekordy.
     *
     * @return bool Zawsze false - logi tworzone automatycznie
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Określa czy można usuwać rekordy.
     *
     * @return bool Zawsze false - logi są nieusuwalne dla audytu
     */
    public static function canDelete($record): bool
    {
        return false;
    }

    /**
     * Określa czy można usuwać wiele rekordów.
     *
     * @return bool Zawsze false - logi są nieusuwalne dla audytu
     */
    public static function canDeleteAny(): bool
    {
        return false;
    }
}
