@echo off
echo Przełączanie na domenę ngrok...

REM Sprawdza czy podano nową domenę ngrok jako parametr
if "%1"=="" (
    echo Używam domyślnej domeny ngrok z .env
) else (
    echo Aktualizuję domenę ngrok na: %1
    powershell -Command "(Get-Content .env) -replace '^NGROK_DOMAIN=.*', 'NGROK_DOMAIN=%1' | Set-Content .env"
)

REM Ustawia USE_NGROK=true w pliku .env
powershell -Command "(Get-Content .env) -replace '^USE_NGROK=.*', 'USE_NGROK=true' | Set-Content .env"

REM Wyczyść cache konfiguracji
php artisan config:clear

REM Rebuilds assets for the new domain
npm run build

echo Aplikacja teraz działa na domenie ngrok.
echo Cache konfiguracji został wyczyszczony.
echo Assety zostały przebudowane.

pause