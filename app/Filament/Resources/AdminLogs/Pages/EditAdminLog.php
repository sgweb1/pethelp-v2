<?php

namespace App\Filament\Resources\AdminLogs\Pages;

use App\Filament\Resources\AdminLogs\AdminLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminLog extends EditRecord
{
    protected static string $resource = AdminLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
