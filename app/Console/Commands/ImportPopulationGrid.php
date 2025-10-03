<?php

namespace App\Console\Commands;

use App\Models\PopulationGrid;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * Komenda do importu siatek populacyjnych z Eurostat Census 2021.
 *
 * Pobiera i importuje dane o rozmieszczeniu ludności w kratkach 1×1 km
 * dla Polski z oficjalnych danych Eurostat GEOSTAT.
 *
 * @author Claude AI Assistant
 *
 * @version 1.0.0
 */
class ImportPopulationGrid extends Command
{
    /**
     * Sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'population:import-grid
                            {--country=PL : Kod kraju (domyślnie PL - Polska)}
                            {--force : Wymuś ponowny import (usuń istniejące dane)}
                            {--batch=1000 : Rozmiar batcha podczas importu}
                            {--file= : Ścieżka do lokalnego pliku ZIP (opcjonalnie)}
                            {--csv= : Ścieżka do lokalnego pliku CSV (opcjonalnie)}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Importuje siatki populacyjne 1km² z Eurostat Census 2021 dla Polski';

    /**
     * URL do pobrania danych Eurostat Census Grid 2021.
     */
    private const EUROSTAT_DATA_URL = 'https://gisco-services.ec.europa.eu/census/2021/Eurostat_Census-GRID_2021_V2.2.zip';

