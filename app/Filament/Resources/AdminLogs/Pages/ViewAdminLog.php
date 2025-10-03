<?php

namespace App\Filament\Resources\AdminLogs\Pages;

use App\Filament\Resources\AdminLogs\AdminLogResource;
use Filament\Resources\Pages\ViewRecord;

/**
 * Strona podglądu szczegółów logu aktywności administratora.
 *
 * Wyświetla wszystkie informacje o akcji wykonanej przez administratora
 * w trybie tylko do odczytu.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ViewAdminLog extends ViewRecord
{
    protected static string $resource = AdminLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
