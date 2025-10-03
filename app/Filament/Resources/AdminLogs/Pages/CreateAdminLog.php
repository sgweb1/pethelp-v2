<?php

namespace App\Filament\Resources\AdminLogs\Pages;

use App\Filament\Resources\AdminLogs\AdminLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminLog extends CreateRecord
{
    protected static string $resource = AdminLogResource::class;
}
