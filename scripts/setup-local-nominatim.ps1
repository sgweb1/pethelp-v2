# Setup skrypt dla lokalnego Nominatim na Windows/Laragon
# Uruchom jako Administrator w PowerShell

param(
    [switch]$Force,
    [switch]$SkipDownload
)

Write-Host "🚀 PetHelp - Setup Lokalnego Nominatim" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green

# Sprawdź wymagania
Write-Host "📋 Sprawdzanie wymagań systemowych..." -ForegroundColor Yellow

# Sprawdź Docker Desktop
try {
    $dockerVersion = docker --version
    Write-Host "✅ Docker Desktop: $dockerVersion" -ForegroundColor Green
} catch {
    Write-Error "❌ Docker Desktop nie jest zainstalowany lub nie działa. Zainstaluj Docker Desktop z https://www.docker.com/products/docker-desktop"
    exit 1
}

# Sprawdź Docker Compose
try {
    $composeVersion = docker-compose --version
    Write-Host "✅ Docker Compose: $composeVersion" -ForegroundColor Green
} catch {
    Write-Error "❌ Docker Compose nie jest dostępny"
    exit 1
}

# Sprawdź miejsce na dysku (minimum 20GB)
$freeSpace = (Get-WmiObject -Class Win32_LogicalDisk -Filter "DeviceID='C:'").FreeSpace / 1GB
if ($freeSpace -lt 20) {
    Write-Warning "⚠️ Mało miejsca na dysku C: ($([math]::Round($freeSpace, 1))GB). Zalecane minimum: 20GB"
    if (-not $Force) {
        $continue = Read-Host "Kontynuować? (y/n)"
        if ($continue -ne 'y') { exit 1 }
    }
} else {
    Write-Host "✅ Dostępne miejsce na dysku: $([math]::Round($freeSpace, 1))GB" -ForegroundColor Green
}

# Przejdź do katalogu projektu
$projectDir = Split-Path -Parent $PSScriptRoot
Set-Location $projectDir

Write-Host "📂 Katalog projektu: $projectDir" -ForegroundColor Cyan

# Sprawdź czy kontenery już istnieją
$existingContainers = docker ps -a --filter "name=pethelp-nominatim" --format "{{.Names}}"
if ($existingContainers -and -not $Force) {
    Write-Warning "⚠️ Kontenery Nominatim już istnieją: $existingContainers"
    $recreate = Read-Host "Czy usunąć i utworzyć ponownie? (y/n)"
    if ($recreate -eq 'y') {
        Write-Host "🗑️ Usuwanie istniejących kontenerów..." -ForegroundColor Yellow
        docker-compose -f docker-compose.nominatim.yml down -v
    } else {
        Write-Host "✅ Kontynuacja z istniejącymi kontenerami" -ForegroundColor Green
    }
}

# Utwórz katalogi dla danych
$dataDir = Join-Path $projectDir "docker-data\nominatim"
if (-not (Test-Path $dataDir)) {
    New-Item -ItemType Directory -Path $dataDir -Force | Out-Null
    Write-Host "📁 Utworzono katalog danych: $dataDir" -ForegroundColor Green
}

# Pobierz i uruchom kontenery
Write-Host "🐳 Uruchamianie kontenerów Nominatim..." -ForegroundColor Yellow
Write-Host "⏰ UWAGA: Pierwszy import danych Polski może zająć 2-4 godziny!" -ForegroundColor Red

try {
    # Start PostgreSQL first
    Write-Host "📊 Uruchamianie bazy danych PostgreSQL..." -ForegroundColor Cyan
    docker-compose -f docker-compose.nominatim.yml up -d nominatim-db

    # Wait for database
    Write-Host "⏳ Czekanie na bazę danych (30 sekund)..." -ForegroundColor Cyan
    Start-Sleep -Seconds 30

    # Start Nominatim (this will trigger data import)
    Write-Host "🗺️ Uruchamianie Nominatim (import danych Polski rozpocznie się automatycznie)..." -ForegroundColor Cyan
    docker-compose -f docker-compose.nominatim.yml up -d nominatim

    # Start Redis cache
    Write-Host "💾 Uruchamianie Redis cache..." -ForegroundColor Cyan
    docker-compose -f docker-compose.nominatim.yml up -d nominatim-redis

    Write-Host "✅ Kontenery uruchomione pomyślnie!" -ForegroundColor Green

} catch {
    Write-Error "❌ Błąd podczas uruchamiania kontenerów: $($_.Exception.Message)"
    exit 1
}

