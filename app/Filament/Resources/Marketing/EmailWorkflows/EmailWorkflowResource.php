<?php

namespace App\Filament\Resources\Marketing\EmailWorkflows;

use App\Filament\Resources\Marketing\EmailWorkflows\Pages\CreateEmailWorkflow;
use App\Filament\Resources\Marketing\EmailWorkflows\Pages\EditEmailWorkflow;
use App\Filament\Resources\Marketing\EmailWorkflows\Pages\ListEmailWorkflows;
use App\Filament\Resources\Marketing\EmailWorkflows\Schemas\EmailWorkflowForm;
use App\Filament\Resources\Marketing\EmailWorkflows\Tables\EmailWorkflowsTable;
use App\Models\EmailWorkflow;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Filament Resource dla zarządzania Email Workflows.
 *
 * Umożliwia tworzenie i zarządzanie zautomatyzowanymi sekwencjami emaili
 * wysyłanych na podstawie triggerów (rejestracja, booking, nieaktywność).
 *
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class EmailWorkflowResource extends Resource
{
    protected static ?string $model = EmailWorkflow::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Email Workflows';

    protected static ?string $modelLabel = 'Workflow';

    protected static ?string $pluralModelLabel = 'Workflows';

    public static function form(Schema $schema): Schema
    {
        return EmailWorkflowForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailWorkflowsTable::configure($table);
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
            'index' => ListEmailWorkflows::route('/'),
            'create' => CreateEmailWorkflow::route('/create'),
            'edit' => EditEmailWorkflow::route('/{record}/edit'),
        ];
    }
}
