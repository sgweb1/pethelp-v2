<?php

namespace App\Filament\Resources\PetTypes\Pages;

use App\Filament\Resources\PetTypes\PetTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPetTypes extends ListRecords
{
    protected static string $resource = PetTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
