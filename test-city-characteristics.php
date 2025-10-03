<?php

/**
 * Skrypt testowy dla systemu charakterystyk miast.
 *
 * Sprawdza wykrywanie kontekstu dla różnych typów miast w Polsce
 * i pokazuje zastosowane współczynniki korekcyjne.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\GUSApiService;

// Kolorowe output w terminalu
function colorize($text, $color)
{
    $colors = [
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'red' => "\033[31m",
        'cyan' => "\033[36m",
        'reset' => "\033[0m",
    ];

    return ($colors[$color] ?? '').$text.$colors['reset'];
}

echo colorize("\n╔════════════════════════════════════════════════════════════╗\n", 'cyan');
echo colorize("║   Test systemu charakterystyk miast - Rozszerzona baza    ║\n", 'cyan');
echo colorize("╚════════════════════════════════════════════════════════════╝\n", 'cyan');

$gus = app(GUSApiService::class);

$testCities = [
    // Duże miasta uniwersyteckie
    ['name' => 'Warszawa', 'lat' => 52.2297, 'lng' => 21.0122, 'radius' => 15],
    ['name' => 'Kraków', 'lat' => 50.0647, 'lng' => 19.9450, 'radius' => 12],
    ['name' => 'Wrocław', 'lat' => 51.1079, 'lng' => 17.0385, 'radius' => 12],

    // Kurorty górskie
    ['name' => 'Zakopane', 'lat' => 49.2992, 'lng' => 19.9496, 'radius' => 8],
    ['name' => 'Karpacz', 'lat' => 50.7753, 'lng' => 15.7410, 'radius' => 5],
    ['name' => 'Wisła', 'lat' => 49.6525, 'lng' => 18.8619, 'radius' => 6],

    // Kurorty nadmorskie
    ['name' => 'Sopot', 'lat' => 54.4419, 'lng' => 18.5601, 'radius' => 5],
    ['name' => 'Kołobrzeg', 'lat' => 54.1758, 'lng' => 15.5831, 'radius' => 8],
    ['name' => 'Świnoujście', 'lat' => 53.9108, 'lng' => 14.2473, 'radius' => 8],

    // Miejsca pielgrzymkowe
    ['name' => 'Częstochowa', 'lat' => 50.8118, 'lng' => 19.1203, 'radius' => 10],
    ['name' => 'Licheń', 'lat' => 52.3211, 'lng' => 18.3519, 'radius' => 5],

    // Centra przemysłowe
    ['name' => 'Gliwice', 'lat' => 50.2945, 'lng' => 18.6714, 'radius' => 10],
    ['name' => 'Katowice', 'lat' => 50.2599, 'lng' => 19.0216, 'radius' => 12],

    // Mniejsze miasta
    ['name' => 'Olsztyn', 'lat' => 53.7766, 'lng' => 20.4765, 'radius' => 10],
    ['name' => 'Lublin', 'lat' => 51.2465, 'lng' => 22.5684, 'radius' => 12],
];

foreach ($testCities as $city) {
    echo "\n".colorize("━━━ {$city['name']} ━━━", 'yellow')."\n";

    $clients = $gus->estimatePotentialClients($city['lat'], $city['lng'], $city['radius']);

    // Pobierz ostatnie logi dla tego miasta
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logs = file($logFile);
        $logs = array_reverse($logs);

        // Znajdź logi kontekstu dla tego miasta
        foreach ($logs as $log) {
            if (strpos($log, 'Wykryto kontekst miasta') !== false) {
                preg_match('/\{([^}]+)\}/', $log, $matches);
                if ($matches) {
                    $context = json_decode('{'.$matches[1].'}', true);
                    if ($context) {
                        echo colorize('  Kategoria: ', 'blue').$context['category']."\n";
                        echo colorize('  Populacja gridowa: ', 'blue').number_format($context['population'], 0, ',', ' ')."\n";

                        $flags = [];
                        if ($context['has_university'] ?? false) {
                            $flags[] = '🎓 Uniwersytet';
                        }
                        if ($context['is_tourist_destination'] ?? false) {
                            $flags[] = '🏖️ Turystyka';
                        }
                        if ($context['is_commuter_hub'] ?? false) {
                            $flags[] = '🚗 Dojazdy';
                        }

                        if (! empty($flags)) {
                            echo colorize('  Cechy: ', 'blue').implode(' | ', $flags)."\n";
                        }
                    }
                }
                break;
            }
        }

        // Znajdź logi współczynników
        foreach ($logs as $log) {
            if (strpos($log, 'Zastosowano współczynniki korekcyjne') !== false) {
                preg_match('/correction_factor["\']:\s*([0-9.]+)/', $log, $matches);
                if ($matches) {
                    $factor = (float) $matches[1];
                    $percentage = ($factor - 1.0) * 100;
                    echo colorize('  Współczynnik korekty: ', 'blue').$factor.' ('.
                        colorize(sprintf('%+.1f%%', $percentage), $percentage > 0 ? 'green' : 'red').")\n";
                }
                break;
            }
        }
    }

    echo colorize('  ➜ Potencjalni klienci: ', 'green').colorize(number_format($clients, 0, ',', ' '), 'green')."\n";
}

echo "\n".colorize("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n", 'cyan');
echo colorize("Test zakończony!\n\n", 'green');

// Statystyki pokrycia
$config = config('city_characteristics');
echo colorize("📊 Statystyki bazy danych:\n", 'yellow');
echo '   • Miasta uniwersyteckie: '.colorize(count($config['university_cities']), 'green')."\n";
echo '   • Destynacje turystyczne: '.colorize(count($config['tourist_destinations']), 'green')."\n";
echo '   • Centra dojazdowe: '.colorize(count($config['commuter_hubs']), 'green')."\n";
echo '   • Aglomeracje: '.colorize(count($config['agglomerations']), 'green')."\n";

echo "\n";
