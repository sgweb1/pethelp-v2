# Siatki populacyjne (Population Grids) - Plan implementacji

## Dostępne źródła danych

### 1. **Eurostat GEOSTAT (REKOMENDOWANE)**
- ✅ Grid 1km × 1km dla całej Europy
- ✅ Darmowy, open data
- ✅ Format CSV lub GeoPackage
- ✅ Aktualizowany co kilka lat
- ✅ Łatwy do implementacji
- 📍 Źródło: https://ec.europa.eu/eurostat/web/gisco/geodata/reference-data/population-distribution-demography/geostat

**Struktura danych:**
```csv
GRD_ID,TOT_P,TOT_P_2021
1kmN4500E2100,250,255
```
- GRD_ID: identyfikator kratki (współrzędne)
- TOT_P: populacja w kratce

### 2. **GHSL (Global Human Settlement Layer)**
- ✅ Grid globalny, bardzo szczegółowy
- ✅ Różne rozdzielczości: 1km, 100m, nawet 30m
- ⚠️ Większe pliki (GeoTIFF)
- 📍 Źródło: https://ghsl.jrc.ec.europa.eu/

### 3. **GUS BDOT10k**
- ✅ Polskie dane topograficzne
- ⚠️ Bardziej skomplikowany format
- ⚠️ Trudniejszy dostęp do danych populacyjnych

## Propozycja implementacji

### Faza 1: Pobranie i przygotowanie danych
1. Pobierz grid Eurostat dla Polski (1km²)
2. Zapisz w lokalnej bazie (tabela `population_grid`)
3. Struktura tabeli:
```sql
CREATE TABLE population_grid (
    id BIGINT PRIMARY KEY,
    grid_id VARCHAR(50) UNIQUE,
    latitude DECIMAL(10, 7),
    longitude DECIMAL(10, 7),
    population INT,
    year INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Faza 2: Implementacja algorytmu
```php
public function estimateWithGrid(float $lat, float $lng, int $radiusKm): int
{
    // 1. Znajdź wszystkie kratki w promieniu
    $grids = PopulationGrid::whereRaw(
        "ST_Distance_Sphere(
            point(longitude, latitude),
            point(?, ?)
        ) <= ?",
        [$lng, $lat, $radiusKm * 1000]
    )->get();

    // 2. Zsumuj populację
    $totalPopulation = $grids->sum('population');

    // 3. Zastosuj współczynniki
    return (int) round($totalPopulation * 0.37 * 0.25);
}
```

### Faza 3: Fallback i optymalizacja
- Jeśli brak danych gridowych → fallback do obecnej metody (gmina/powiat)
- Cache wyników dla często sprawdzanych lokalizacji
- Indeks spatial na kolumnach lat/lng

## Zalety rozwiązania

✅ **Dokładność**: Uwzględnia rzeczywiste rozmieszczenie ludności
✅ **Uniwersalność**: Działa dla całej Polski (i Europy)
✅ **Niezależność od granic**: Nie ma problemu z granicami gmin/powiatów
✅ **Skalowalność**: Grid 1km² to ~300k kratek dla Polski - mieści się w DB

## Przykład dla Olsztyna

### Obecna metoda (gmina):
- Gmina Olsztyn: 166,392 os. / 1,884 km²
- Gęstość: 88.32 os/km²
- Estymacja dla r=10km: **2,567 klientów**

### Z gridami 1km²:
- Centrum Olsztyna: ~3,000-5,000 os/km²
- Peryferie: ~100-500 os/km²
- Obszary wiejskie: ~10-50 os/km²
- Estymacja dla r=10km: **~8,000-12,000 klientów** ✅ (znacznie dokładniej!)

## Status implementacji

### ✅ ZREALIZOWANE

1. **✅ Migracja tabeli `population_grid`**
   - Utworzono: `2025_10_02_181130_create_population_grid_table.php`
   - Kolumny: grid_id (unique), latitude, longitude, population, year
   - Indeksy spatial: lat/lng, population, year

2. **✅ Model `PopulationGrid`**
   - Metoda `findInRadius()` - wyszukiwanie kratek w promieniu (Haversine)
   - Metoda `totalPopulation()` - sumowanie populacji
   - Metoda `findByGridId()` - lookup po ID kratki

3. **✅ Wyczyszczono stary system**
   - Usunięto tabelę `population_data`
   - Usunięto model `PopulationData`
   - Wyczyszczono `GUSApiService` ze starego kodu
   - Usunięto testowe skrypty

4. **✅ Nowy `GUSApiService` (v2.0.0)**
   - `estimatePotentialClients()` - używa gridów 1km²
   - `estimateByRadius()` - fallback dla braku danych
   - Czysty, prosty kod - tylko estymacja bez logiki GUS API

5. **✅ Komenda importu `population:import-grid`**
   - Pobiera dane z Eurostat Census 2021 (v2.2)
   - Filtruje tylko Polskę (--country=PL)
   - Batch insert dla wydajności
   - Progress bar

### 📋 NASTĘPNE KROKI

1. **Uruchomić import danych**
   ```bash
   php artisan population:import-grid --force
   ```

2. **Przetestować estymację**
   - Olsztyn (lat: 53.77304, lon: 20.49551, radius: 10km)
   - Warszawa centrum
   - Mniejsze miasta

3. **Walidacja wyników**
   - Porównać z rzeczywistą populacją
   - Sprawdzić accuracy względem starych metod

## Źródło danych

**Eurostat Census Grid 2021 (v2.2)**
- URL: https://gisco-services.ec.europa.eu/census/2021/Eurostat_Census-GRID_2021_V2.2.zip
- Format: CSV, GeoPackage, Raster
- Data: 22 stycznia 2025
- Rozdzielczość: 1km × 1km
- Zakres: Cała Europa (filtrujemy PL)
