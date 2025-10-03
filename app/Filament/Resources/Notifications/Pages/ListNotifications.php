<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use Filament\Resources\Pages\ListRecords;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Powiadomienia są tworzone automatycznie przez system
            // nie ma potrzeby ręcznego tworzenia przez panel admina
        ];
    }
}
