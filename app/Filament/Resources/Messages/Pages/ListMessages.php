<?php

namespace App\Filament\Resources\Messages\Pages;

use App\Filament\Resources\Messages\MessageResource;
use Filament\Resources\Pages\ListRecords;

class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Wiadomości są tworzone automatycznie przez system czatu
            // nie ma potrzeby ręcznego tworzenia przez panel admina
        ];
    }
}
