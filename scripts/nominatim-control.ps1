# Nominatim Control Script dla PetHelp
# Uruchom w PowerShell

param(
    [string]$Action,
    [switch]$Force,
    [switch]$Verbose
)

# Funkcje pomocnicze
function Write-Status {
    param([string]$Message, [string]$Level = "INFO")

    $color = switch ($Level) {
        "ERROR" { "Red" }
        "WARNING" { "Yellow" }
        "SUCCESS" { "Green" }
        default { "Cyan" }
    }

    Write-Host "[$Level] $Message" -ForegroundColor $color
}

function Get-ContainerStatus {
    param([string]$ContainerName)

    try {
        $status = docker ps -a --filter "name=$ContainerName" --format "{{.Status}}"
        return $status
    } catch {
        return "Not found"
    }
}

function Show-NominatimStatus {
    Write-Status "🔍 Sprawdzanie statusu kontenerów Nominatim..." "INFO"

    $containers = @("pethelp-nominatim-db", "pethelp-nominatim", "pethelp-nominatim-redis")

    foreach ($container in $containers) {
        $status = Get-ContainerStatus -ContainerName $container

        if ($status -match "Up") {
            Write-Status "✅ $container - $status" "SUCCESS"
        } elseif ($status -eq "Not found") {
            Write-Status "❌ $container - Nie znaleziono" "ERROR"
        } else {
            Write-Status "⚠️ $container - $status" "WARNING"
        }
    }

    # Test API endpoint
    Write-Status "🌐 Testowanie API endpoint..." "INFO"
    try {
        $response = Invoke-RestMethod -Uri "http://localhost:8080/status" -TimeoutSec 5
        Write-Status "✅ API Nominatim działa poprawnie" "SUCCESS"
    } catch {
        Write-Status "❌ API Nominatim nie odpowiada: $($_.Exception.Message)" "ERROR"
    }
}

function Start-Nominatim {
    Write-Status "🚀 Uruchamianie kontenerów Nominatim..." "INFO"

    try {
        docker-compose -f docker-compose.nominatim.yml up -d

        Write-Status "⏳ Czekanie na inicjalizację (30 sekund)..." "INFO"
        Start-Sleep -Seconds 30

        Show-NominatimStatus
        Write-Status "✅ Kontenery uruchomione" "SUCCESS"
    } catch {
        Write-Status "❌ Błąd podczas uruchamiania: $($_.Exception.Message)" "ERROR"
    }
}

function Stop-Nominatim {
    Write-Status "⏹️ Zatrzymywanie kontenerów Nominatim..." "INFO"

    try {
        docker-compose -f docker-compose.nominatim.yml stop
        Write-Status "✅ Kontenery zatrzymane" "SUCCESS"
    } catch {
        Write-Status "❌ Błąd podczas zatrzymywania: $($_.Exception.Message)" "ERROR"
    }
}

function Restart-Nominatim {
    Write-Status "🔄 Restart kontenerów Nominatim..." "INFO"

    Stop-Nominatim
    Start-Sleep -Seconds 5
    Start-Nominatim
}

function Show-Logs {
    param([string]$Container = "nominatim")

    $containerName = switch ($Container) {
        "db" { "pethelp-nominatim-db" }
        "redis" { "pethelp-nominatim-redis" }
        default { "pethelp-nominatim" }
    }

    Write-Status "📋 Wyświetlanie logów kontenera $containerName..." "INFO"

    try {
        docker logs -f $containerName
    } catch {
        Write-Status "❌ Błąd podczas pobierania logów: $($_.Exception.Message)" "ERROR"
    }
}

function Update-Data {
    Write-Status "📦 Aktualizacja danych OSM Polski..." "INFO"

    if (-not $Force) {
        $confirm = Read-Host "Czy na pewno chcesz zaktualizować dane? (y/n)"
        if ($confirm -ne 'y') {
            Write-Status "Anulowano aktualizację" "WARNING"
            return
        }
    }

    try {
        # Zatrzymaj Nominatim (pozostaw DB)
        docker stop pethelp-nominatim

        # Usuń kontener Nominatim
        docker rm pethelp-nominatim

        # Uruchom ponownie z aktualizacją danych
        docker-compose -f docker-compose.nominatim.yml up -d nominatim

        Write-Status "✅ Rozpoczęto aktualizację danych" "SUCCESS"
        Write-Status "⏳ Import może potrwać 2-4 godziny. Sprawdź logi: docker logs -f pethelp-nominatim" "INFO"
    } catch {
        Write-Status "❌ Błąd podczas aktualizacji: $($_.Exception.Message)" "ERROR"
    }
}

function Remove-All {
    Write-Status "🗑️ Usuwanie wszystkich kontenerów i danych Nominatim..." "WARNING"

    if (-not $Force) {
        $confirm = Read-Host "⚠️ UWAGA: To usunie WSZYSTKIE dane Nominatim. Czy kontynuować? (y/n)"
        if ($confirm -ne 'y') {
            Write-Status "Anulowano usuwanie" "WARNING"
            return
        }
    }

    try {
        docker-compose -f docker-compose.nominatim.yml down -v
        Write-Status "✅ Wszystkie kontenery i wolumeny usunięte" "SUCCESS"
    } catch {
        Write-Status "❌ Błąd podczas usuwania: $($_.Exception.Message)" "ERROR"
    }
}

