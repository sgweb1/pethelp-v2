<?php

namespace App\Filament\Resources\PetTypes;

use App\Filament\Resources\PetTypes\Pages\CreatePetType;
use App\Filament\Resources\PetTypes\Pages\EditPetType;
use App\Filament\Resources\PetTypes\Pages\ListPetTypes;
use App\Filament\Resources\PetTypes\Schemas\PetTypeForm;
use App\Filament\Resources\PetTypes\Tables\PetTypesTable;
use App\Models\PetType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PetTypeResource extends Resource
{
    protected static ?string $model = PetType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Typy zwierząt';

    protected static ?string $modelLabel = 'Typ zwierzęcia';

    protected static ?string $pluralModelLabel = 'Typy zwierząt';

    protected static string|UnitEnum|null $navigationGroup = 'Zwierzęta';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PetTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PetTypesTable::configure($table);
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
            'index' => ListPetTypes::route('/'),
            'create' => CreatePetType::route('/create'),
            'edit' => EditPetType::route('/{record}/edit'),
        ];
    }
}