    /**
     * Wykonuje komendę.
     */
    public function handle(): int
    {
        $country = $this->option('country');
        $force = $this->option('force');
        $batchSize = (int) $this->option('batch');
        $localFile = $this->option('file');
        $csvFile = $this->option('csv');

        $this->info("🌍 Import siatek populacyjnych dla kraju: {$country}");
        $this->newLine();

        // Sprawdź czy dane już istnieją
        if (! $force && PopulationGrid::count() > 0) {
            $count = PopulationGrid::count();
            $this->warn("⚠️  Baza zawiera już {$count} kratek populacyjnych.");
            $this->warn('   Użyj --force aby wymusić ponowny import.');

            return self::FAILURE;
        }

        // Krok 1: Przygotuj plik CSV
        if ($csvFile) {
            // Użyj bezpośrednio podanego pliku CSV
            $this->info('📄 Używam lokalnego pliku CSV...');
            $this->info('   Ścieżka: '.$csvFile);

            if (! file_exists($csvFile)) {
                $this->error('   Plik nie istnieje: '.$csvFile);

                return self::FAILURE;
            }

            $csvPath = $csvFile;
            $fileSize = filesize($csvPath);
            $this->info('   Rozmiar: '.round($fileSize / 1024 / 1024, 2).' MB');
            $this->newLine();
        } else {
            // Pobierz i rozpakuj dane z ZIP
            $csvPath = $this->downloadAndExtractData($localFile);
            if (! $csvPath) {
                $this->error('❌ Nie udało się pobrać i rozpakować danych.');

                return self::FAILURE;
            }
        }

        // Krok 2: Import danych do bazy
        $this->info('📥 Importowanie danych do bazy...');

        if ($force) {
            $this->warn('🗑️  Usuwanie istniejących danych...');
            PopulationGrid::truncate();
        }

        try {
            $imported = $this->importCsvData($csvPath, $country, $batchSize);

            $this->newLine();
            $this->info('✅ Import zakończony pomyślnie!');
            $this->info("   Zaimportowano {$imported} kratek dla kraju {$country}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Błąd podczas importu: '.$e->getMessage());
            Log::error('Import population grid failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Pobiera i rozpakowuje dane z Eurostat lub używa lokalnego pliku.
     *
     * @param  string|null  $localFilePath  Ścieżka do lokalnego pliku ZIP
     * @return string|null Ścieżka do pliku CSV lub null jeśli błąd
     */
    private function downloadAndExtractData(?string $localFilePath = null): ?string
    {
        // Utwórz katalog tymczasowy
        $tempDir = storage_path('app/temp/population_grid');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = $tempDir.'/eurostat_grid.zip';

        // Użyj lokalnego pliku lub pobierz z internetu
        if ($localFilePath) {
            $this->info('📁 Używam lokalnego pliku ZIP...');
            $this->info('   Ścieżka: '.$localFilePath);

            if (! file_exists($localFilePath)) {
                $this->error('   Plik nie istnieje: '.$localFilePath);

                return null;
            }

            // Skopiuj lokalny plik do temp
            copy($localFilePath, $zipPath);

            $fileSize = filesize($zipPath);
            $this->info('   Rozmiar: '.round($fileSize / 1024 / 1024, 2).' MB');
        } else {
            $this->info('📡 Pobieranie danych z Eurostat...');

            // Pobierz plik ZIP
            try {
                $this->info('   URL: '.self::EUROSTAT_DATA_URL);

                $response = Http::timeout(300)
                    ->withOptions(['sink' => $zipPath])
                    ->get(self::EUROSTAT_DATA_URL);

                if (! $response->successful()) {
                    $this->error("   Błąd HTTP: {$response->status()}");

                    return null;
                }

                $fileSize = filesize($zipPath);
                $this->info('   Pobrano '.round($fileSize / 1024 / 1024, 2).' MB');
            } catch (\Exception $e) {
                $this->error('   Błąd pobierania: '.$e->getMessage());

                return null;
            }
        }

        // Rozpakuj ZIP
        $this->info('📦 Rozpakowywanie archiwum...');

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            $this->error('   Nie udało się otworzyć archiwum ZIP');

            return null;
        }

        $extractPath = $tempDir.'/extracted';
        $zip->extractTo($extractPath);
        $zip->close();

        // Znajdź plik CSV
        $csvFiles = glob($extractPath.'/*.csv');
        if (empty($csvFiles)) {
            $this->error('   Nie znaleziono pliku CSV w archiwum');

            return null;
        }

        $csvPath = $csvFiles[0];
        $this->info('   Znaleziono: '.basename($csvPath));

        return $csvPath;
    }

    /**
     * Importuje dane z pliku CSV do bazy danych.
     *
     * @param  string  $csvPath  Ścieżka do pliku CSV
     * @param  string  $country  Kod kraju do filtrowania
     * @param  int  $batchSize  Rozmiar batcha
     * @return int Liczba zaimportowanych rekordów
     */
    private function importCsvData(string $csvPath, string $country, int $batchSize): int
    {
        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            throw new \Exception("Nie można otworzyć pliku CSV: {$csvPath}");
        }

        // Przeczytaj nagłówek
        $header = fgetcsv($handle);
        if (! $header) {
            throw new \Exception('Plik CSV jest pusty');
        }

        // Znajdź indeksy kolumn dla nowego formatu Eurostat Census 2021 V2
        $spatialIdx = array_search('SPATIAL', $header);
        $statIdx = array_search('STAT', $header);
        $obsValueIdx = array_search('OBS_VALUE', $header);
        $timePeriodIdx = array_search('TIME_PERIOD', $header);

        if ($spatialIdx === false || $statIdx === false || $obsValueIdx === false) {
            throw new \Exception('Nie znaleziono wymaganych kolumn w CSV (SPATIAL, STAT, OBS_VALUE)');
        }

        $this->info('   Kolumny: SPATIAL='.$spatialIdx.', STAT='.$statIdx.', OBS_VALUE='.$obsValueIdx);
        $this->info('   Format: Eurostat Census 2021 V2 (EPSG:3035 LAEA)');
        $this->newLine();

        // Policz linie (dla progress bara)
        $totalLines = 0;
        while (fgets($handle)) {
            $totalLines++;
        }
        rewind($handle);
        fgetcsv($handle); // Pomiń nagłówek ponownie

        // Progress bar
        $bar = $this->output->createProgressBar($totalLines);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $imported = 0;
        $batch = [];
        $lineNum = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $lineNum++;
            $bar->advance();

            // Pobierz dane z wiersza
            $spatial = $row[$spatialIdx] ?? null;
            $stat = $row[$statIdx] ?? null;
            $population = $row[$obsValueIdx] ?? 0;
            $year = isset($timePeriodIdx) && isset($row[$timePeriodIdx]) ? (int) $row[$timePeriodIdx] : 2021;

            // Filtruj tylko total population (STAT=T) dla danego kraju
            if (! $spatial || ! str_starts_with($spatial, $country.'_CRS3035RES1000m')) {
                continue;
            }

            // Filtruj tylko total population
            if ($stat !== 'T') {
                continue;
            }

            // Parsuj współrzędne LAEA z SPATIAL
            // Format: PL_CRS3035RES1000mN{northing}E{easting}
            if (! preg_match('/N(\d+)E(\d+)$/', $spatial, $matches)) {
                continue;
            }

            $northing = (int) $matches[1];
            $easting = (int) $matches[2];

            // Konwertuj LAEA (EPSG:3035) na WGS84 (EPSG:4326)
            [$lat, $lon] = $this->laeaToWgs84($easting, $northing);

            // Walidacja współrzędnych
            if (! $lat || ! $lon) {
                continue;
            }

            // Dodaj do batcha
            $batch[] = [
                'grid_id' => $spatial,
                'latitude' => $lat,
                'longitude' => $lon,
                'population' => max(0, (int) $population),
                'year' => $year,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Zapisz batch gdy osiągnie rozmiar
            if (count($batch) >= $batchSize) {
                DB::table('population_grid')->insert($batch);
                $imported += count($batch);
                $batch = [];
            }
        }

        // Zapisz pozostałe rekordy
        if (! empty($batch)) {
            DB::table('population_grid')->insert($batch);
            $imported += count($batch);
        }

        $bar->finish();
        fclose($handle);

        return $imported;
    }

    /**
     * Konwertuje współrzędne z LAEA (EPSG:3035) na WGS84 (EPSG:4326).
     *
     * Używa uproszczonej konwersji odwrotnej projekcji Lambert Azimuthal Equal Area.
     * Wystarczająca dokładność dla gridów 1km².
     *
     * @param  int  $easting  Współrzędna E w metrach
     * @param  int  $northing  Współrzędna N w metrach
     * @return array [latitude, longitude]
     */
    private function laeaToWgs84(int $easting, int $northing): array
    {
        // Parametry EPSG:3035 (ETRS89-LAEA)
        $a = 6378137.0; // promień równikowy WGS84
        $e = 0.0818191908426; // mimośród
        $lat0 = deg2rad(52.0); // szerokość początkowa
        $lon0 = deg2rad(10.0); // długość początkowa
        $x0 = 4321000.0; // false easting
        $y0 = 3210000.0; // false northing

        // Usuń false easting/northing
        $x = $easting - $x0;
        $y = $northing - $y0;

        // Oblicz rho
        $rho = sqrt($x * $x + $y * $y);

        if ($rho < 0.001) {
            // Punkt bardzo blisko środka projekcji
            return [rad2deg($lat0), rad2deg($lon0)];
        }

        // Oblicz c
        $c = 2 * asin($rho / (2 * $a));

        // Oblicz latitude
        $lat = asin(cos($c) * sin($lat0) + ($y * sin($c) * cos($lat0) / $rho));

        // Oblicz longitude
        $lon = $lon0 + atan2($x * sin($c), ($rho * cos($lat0) * cos($c) - $y * sin($lat0) * sin($c)));

        return [
            round(rad2deg($lat), 7),
            round(rad2deg($lon), 7),
        ];
    }
}
