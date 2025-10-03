# Współczynniki korekcyjne dla estymacji populacji

## Wprowadzenie

System estymacji rzeczywistej liczby osób przebywających w obszarze na podstawie danych z siatki populacyjnej Eurostat 2021 (1km²).

**Źródło danych grid**: https://ec.europa.eu/eurostat/web/gisco/geodata/population-distribution/population-grids

## 1. Kategoryzacja miejscowości

Miasta/gminy dzielimy według liczby zameldowanych mieszkańców:

| Kategoria | Zakres (zameldowani) | Opis |
|-----------|---------------------|------|
| **X0** | < 5 000 | Wieś / bardzo mała miejscowość |
| **X1** | 5 000 – 30 000 | Małe miasto |
| **X2** | 30 000 – 100 000 | Średnie miasto |
| **X3** | 100 000 – 500 000 | Duże miasto |
| **X4** | > 500 000 | Metropolia / duże miasto aglomeracyjne |

## 2. Podstawowe współczynniki korekty (k_base)

**Interpretacja**: `k × (sumaryczna populacja z gridu w promieniu R)` = skorygowana rzeczywista liczba osób

| Kategoria | Zakres | k_base | Uzasadnienie |
|-----------|--------|--------|--------------|
| **X0** (wieś) | < 5k | **1.00** | Praktycznie brak ruchu, meldunki ≈ rzeczywistość |
| **X1** (małe) | 5k–30k | **1.02** | Lekki ruch dojezdny / zakupy |
| **X2** (średnie) | 30k–100k | **1.05** | Większy ruch dzienny, studenci, usługi |
| **X3** (duże) | 100k–500k | **1.10 – 1.25** | Znaczny ruch dojazdowy, pracownicy, turyści |
| **X4** (metropolia) | > 500k | **1.20 – 1.50** | Aglomeracje + ruch międzygminny, turyści, studenci |

### Wybór wartości dla X3 i X4

- **Centrum turystyczne/uniwersyteckie** → wyższy mnożnik
- **Przemysłowo-biurowe** → wyższy mnożnik
- **Sypialniane/monotonne** → niższy mnożnik

## 3. Dodatkowe korekty (K_*)

Oprócz podstawowego współczynnika `k_base`, stosujemy dodatkowe korekty:

### 3.1 Studenci (K_students)

```
Warunek: miasto ma uczelnie
Wartość: +3–10% w zależności od skali

K_students = min(0.10, liczba_studentów / zameldowani)
```

**Przykład**:
- Olsztyn: ~10 000 studentów, 166 000 zameldowanych
- K_students = min(0.10, 10000/166000) ≈ 0.06 (6%)

### 3.2 Turystyka sezonowa (K_tourism)

```
Warunek: miasto turystyczne
Wartość: +5–50% w zależności od sezonu i lokalizacji

K_tourism = f(sezon, tourist_index)
```

**Wskaźnik turystyczny**:
```
tourist_index = noclegów_turystycznych_rocznie / zameldowani
```

**Progi**:
- `tourist_index < 0.5` → K_tourism = 0.02 (2%)
- `tourist_index 0.5–2.0` → K_tourism = 0.05–0.15 (5-15%)
- `tourist_index > 2.0` → K_tourism = 0.20+ (20%+)

### 3.3 Ruch dojazdowy (K_commuters)

```
Warunek: miasto centrum pracy regionu
Wartość: +5–20% w godzinach pracy

K_commuters = f(godzina_dnia, commuter_ratio)
```

**Wskaźnik dojazdowości**:
```
commuter_ratio = liczba_miejsc_pracy / zameldowani
```

**Progi**:
- `commuter_ratio < 0.8` → K_commuters = 0.00 (miasto sypialniane)
- `commuter_ratio 0.8–1.2` → K_commuters = 0.05 (5%)
- `commuter_ratio > 1.2` → K_commuters = 0.10–0.20 (10-20%)

### 3.4 Gęstość zabudowy (K_buildings)

```
Warunek: wysoka gęstość zabudowy w obszarze
Wartość: +5–10%

building_density_ratio = buildings_in_radius / mean_buildings_per_km2_in_gmina

K_buildings = (building_density_ratio - 1.0) * 0.05
K_buildings = max(0.00, min(0.10, K_buildings))
```

## 4. Wzory końcowe

### Model multiplikatywny (zalecany)

```
G = sum(grid_cells_population_in_radius)
k_base = f(city_size)

adjusted_population = G × k_base × (1 + K_students + K_tourism + K_commuters + K_buildings)
```

### Model addytywny (alternatywny)

```
adjusted_population = G × k_base + absolute_adjustments

gdzie:
absolute_adjustments = estimated_students + estimated_tourists + ...
```

## 5. Reguły decyzyjne i progi przełączeń

### 5.1 Przełączanie na dane dzielnicowe

```
JEŚLI gmina > 100k AND promień R ≤ 5km:
    → użyj danych na poziomie dzielnicy/osiedla (jeśli dostępne)
    → NIE używaj średniej gminnej
```

### 5.2 Korekta dla centrów miejskich

```
urban_core_area_ratio = powierzchnia_o_gęstości_>_threshold / całkowita_powierzchnia

JEŚLI urban_core_area_ratio > 0.3:
    → k_base += 0.05 do 0.10 (zwiększ o 5-10%)
```

### 5.3 Bonus uniwersytecki

```
JEŚLI miasto_ma_uczelnię AND studentów > 5000:
    student_bonus = min(0.10, studentów / zameldowani)
    → maksymalnie +10%
```

### 5.4 Korekta turystyczna

