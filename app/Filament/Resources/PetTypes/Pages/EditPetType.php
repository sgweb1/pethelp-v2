<?php

namespace App\Filament\Resources\PetTypes\Pages;

use App\Filament\Resources\PetTypes\PetTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPetType extends EditRecord
{
    protected static string $resource = PetTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
