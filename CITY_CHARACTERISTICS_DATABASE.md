# Baza charakterystyk miast - dokumentacja

## Wprowadzenie

Plik konfiguracyjny `config/city_characteristics.php` zawiera rozszerzoną bazę charakterystyk miast w Polsce używaną przez system estymacji populacji.

## Statystyki bazy danych

### Miasta uniwersyteckie
- **Liczba miast**: 80+
- **Kategorie**:
  - Duże ośrodki akademickie (>100k studentów): 8 miast
  - Średnie ośrodki (30-100k studentów): 12 miast
  - Mniejsze ośrodki (uczelnie wyższe, akademie): 40+ miast
  - Miasta z wyższymi szkołami zawodowymi: 20+ miast

### Destynacje turystyczne
- **Liczba lokalizacji**: 150+
- **Kategorie**:
  - Miasta historyczne i kulturowe: 20 miast
  - Kurorty nadmorskie (Bałtyk): 20+ miejscowości
  - Kurorty górskie: 25+ miejscowości
  - Miejsca pielgrzymkowe: 12 lokalizacji
  - Uzdrowiska i miasta spa: 20+ miejscowości
  - Regiony jezior (Mazury, Pojezierze): 12+ miejsc
  - Miejsca pamięci narodowej: 8 lokalizacji

### Centra dojazdowe
- **Liczba miast**: 70+
- **Kategorie**:
  - Stolice województw: 18 miast
  - Duże miasta >100k: 22 miasta
  - Ważne centra przemysłowe i biznesowe: 30+ miast

### Aglomeracje miejskie
- **Liczba aglomeracji**: 9
- **Łączna liczba miast w aglomeracjach**: 60+

## Pokrycie geograficzne

### Województwa z największym pokryciem

| Województwo | Miasta uniwersyteckie | Destynacje turystyczne | Centra dojazdowe |
|-------------|----------------------|------------------------|------------------|
| **Śląskie** | 12 | 8 | 20 |
| **Małopolskie** | 6 | 35+ | 8 |
| **Mazowieckie** | 8 | 12 | 10 |
| **Dolnośląskie** | 6 | 20+ | 8 |
| **Pomorskie** | 4 | 25+ | 6 |
| **Wielkopolskie** | 5 | 8 | 8 |
| **Warmińsko-Mazurskie** | 3 | 15+ | 4 |

## Przykłady wykrywania kontekstu

### 1. Zakopane (kurort górski)
```
Populacja: ~46,000
Kategoria: X2 (średnie miasto)
✓ Destynacja turystyczna (K_tourism = 0.03)
✗ Uniwersytet
✗ Centrum dojazdowe

Współczynnik korekty: ~1.08
```

### 2. Sopot (kurort nadmorski w Trójmieście)
```
Populacja: ~120,000
Kategoria: X3 (duże miasto)
✓ Destynacja turystyczna (K_tourism = 0.05)
✓ Uniwersytet (K_students = 0.06)
✓ Centrum dojazdowe (K_commuters = 0.10)

Współczynnik korekty: ~1.41
```

### 3. Częstochowa (miejsce pielgrzymkowe)
```
Populacja: ~235,000
Kategoria: X3 (duże miasto)
✓ Destynacja turystyczna (K_tourism = 0.05)
✓ Uniwersytet (K_students = 0.06)
✓ Centrum dojazdowe (K_commuters = 0.10)

Współczynnik korekty: ~1.41
```

### 4. Gliwice (przemysłowe + uniwersytet)
```
Populacja: ~318,000
Kategoria: X3 (duże miasto)
✗ Destynacja turystyczna
✓ Uniwersytet (K_students = 0.06)
✓ Centrum dojazdowe (K_commuters = 0.10)

Współczynnik korekty: ~1.35
```