function Test-API {
    Write-Status "🧪 Testowanie API Nominatim..." "INFO"

    # Test search
    try {
        Write-Status "Testowanie wyszukiwania: Warszawa..." "INFO"
        $searchResponse = Invoke-RestMethod -Uri "http://localhost:8080/search?q=Warszawa&format=json&limit=1" -TimeoutSec 10

        if ($searchResponse.Count -gt 0) {
            Write-Status "✅ Search API działa - znaleziono $($searchResponse.Count) wyników" "SUCCESS"
        } else {
            Write-Status "⚠️ Search API nie zwrócił wyników" "WARNING"
        }
    } catch {
        Write-Status "❌ Search API test failed: $($_.Exception.Message)" "ERROR"
    }

    # Test reverse
    try {
        Write-Status "Testowanie reverse geocoding: 52.2297, 21.0122..." "INFO"
        $reverseResponse = Invoke-RestMethod -Uri "http://localhost:8080/reverse?lat=52.2297&lon=21.0122&format=json" -TimeoutSec 10

        if ($reverseResponse.display_name) {
            Write-Status "✅ Reverse API działa - znaleziono: $($reverseResponse.display_name)" "SUCCESS"
        } else {
            Write-Status "⚠️ Reverse API nie zwrócił wyniku" "WARNING"
        }
    } catch {
        Write-Status "❌ Reverse API test failed: $($_.Exception.Message)" "ERROR"
    }
}

function Switch-ToLocal {
    Write-Status "🔄 Przełączanie na lokalny Nominatim..." "INFO"

    $envFile = ".env"
    if (Test-Path $envFile) {
        # Backup .env
        Copy-Item $envFile "$envFile.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"

        # Update config
        $envContent = Get-Content $envFile
        $envContent = $envContent -replace "NOMINATIM_LOCAL_ENABLED=false", "NOMINATIM_LOCAL_ENABLED=true"
        $envContent | Set-Content $envFile

        Write-Status "✅ Konfiguracja zaktualizowana" "SUCCESS"
        Write-Status "🔄 Uruchom 'php artisan config:clear' aby zastosować zmiany" "INFO"
    } else {
        Write-Status "❌ Plik .env nie znaleziony" "ERROR"
    }
}

function Switch-ToExternal {
    Write-Status "🔄 Przełączanie na zewnętrzny Nominatim..." "INFO"

    $envFile = ".env"
    if (Test-Path $envFile) {
        # Backup .env
        Copy-Item $envFile "$envFile.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"

        # Update config
        $envContent = Get-Content $envFile
        $envContent = $envContent -replace "NOMINATIM_LOCAL_ENABLED=true", "NOMINATIM_LOCAL_ENABLED=false"
        $envContent | Set-Content $envFile

        Write-Status "✅ Konfiguracja zaktualizowana" "SUCCESS"
        Write-Status "🔄 Uruchom 'php artisan config:clear' aby zastosować zmiany" "INFO"
    } else {
        Write-Status "❌ Plik .env nie znaleziony" "ERROR"
    }
}

# Główna logika
switch ($Action.ToLower()) {
    "status" { Show-NominatimStatus }
    "start" { Start-Nominatim }
    "stop" { Stop-Nominatim }
    "restart" { Restart-Nominatim }
    "logs" { Show-Logs }
    "logs-db" { Show-Logs -Container "db" }
    "logs-redis" { Show-Logs -Container "redis" }
    "update" { Update-Data }
    "remove" { Remove-All }
    "test" { Test-API }
    "local" { Switch-ToLocal }
    "external" { Switch-ToExternal }
    default {
        Write-Host @"
🗺️ Nominatim Control Script dla PetHelp

UŻYCIE:
  .\nominatim-control.ps1 -Action <action> [-Force] [-Verbose]

AKCJE:
  status      - Sprawdź status kontenerów i API
  start       - Uruchom wszystkie kontenery
  stop        - Zatrzymaj wszystkie kontenery
  restart     - Restart wszystkich kontenerów
  logs        - Pokaż logi Nominatim
  logs-db     - Pokaż logi bazy danych
  logs-redis  - Pokaż logi Redis
  update      - Zaktualizuj dane OSM Polski
  remove      - Usuń wszystkie kontenery i dane
  test        - Przetestuj API endpoints
  local       - Przełącz na lokalny Nominatim
  external    - Przełącz na zewnętrzny Nominatim

PRZYKŁADY:
  .\nominatim-control.ps1 -Action status
  .\nominatim-control.ps1 -Action start
  .\nominatim-control.ps1 -Action remove -Force
  .\nominatim-control.ps1 -Action test

FLAGI:
  -Force      - Nie pytaj o potwierdzenie
  -Verbose    - Wyświetl więcej informacji
"@
    }
}