```
JEŚLI tourist_index > threshold:
    → zastosuj sezonową korektę season_factor(date, place)
```

## 6. Przykład obliczeniowy: Olsztyn

### Dane wejściowe

- **Zameldowani**: 166 000
- **Kategoria**: X3 (duże miasto)
- **k_base**: 1.12 (wybrane z zakresu 1.10–1.25)
- **Studenci**: ~10 000
- **Charakter**: miasto uniwersyteckie, umiarkowana turystyka

### Obliczenia dodatkowych korekt

```
K_students = min(0.10, 10000/166000) ≈ 0.06 (6%)
K_tourism = 0.02 (2%, turystyka umiarkowana)
K_commuters = 0.03 (3%, centrum regionu)
K_buildings = 0.00 (brak szczegółowych danych)

Łącznie: multiplicative_factor = 1.12 × (1 + 0.06 + 0.02 + 0.03)
                                = 1.12 × 1.11
                                = 1.2432
```

### Wynik

```
G (suma z gridu w promieniu 10 km) = 140 000

adjusted_population = 140 000 × 1.2432 ≈ 174 048

Dla porównania:
- Zameldowani: 166 000
- Grid bez korekty: 140 000
- Grid z korektą: 174 048
```

Wynik uwzględnia studentów i ruch dzienny, dając realniejszy obraz liczby osób przebywających w obszarze.

## 7. Kalibracja i walidacja

### 7.1 Zbieranie danych ground-truth

Dla kalibracji zbierz dane z:
- Dane z miast/BDL (Bank Danych Lokalnych GUS)
- Liczba noclegów turystycznych (GUS)
- Dane z operatorów telefonii (jeśli dostępne)
- Dane z liczników ruchu miejskiego
- Statystyki komunikacji miejskiej

### 7.2 Proces kalibracji

```python
# Dla każdego testowego obszaru
for test_area in test_areas:
    ratio = real_people_count / grid_sum

# Dopasuj k_base i K_* przez regresję
# Np. wieloraka regresja liniowa:
adjusted = k_base × G × (1 + β₁×students_ratio + β₂×tourist_index + ...)

# Grid search dla optymalnych wartości
```

### 7.3 Aktualizacja parametrów

- Zapisz parametry jako konfigurację w bazie danych
- Aktualizuj **kwartalnie** na podstawie nowych danych
- Monitoruj accuracy metrics (MAE, RMSE)

## 8. Implementacja w systemie

### 8.1 Tabela konfiguracji w DB

```sql
CREATE TABLE population_coefficients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(10),           -- X0, X1, X2, X3, X4
    population_min INT,             -- dolny próg
    population_max INT,             -- górny próg
    k_base_min DECIMAL(4,2),        -- min współczynnik
    k_base_max DECIMAL(4,2),        -- max współczynnik
    k_students DECIMAL(4,2),
    k_tourism DECIMAL(4,2),
    k_commuters DECIMAL(4,2),
    k_buildings DECIMAL(4,2),
    updated_at TIMESTAMP
);
```

### 8.2 Funkcja estymacji (pseudokod)

```php
function estimateRealPopulation(
    float $lat,
    float $lng,
    float $radiusKm
): int {
    // 1. Pobierz sumę z gridu
    $G = $this->getGridPopulationSum($lat, $lng, $radiusKm);

    // 2. Określ kategorię miasta
    $citySize = $this->getCitySize($lat, $lng);
    $category = $this->determineCategory($citySize);

    // 3. Pobierz współczynniki dla kategorii
    $coefficients = $this->getCoefficients($category);
    $k_base = $this->selectKBase($coefficients, $cityContext);

    // 4. Oblicz dodatkowe korekty
    $K_students = $this->calculateStudentCorrection($citySize);
    $K_tourism = $this->calculateTourismCorrection($location, $season);
    $K_commuters = $this->calculateCommuterCorrection($location);
    $K_buildings = $this->calculateBuildingDensityCorrection($area);

    // 5. Zastosuj wzór
    $multiplicative = 1 + $K_students + $K_tourism + $K_commuters + $K_buildings;
    $adjusted = $G * $k_base * $multiplicative;

    return round($adjusted);
}
```

## 9. Metryki jakości

### 9.1 Wskaźniki do monitorowania

```
MAE (Mean Absolute Error) = średnia(|predicted - actual|)
RMSE (Root Mean Square Error) = sqrt(średnia((predicted - actual)²))
MAPE (Mean Absolute Percentage Error) = średnia(|predicted - actual| / actual) × 100%
```

### 9.2 Cele jakościowe

- **MAE < 5%** dla miast X2-X4
- **MAE < 10%** dla miast X0-X1
- **MAPE < 15%** ogólnie

## 10. Changelog współczynników

| Data | Wersja | Zmiany |
|------|--------|--------|
| 2025-10-03 | 1.0 | Inicjalna wersja współczynników |

---

## Notatki dodatkowe

### Sezonowość

Dla turystyki uwzględnij sezonowość:
- **Sezon letni** (VI-VIII): K_tourism × 1.5
- **Ferie zimowe** (I-II): K_tourism × 1.2
- **Reszta roku**: K_tourism × 1.0

### Godziny dnia

Dla ruchu dojazdowego:
- **08:00-16:00** (godz. pracy): K_commuters × 1.0
- **Wieczory/weekendy**: K_commuters × 0.3

### Dane Eurostat 2021

- Rozdzielczość: **1 km²**
- Projekcja: ETRS89 Lambert Azimuthal Equal Area
- Zmienne: płeć, wiek, zatrudnienie, miejsce urodzenia
- Format: GeoPackage (GPKG), GeoTIFF, GeoParquet, CSV
