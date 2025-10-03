<?php

/**
 * Skrypt naprawiający typy w Filament Resources zgodnie z Filament v4
 *
 * Zmienia:
 * - protected static ?string $navigationIcon => protected static string | BackedEnum | null $navigationIcon
 * - protected static ?string $navigationGroup => protected static string | UnitEnum | null $navigationGroup
 * - Dodaje use BackedEnum; i use UnitEnum;
 */
$resourceFiles = [
    'app/Filament/Resources/AdminLogs/AdminLogResource.php',
    'app/Filament/Resources/Bookings/BookingResource.php',
    'app/Filament/Resources/Disputes/DisputeResource.php',
    'app/Filament/Resources/Messages/MessageResource.php',
    'app/Filament/Resources/Notifications/NotificationResource.php',
    'app/Filament/Resources/Payments/PaymentResource.php',
    'app/Filament/Resources/Pets/PetResource.php',
    'app/Filament/Resources/PetTypes/PetTypeResource.php',
    'app/Filament/Resources/Reviews/ReviewResource.php',
    'app/Filament/Resources/ServiceCategories/ServiceCategoryResource.php',
    'app/Filament/Resources/Services/ServiceResource.php',
    'app/Filament/Resources/Users/UserResource.php',
];

foreach ($resourceFiles as $file) {
    if (! file_exists($file)) {
        echo "❌ Plik nie istnieje: $file\n";

        continue;
    }

    $content = file_get_contents($file);
    $originalContent = $content;

    // Dodaj use BackedEnum; jeśli nie istnieje
    if (! str_contains($content, 'use BackedEnum;')) {
        // Znajdź ostatni use statement przed pierwszą klasą
        if (preg_match('/(use [^;]+;\n)([\n]*(?:\/\*\*|class|abstract))/s', $content, $matches)) {
            $content = str_replace(
                $matches[1].$matches[2],
                $matches[1]."use BackedEnum;\n".$matches[2],
                $content
            );
        }
    }

    // Dodaj use UnitEnum; jeśli nie istnieje (tylko dla plików z navigationGroup)
    if (str_contains($content, '$navigationGroup') && ! str_contains($content, 'use UnitEnum;')) {
        if (preg_match('/(use BackedEnum;\n)([\n]*(?:\/\*\*|class|abstract))/s', $content, $matches)) {
            $content = str_replace(
                $matches[1].$matches[2],
                $matches[1]."use UnitEnum;\n".$matches[2],
                $content
            );
        }
    }

    // Napraw typ navigationIcon
    $content = preg_replace(
        '/protected static \?string \$navigationIcon = /',
        'protected static string | BackedEnum | null $navigationIcon = ',
        $content
    );

    // Napraw typ activeNavigationIcon jeśli istnieje
    $content = preg_replace(
        '/protected static \?string \$activeNavigationIcon = /',
        'protected static string | BackedEnum | null $activeNavigationIcon = ',
        $content
    );

    // Napraw typ navigationGroup - już powinno być OK, ale sprawdzamy
    // Szukamy public static string|null (bez spacji) i zamieniamy na protected static string | UnitEnum | null
    $content = preg_replace(
        '/public static string\|null \$navigationGroup = /',
        'protected static string | UnitEnum | null $navigationGroup = ',
        $content
    );

    // Zapisz tylko jeśli coś się zmieniło
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "✅ Naprawiono: $file\n";
    } else {
        echo "⏭️  Bez zmian: $file\n";
    }
}

echo "\n✅ Gotowe!\n";