### 5. Lublin (uniwersyteckie centrum)
```
Populacja: ~413,000
Kategoria: X3 (duże miasto)
✓ Destynacja turystyczna (K_tourism = 0.05)
✓ Uniwersytet (K_students = 0.06)
✓ Centrum dojazdowe (K_commuters = 0.10)

Współczynnik korekty: ~1.41
```

## Zarządzanie listami

### Dodawanie nowych miast

Edytuj plik `config/city_characteristics.php` i dodaj nazwę miasta (małe litery) do odpowiedniej tablicy:

```php
'university_cities' => [
    // ... istniejące miasta
    'nowe miasto',  // Dodaj tutaj
],
```

### Usuwanie miast

Usuń nazwę miasta z odpowiedniej tablicy w pliku konfiguracyjnym.

### Zmiana kategorii

Przenieś nazwę miasta z jednej tablicy do drugiej.

## Kalibracja i walidacja

### Weryfikacja poprawności list

```bash
php artisan tinker --execute="
\$config = config('city_characteristics');
echo 'Miasta uniwersyteckie: ' . count(\$config['university_cities']) . \"\n\";
echo 'Destynacje turystyczne: ' . count(\$config['tourist_destinations']) . \"\n\";
echo 'Centra dojazdowe: ' . count(\$config['commuter_hubs']) . \"\n\";
"
```

### Test dla konkretnego miasta

```bash
php artisan tinker --execute="
\$gus = app(App\Services\GUSApiService::class);
\$clients = \$gus->estimatePotentialClients(LAT, LNG, RADIUS);
echo \"Potencjalni klienci: \$clients\n\";
"
```

## Źródła danych

### Miasta uniwersyteckie
- **POL-on** - Zintegrowany System Informacji o Szkolnictwie Wyższym i Nauce
- Lista uczelni publicznych i niepublicznych MNiSW
- Własne badania lokalne

### Destynacje turystyczne
- **GUS** - Turystyka w Polsce (raport roczny)
- **POT** - Polska Organizacja Turystyczna
- Ranking miejsc turystycznych Ministerstwa Sportu i Turystyki
- Dane o noclegach turystycznych (GUS)

### Centra dojazdowe
- **GUS** - Dojazdy do pracy (NSP 2021)
- Dane o aglomeracjach miejskich
- Statystyki komunikacji miejskiej
- Analiza gęstości miejsc pracy

## Planowane rozszerzenia

### Wersja 2.0 (Q1 2026)
- [ ] Integracja z API OpenStreetMap dla dynamicznego wykrywania uczelni
- [ ] Automatyczna aktualizacja list z GUS API
- [ ] Współczynniki sezonowe dla destynacji turystycznych
- [ ] Dane o parkach i terenach zielonych

### Wersja 2.1 (Q2 2026)
- [ ] Współczynniki godzinowe dla centrów dojazdowych
- [ ] Integracja z danymi o wydarzeniach (festiwale, koncerty)
- [ ] Predykcja ruchu turystycznego na podstawie historii

## Konserwacja

### Częstotliwość aktualizacji
- **Miasta uniwersyteckie**: raz na rok (wrzesień)
- **Destynacje turystyczne**: raz na pół roku (sezon letni/zimowy)
- **Centra dojazdowe**: raz na 2 lata (po nowym spisie)

### Odpowiedzialność
- System Administrator: aktualizacja list po zmianach w bazie GUS
- DevOps: monitorowanie accuracy współczynników
- Product Manager: decyzje o dodawaniu nowych kategorii

## Changelog

### 2025-10-03 - v1.0
- ✅ Utworzenie rozszerzonej bazy charakterystyk miast
- ✅ 80+ miast uniwersyteckich
- ✅ 150+ destynacji turystycznych
- ✅ 70+ centrów dojazdowych
- ✅ 9 aglomeracji miejskich
- ✅ Integracja z GUSApiService

---

**Ostatnia aktualizacja**: 2025-10-03
**Wersja**: 1.0
**Autor**: Claude AI Assistant
