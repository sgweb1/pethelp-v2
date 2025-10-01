# 🗺️ Lokalny Nominatim dla PetHelp

Kompletna dokumentacja instalacji i konfiguracji lokalnego Nominatim z danymi Polski.

## 📋 WYMAGANIA SYSTEMOWE

### Minimalne wymagania:
- **RAM**: 8GB (zalecane: 16GB)
- **Dysk**: 20GB wolnego miejsca
- **CPU**: 4 rdzenie (zalecane: 8+ rdzeni)
- **Docker Desktop**: Najnowsza wersja
- **Windows**: 10/11 z WSL2

### Zalecane wymagania dla produkcji:
- **RAM**: 32GB
- **Dysk**: SSD 50GB+
- **CPU**: 8+ rdzeni
- **Network**: Stabilne łącze (podczas importu)

## 🚀 INSTALACJA KROK PO KROKU

### 1. Przygotowanie środowiska

```powershell
# Sprawdź Docker Desktop
docker --version
docker-compose --version

# Sprawdź dostępną pamięć RAM
Get-WmiObject -Class Win32_ComputerSystem | Select-Object TotalPhysicalMemory

# Sprawdź wolne miejsce na dysku
Get-WmiObject -Class Win32_LogicalDisk -Filter "DeviceID='C:'" | Select-Object FreeSpace
```

### 2. Uruchomienie setup script

```powershell
# Przejdź do katalogu projektu
cd C:\laragon\www\pethelp

# Uruchom setup (jako Administrator)
.\scripts\setup-local-nominatim.ps1
```

### 3. Monitorowanie procesu importu

```powershell
# Sprawdź logi importu
docker logs -f pethelp-nominatim

# Sprawdź status wszystkich kontenerów
.\scripts\nominatim-control.ps1 -Action status
```

## ⏱️ TIMELINE IMPORTU

### Etapy importu danych Polski:

1. **Download OSM data** (15-30 min)
   - Pobieranie pliku poland-latest.osm.pbf (~2GB)
   - Wyświetlane jako: "Downloading OSM data..."

2. **Database preparation** (30-60 min)
   - Tworzenie tabel i indeksów
   - Wyświetlane jako: "Setting up database..."

3. **Data import** (60-120 min)
   - Import węzłów, dróg i relacji
   - Wyświetlane jako: "Importing OSM data..."

4. **Indexing** (30-60 min)
   - Tworzenie indeksów wyszukiwania
   - Wyświetlane jako: "Creating indexes..."

5. **Finalization** (10-15 min)
   - Optymalizacja bazy danych
   - Wyświetlane jako: "Finalizing setup..."

**Total**: 2-4 godziny (zależnie od sprzętu)

## 🔧 KONFIGURACJA

### Environment Variables (.env)

```env
# Local Nominatim Configuration
NOMINATIM_LOCAL_ENABLED=false          # Ustaw true po zakończeniu importu
NOMINATIM_LOCAL_URL=http://localhost:8080
NOMINATIM_FALLBACK_ENABLED=true        # Fallback do zewnętrznego API
NOMINATIM_CACHE_TTL=86400              # 24h cache
NOMINATIM_RATE_LIMIT_DELAY=100         # 100ms opóźnienie (szybsze dla lokalnego)
```

### Przełączanie źródeł

```powershell
# Przełącz na lokalny Nominatim
.\scripts\nominatim-control.ps1 -Action local
php artisan config:clear

# Przełącz na zewnętrzny Nominatim
.\scripts\nominatim-control.ps1 -Action external
php artisan config:clear
```

## 🧪 TESTOWANIE

### Test podstawowy

```powershell
# Test wszystkich API endpoints
.\scripts\nominatim-control.ps1 -Action test

# Test manualny przez cURL
curl "http://localhost:8080/search?q=Warszawa&format=json&limit=1"
curl "http://localhost:8080/reverse?lat=52.2297&lon=21.0122&format=json"
```

### Test aplikacji PetHelp

```powershell
# Test przez Laravel API
curl -X POST http://pethelp.test/api/location/search \
  -H "Content-Type: application/json" \
  -d '{"query": "Warszawa Mokotów", "limit": 5}'

# Test status
curl http://pethelp.test/api/location/status
```

## 📊 MONITORING I PERFORMANCE

### Sprawdzanie zasobów

```powershell
# Status kontenerów
docker stats pethelp-nominatim-db pethelp-nominatim pethelp-nominatim-redis

# Wykorzystanie dysku
docker system df

# Logi performance
docker logs pethelp-nominatim | grep -i "time\|performance\|slow"
```

### Metryki wydajności

- **Średni czas wyszukiwania**: 50-200ms (lokalny) vs 1000-3000ms (zewnętrzny)
- **Rate limiting**: Brak (lokalny) vs 1 req/sec (zewnętrzny)
- **Dostępność**: 99.9% (lokalny) vs 95% (zależnie od internetu)

## 🔄 AKTUALIZACJA DANYCH

### Automatyczna aktualizacja (rekomendowane)

Nominatim może automatycznie pobierać updates z OSM:

