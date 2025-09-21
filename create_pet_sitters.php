<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MapItem;
use App\Models\User;

echo "Sprawdzanie istniejących użytkowników...\n";
$userIds = User::pluck('id')->toArray();
echo "Znaleziono " . count($userIds) . " użytkowników\n";

if (empty($userIds)) {
    echo "Tworzenie użytkowników...\n";
    User::factory(50)->create();
    $userIds = User::pluck('id')->toArray();
}

echo "Tworzenie 400 pet sitterów...\n";

for ($i = 0; $i < 4; $i++) {
    $batch = 100;
    echo "Batch " . ($i + 1) . " - tworzenie $batch pet sitterów...\n";

    MapItem::factory()
        ->count($batch)
        ->petSitter()
        ->create([
            'user_id' => function() use ($userIds) {
                return $userIds[array_rand($userIds)];
            }
        ]);

    echo "Utworzono batch " . ($i + 1) . "\n";
}

$totalPetSitters = MapItem::where('content_type', 'pet_sitter')->count();
echo "Łączna liczba pet sitterów: $totalPetSitters\n";
echo "Gotowe!\n";