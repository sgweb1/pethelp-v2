@echo off
echo Przełączanie na domenę lokalną...

REM Ustawia USE_NGROK=false w pliku .env
powershell -Command "(Get-Content .env) -replace '^USE_NGROK=.*', 'USE_NGROK=false' | Set-Content .env"

REM Wyczyść cache konfiguracji
php artisan config:clear

echo Aplikacja teraz działa na: http://pethelp.test
echo Cache konfiguracji został wyczyszczony.

pause