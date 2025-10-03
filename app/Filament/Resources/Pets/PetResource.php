<?php

namespace App\Filament\Resources\Pets;

use App\Filament\Resources\Pets\Pages\CreatePet;
use App\Filament\Resources\Pets\Pages\EditPet;
use App\Filament\Resources\Pets\Pages\ListPets;
use App\Filament\Resources\Pets\Pages\ViewPet;
use App\Filament\Resources\Pets\Schemas\PetForm;
use App\Filament\Resources\Pets\Schemas\PetInfolist;
use App\Filament\Resources\Pets\Tables\PetsTable;
use App\Models\Pet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Resource Filament dla zarządzania zwierzętami.
 *
 * Zapewnia pełny interfejs CRUD dla profili zwierząt z zaawansowanymi polami
 * dla informacji medycznych, behawioralnych i kontaktów awaryjnych.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PetResource extends Resource
{
    protected static ?string $model = Pet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $navigationLabel = 'Zwierzęta';

    protected static ?string $modelLabel = 'zwierzę';

    protected static ?string $pluralModelLabel = 'zwierzęta';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return PetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPets::route('/'),
            'create' => CreatePet::route('/create'),
            'view' => ViewPet::route('/{record}'),
            'edit' => EditPet::route('/{record}/edit'),
        ];
    }
}