# Monitorowanie procesu importu
Write-Host ""
Write-Host "📊 MONITOROWANIE IMPORTU DANYCH" -ForegroundColor Yellow
Write-Host "================================" -ForegroundColor Yellow
Write-Host "Import danych Polski do Nominatim jest w toku..." -ForegroundColor Cyan
Write-Host ""
Write-Host "Sprawdź status importu:" -ForegroundColor White
Write-Host "docker logs -f pethelp-nominatim" -ForegroundColor Gray
Write-Host ""
Write-Host "Sprawdź logi bazy danych:" -ForegroundColor White
Write-Host "docker logs -f pethelp-nominatim-db" -ForegroundColor Gray
Write-Host ""
Write-Host "Po zakończeniu importu, API będzie dostępne pod adresem:" -ForegroundColor White
Write-Host "http://localhost:8080" -ForegroundColor Green
Write-Host ""
Write-Host "Test API:" -ForegroundColor White
Write-Host "curl 'http://localhost:8080/search?q=Warszawa&format=json'" -ForegroundColor Gray

# Aktualizuj .env z konfiguracją lokalnego Nominatim
Write-Host ""
Write-Host "⚙️ Aktualizacja konfiguracji..." -ForegroundColor Yellow

$envFile = Join-Path $projectDir ".env"
if (Test-Path $envFile) {
    $envContent = Get-Content $envFile -Raw

    # Dodaj konfigurację Nominatim
    if (-not $envContent.Contains("NOMINATIM_LOCAL_ENABLED")) {
        $nominatimConfig = @"

# Local Nominatim Configuration
NOMINATIM_LOCAL_ENABLED=false
NOMINATIM_LOCAL_URL=http://localhost:8080
NOMINATIM_FALLBACK_ENABLED=true
NOMINATIM_CACHE_TTL=86400
NOMINATIM_RATE_LIMIT_DELAY=100

"@
        Add-Content -Path $envFile -Value $nominatimConfig
        Write-Host "✅ Dodano konfigurację Nominatim do .env" -ForegroundColor Green
    } else {
        Write-Host "ℹ️ Konfiguracja Nominatim już istnieje w .env" -ForegroundColor Cyan
    }
} else {
    Write-Warning "⚠️ Plik .env nie znaleziony"
}

Write-Host ""
Write-Host "🎯 NASTĘPNE KROKI:" -ForegroundColor Green
Write-Host "1. Poczekaj na zakończenie importu danych (sprawdź logi)" -ForegroundColor White
Write-Host "2. Przetestuj API: curl 'http://localhost:8080/search?q=Warszawa&format=json'" -ForegroundColor White
Write-Host "3. Zmień NOMINATIM_LOCAL_ENABLED=true w .env" -ForegroundColor White
Write-Host "4. Uruchom 'php artisan config:clear'" -ForegroundColor White
Write-Host "5. Przetestuj aplikację PetHelp" -ForegroundColor White
Write-Host ""
Write-Host "📚 Pomocne komendy:" -ForegroundColor Yellow
Write-Host "- docker-compose -f docker-compose.nominatim.yml logs -f nominatim" -ForegroundColor Gray
Write-Host "- docker-compose -f docker-compose.nominatim.yml stop" -ForegroundColor Gray
Write-Host "- docker-compose -f docker-compose.nominatim.yml start" -ForegroundColor Gray
Write-Host "- docker-compose -f docker-compose.nominatim.yml down" -ForegroundColor Gray

Write-Host ""
Write-Host "✅ Setup zakończony! Import danych w toku..." -ForegroundColor Green