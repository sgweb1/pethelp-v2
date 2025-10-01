# 🌐 Przełączanie domen - instrukcja użycia

System umożliwia łatwe przełączanie między domeną lokalną (`http://pethelp.test`) a domeną ngrok dla deweloperów.

## 🚀 Szybki start

### Metoda 1: Komenda Laravel Artisan (Zalecana)
```bash
# Przełączenie na domenę lokalną
php artisan domain:switch local

# Przełączenie na ngrok (używa domeny z .env)
php artisan domain:switch ngrok

# Przełączenie na ngrok z nową domeną
php artisan domain:switch ngrok --ngrok-url="https://your-new-ngrok-url.ngrok.io"

# Przełączenie z wymuszonym rebuildingiem assetów
php artisan domain:switch local --rebuild-assets
```

### Metoda 2: Skrypty Windows (.bat)
```bash
# Przełącz na lokalną
./switch-to-local.bat

# Przełącz na ngrok (z opcjonalną nową domeną)
./switch-to-ngrok.bat
./switch-to-ngrok.bat "https://your-new-url.ngrok.io"
```

### Metoda 3: PowerShell
```powershell
# Przełącz na lokalną
./switch-domain.ps1 -Domain local

# Przełącz na ngrok
./switch-domain.ps1 -Domain ngrok

# Przełącz na ngrok z nową domeną
./switch-domain.ps1 -Domain ngrok -NgrokUrl "https://your-new-url.ngrok.io"
```

## ⚙️ Konfiguracja środowiska

System używa następujących zmiennych w pliku `.env`:

```env
# Domain Configuration - Set USE_NGROK=true to use ngrok, false for local
USE_NGROK=false
LOCAL_DOMAIN=http://pethelp.test
NGROK_DOMAIN=https://sneaky-angeles-immensely.ngrok-free.dev
```

### Zmienne konfiguracyjne:
- **`USE_NGROK`** - `true` dla ngrok, `false` dla lokalnej domeny
- **`LOCAL_DOMAIN`** - URL lokalnego środowiska deweloperskiego
- **`NGROK_DOMAIN`** - URL tunelu ngrok

## 🔧 Jak to działa

1. **Dynamiczna konfiguracja URL**: `config/app.php` automatycznie wybiera domenę na podstawie `USE_NGROK`
2. **Automatyczne czyszczenie cache**: Po zmianie system automatycznie czyści cache konfiguracji Laravel
3. **Rebuilding assetów**: Przy przełączeniu na ngrok assety są automatycznie przebudowane
4. **Przywracanie stanu**: Domeny można zmieniać wielokrotnie bez problemów

## 📋 Przykłady użycia

### Podczas lokalnego developmentu:
```bash
php artisan domain:switch local
```

### Przed udostępnieniem klientowi przez ngrok:
```bash
# Rozpocznij tunel ngrok
ngrok http 8000

# Przełącz aplikację na ngrok z nowym URL
php artisan domain:switch ngrok --ngrok-url="https://abc123.ngrok.io"
```

### Powrót do pracy lokalnej:
```bash
php artisan domain:switch local
```

## ✅ Weryfikacja

Po przełączeniu możesz sprawdzić aktualną konfigurację:

```bash
# Sprawdź URL aplikacji
php artisan tinker --execute="echo config('app.url');"

# Sprawdź pełną konfigurację domenę
php artisan domain:switch local  # wyświetli aktualną konfigurację
```

## 🎯 Automatyczne procesy

### Przy przełączeniu na ngrok:
- Aktualizacja `USE_NGROK=true` w .env
- Opcjonalna aktualizacja `NGROK_DOMAIN`
- Czyszczenie cache konfiguracji Laravel
- **Automatyczne przebudowanie assetów** (npm run build)

### Przy przełączeniu na local:
- Aktualizacja `USE_NGROK=false` w .env
- Czyszczenie cache konfiguracji Laravel
- Assety pozostają bez zmian (nie trzeba rebuildu)

## 🚨 Uwagi ważne

1. **Assety**: System automatycznie rebuiluje assety przy przełączeniu na ngrok
2. **Cache**: Cache konfiguracji jest automatycznie czyszczony
3. **Backup**: Poprzednie ustawienia domen są zachowane w .env
4. **Ngrok aktywny**: Upewnij się że tunel ngrok jest aktywny przed przełączeniem

## 🔍 Troubleshooting

### Problem: Aplikacja nie ładuje się po przełączeniu
```bash
# Sprawdź czy serwer jest aktywny
php artisan serve --host=0.0.0.0 --port=8000

# Dla ngrok - sprawdź czy tunel jest aktywny
ngrok http 8000
```

### Problem: Assety się nie ładują
```bash
# Wymuszenie rebuildu assetów
npm run build
# lub
php artisan build-assets
```

### Problem: Błędny URL w aplikacji
```bash
# Wyczyść wszystkie cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
```