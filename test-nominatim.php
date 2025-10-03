<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$lat = 52.3336607715546;
$lon = 20.888992182548073;

echo '=== NOMINATIM REVERSE GEOCODING ==='.PHP_EOL.PHP_EOL;
echo "Współrzędne: $lat, $lon".PHP_EOL.PHP_EOL;

$response = \Illuminate\Support\Facades\Http::timeout(10)
    ->withHeaders(['User-Agent' => 'PetHelp/1.0'])
    ->get('https://nominatim.openstreetmap.org/reverse', [
        'lat' => $lat,
        'lon' => $lon,
        'format' => 'json',
        'addressdetails' => 1,
        'zoom' => 10,
    ]);

if ($response->successful()) {
    $data = $response->json();

    echo 'Display Name: '.($data['display_name'] ?? 'brak').PHP_EOL;
    echo PHP_EOL.'Address components:'.PHP_EOL;

    if (isset($data['address'])) {
        foreach ($data['address'] as $key => $value) {
            echo '  '.str_pad($key.':', 25).$value.PHP_EOL;
        }
    }

    echo PHP_EOL.'Główne pola:'.PHP_EOL;
    echo '  place_id:               '.($data['place_id'] ?? 'brak').PHP_EOL;
    echo '  osm_type:               '.($data['osm_type'] ?? 'brak').PHP_EOL;
    echo '  osm_id:                 '.($data['osm_id'] ?? 'brak').PHP_EOL;
    echo '  lat:                    '.($data['lat'] ?? 'brak').PHP_EOL;
    echo '  lon:                    '.($data['lon'] ?? 'brak').PHP_EOL;
    echo '  display_name:           '.($data['display_name'] ?? 'brak').PHP_EOL;

    echo PHP_EOL.'Pełna odpowiedź JSON:'.PHP_EOL;
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo 'Błąd: '.$response->status().PHP_EOL;
}

echo PHP_EOL;
