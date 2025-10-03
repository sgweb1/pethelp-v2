<?php

namespace App\Filament\Resources\PetTypes\Pages;

use App\Filament\Resources\PetTypes\PetTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePetType extends CreateRecord
{
    protected static string $resource = PetTypeResource::class;
}
