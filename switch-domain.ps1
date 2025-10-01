# PowerShell skrypt do przełączania domen między local i ngrok
param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("local", "ngrok")]
    [string]$Domain,

    [string]$NgrokUrl = $null
)

Write-Host "🔧 Przełączanie domeny na: $Domain" -ForegroundColor Cyan

if ($Domain -eq "local") {
    # Przełącz na domenę lokalną
    (Get-Content .env) -replace '^USE_NGROK=.*', 'USE_NGROK=false' | Set-Content .env
    Write-Host "✅ Przełączono na domenę lokalną: http://pethelp.test" -ForegroundColor Green
} else {
    # Przełącz na ngrok
    if ($NgrokUrl) {
        (Get-Content .env) -replace '^NGROK_DOMAIN=.*', "NGROK_DOMAIN=$NgrokUrl" | Set-Content .env
        Write-Host "🔗 Zaktualizowano domenę ngrok na: $NgrokUrl" -ForegroundColor Yellow
    }

    (Get-Content .env) -replace '^USE_NGROK=.*', 'USE_NGROK=true' | Set-Content .env

    # Pobierz aktualną domenę ngrok z .env
    $currentNgrok = (Get-Content .env | Where-Object { $_ -match '^NGROK_DOMAIN=' }) -replace '^NGROK_DOMAIN=', ''
    Write-Host "✅ Przełączono na domenę ngrok: $currentNgrok" -ForegroundColor Green

    Write-Host "🔄 Przebudowuję assety..." -ForegroundColor Yellow
    npm run build
}

Write-Host "🧹 Czyszczę cache konfiguracji..." -ForegroundColor Yellow
php artisan config:clear

Write-Host "🎉 Gotowe! Aplikacja została przełączona." -ForegroundColor Green

# Wyświetl aktualną konfigurację
Write-Host "`n📋 Aktualna konfiguracja:" -ForegroundColor Cyan
$useNgrok = (Get-Content .env | Where-Object { $_ -match '^USE_NGROK=' }) -replace '^USE_NGROK=', ''
$localDomain = (Get-Content .env | Where-Object { $_ -match '^LOCAL_DOMAIN=' }) -replace '^LOCAL_DOMAIN=', ''
$ngrokDomain = (Get-Content .env | Where-Object { $_ -match '^NGROK_DOMAIN=' }) -replace '^NGROK_DOMAIN=', ''

Write-Host "   USE_NGROK: $useNgrok" -ForegroundColor White
Write-Host "   LOCAL_DOMAIN: $localDomain" -ForegroundColor White
Write-Host "   NGROK_DOMAIN: $ngrokDomain" -ForegroundColor White

if ($useNgrok -eq "true") {
    Write-Host "   🌐 Aktywna domena: $ngrokDomain" -ForegroundColor Green
} else {
    Write-Host "   🏠 Aktywna domena: $localDomain" -ForegroundColor Green
}