```yaml
# W docker-compose.nominatim.yml
environment:
  REPLICATION_URL: https://download.geofabrik.de/europe/poland-updates/
```

### Manualna aktualizacja

```powershell
# Aktualizuj dane OSM (2-4h proces)
.\scripts\nominatim-control.ps1 -Action update

# Monitoruj proces
docker logs -f pethelp-nominatim
```

## 🗄️ BACKUP I RESTORE

### Backup bazy danych

```powershell
# Utwórz backup
docker exec pethelp-nominatim-db pg_dump -U nominatim nominatim > nominatim_backup_$(date +%Y%m%d).sql

# Kompresja
gzip nominatim_backup_$(date +%Y%m%d).sql
```

### Restore z backup

```powershell
# Zatrzymaj Nominatim
.\scripts\nominatim-control.ps1 -Action stop

# Restore bazy
gunzip -c nominatim_backup_YYYYMMDD.sql.gz | docker exec -i pethelp-nominatim-db psql -U nominatim -d nominatim

# Uruchom ponownie
.\scripts\nominatim-control.ps1 -Action start
```

## 🔧 TROUBLESHOOTING

### Problemy podczas importu

**Problem**: Brak miejsca na dysku
```powershell
# Rozwiązanie: Zwolnij miejsce lub przenieś docker data
docker system prune -a
```

**Problem**: Import się zawiesza
```powershell
# Sprawdź logi
docker logs pethelp-nominatim

# Restart kontenera
docker restart pethelp-nominatim
```

**Problem**: Out of memory
```powershell
# Zwiększ pamięć dla Docker Desktop
# Settings > Resources > Memory > 8GB+
```

### Problemy z API

**Problem**: API nie odpowiada
```powershell
# Sprawdź status
.\scripts\nominatim-control.ps1 -Action status

# Restart
.\scripts\nominatim-control.ps1 -Action restart
```

**Problem**: Wolne zapytania
```powershell
# Sprawdź indeksy w bazie
docker exec pethelp-nominatim-db psql -U nominatim -d nominatim -c "\di"

# Optymalizuj bazę
docker exec pethelp-nominatim-db psql -U nominatim -d nominatim -c "VACUUM ANALYZE;"
```

## 📈 OPTYMALIZACJA PERFORMANCE

### Tuning PostgreSQL

```sql
-- Wykonaj w kontenerze bazy danych
-- Zwiększ memory settings dla lepszej wydajności
ALTER SYSTEM SET shared_buffers = '4GB';
ALTER SYSTEM SET effective_cache_size = '12GB';
ALTER SYSTEM SET work_mem = '128MB';
SELECT pg_reload_conf();
```

### Tuning aplikacji Laravel

```php
// W config/app.php - zwiększ cache TTL dla lokalnego
'nominatim_cache_ttl' => env('NOMINATIM_CACHE_TTL', 604800), // 7 dni
'nominatim_rate_limit_delay' => env('NOMINATIM_RATE_LIMIT_DELAY', 0), // Brak opóźnień
```

## 🔐 SECURITY CONSIDERATIONS

### Firewall
```powershell
# Ogranicz dostęp do portów Nominatim tylko do localhost
# Port 8080 (Nominatim API) - tylko localhost
# Port 5432 (PostgreSQL) - tylko localhost
# Port 6379 (Redis) - tylko localhost
```

### Updates
```powershell
# Regularnie aktualizuj obrazy Docker
docker-compose -f docker-compose.nominatim.yml pull
.\scripts\nominatim-control.ps1 -Action restart
```

## 📞 WSPARCIE

### Przydatne komendy

```powershell
# Pełny status systemu
.\scripts\nominatim-control.ps1 -Action status

# Test wydajności
.\scripts\nominatim-control.ps1 -Action test

# Pełny restart z czyszczeniem cache
.\scripts\nominatim-control.ps1 -Action restart
php artisan config:clear
php artisan cache:clear
```

### Logi do debugowania

```powershell
# Laravel logs
tail -f storage/logs/laravel.log

# Nominatim logs
docker logs -f pethelp-nominatim

# Database logs
docker logs -f pethelp-nominatim-db
```

### Performance benchmarking

```powershell
# Benchmark search queries
time curl "http://localhost:8080/search?q=Kraków&format=json"
time curl "http://localhost:8080/search?q=Warszawa Mokotów&format=json"
time curl "http://localhost:8080/reverse?lat=50.0614&lon=19.9366&format=json"
```

---

## 🎯 MIGRATION STRATEGY

### Etap 1: Setup & Test (1-2 dni)
1. Zainstaluj Docker + komponenty
2. Uruchom import danych Polski
3. Przetestuj API locally

### Etap 2: Integration (1 dzień)
1. Zmień `NOMINATIM_LOCAL_ENABLED=true`
2. Przetestuj aplikację PetHelp
3. Monitoruj performance

### Etap 3: Production (ongoing)
1. Ustaw automatic updates
2. Monitoring i alerting
3. Regular backups

**Wynikowy czas przejścia**: 2-4 dni (w tym czas importu)