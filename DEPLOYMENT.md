# 🚀 Przewodnik wdrożenia PetHelp na produkcję

## 📋 Spis treści

1. [Automatyczne wdrożenie](#automatyczne-wdrożenie)
2. [Ręczne wdrożenie](#ręczne-wdrożenie)
3. [Pierwsza instalacja](#pierwsza-instalacja)
4. [Rollback (cofnięcie zmian)](#rollback)
5. [Rozwiązywanie problemów](#rozwiązywanie-problemów)

---

## 🤖 Automatyczne wdrożenie

### Krok 1: Uploaduj skrypt na serwer

```bash
# Lokalnie: Wyślij skrypt na serwer
scp deploy.sh twoj-user@pethelp.pro-linuxpl.com:~/pethelp/

# Lub użyj Git (zalecane)
git add deploy.sh
git commit -m "Add deployment script"
git push origin master
```

### Krok 2: Nadaj uprawnienia wykonywania

```bash
# Połącz się z serwerem
ssh twoj-user@pethelp.pro-linuxpl.com

# Przejdź do katalogu aplikacji
cd ~/pethelp

# Nadaj uprawnienia
chmod +x deploy.sh
```

### Krok 3: Uruchom deployment

```bash
# Uruchom skrypt
./deploy.sh
```

**To wszystko!** Skrypt automatycznie:
- ✅ Włączy tryb konserwacji
- ✅ Pobierze zmiany z Git
- ✅ Zainstaluje zależności (Composer + NPM)
- ✅ Zbuduje assety
- ✅ Wykona migracje
- ✅ Wyczyści i zbuduje cache
- ✅ Wyłączy tryb konserwacji

---

## 🔧 Ręczne wdrożenie

Jeśli wolisz mieć pełną kontrolę:

### 1. Połącz się z serwerem

```bash
ssh twoj-user@pethelp.pro-linuxpl.com
cd ~/pethelp
```

### 2. Włącz tryb konserwacji

```bash
php artisan down --retry=60
```

### 3. Pobierz zmiany

```bash
git pull origin master
```

### 4. Aktualizuj zależności

```bash
# Composer
composer install --no-dev --optimize-autoloader --no-interaction

# NPM
npm ci
npm run build
```

### 5. Wykonaj migracje

```bash
php artisan migrate --force
```

### 6. Zbuduj cache

```bash
# Wyczyść stary cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Zbuduj nowy cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Uprawnienia

```bash
chmod -R 775 storage bootstrap/cache
```

### 8. Wyłącz tryb konserwacji

```bash
php artisan up
```

---

## 🆕 Pierwsza instalacja

Jeśli instalujesz aplikację po raz pierwszy, skorzystaj z szczegółowego przewodnika:

📖 **[HOSTING_SHARED_INSTALL.md](HOSTING_SHARED_INSTALL.md)**

**Kluczowe kroki:**
1. Konfiguracja bazy danych (cPanel)
2. Sklonowanie repozytorium
3. Konfiguracja `.env`
4. Instalacja zależności
5. Migracje
6. Konfiguracja `public_html`
7. SSL

---

## ⏪ Rollback (cofnięcie zmian)

### Opcja A: Rollback przez Git

```bash
# Połącz się z serwerem
ssh twoj-user@pethelp.pro-linuxpl.com
cd ~/pethelp

# Włącz tryb konserwacji
php artisan down

# Sprawdź ostatnie commity
git log --oneline -10

# Wróć do poprzedniej wersji (zastąp COMMIT_HASH)
git reset --hard COMMIT_HASH

# Lub wróć do poprzedniego commita
git reset --hard HEAD~1

# Zbuduj ponownie
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Wyłącz tryb konserwacji
php artisan up
```

### Opcja B: Rollback migracji

Jeśli problem jest w migracji:

```bash
# Sprawdź status migracji
php artisan migrate:status

# Cofnij ostatnią migracją (lub batch)
php artisan migrate:rollback --step=1

# Lub cofnij cały ostatni batch
php artisan migrate:rollback
```

---

## 🆘 Rozwiązywanie problemów

### Problem 1: Błąd 500 po deployment

```bash
# Sprawdź logi Laravel
tail -100 ~/pethelp/storage/logs/laravel.log

# Sprawdź uprawnienia
ls -la ~/pethelp/storage
ls -la ~/pethelp/bootstrap/cache

# Wyczyść cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Uprawnienia
chmod -R 775 storage bootstrap/cache
```

### Problem 2: CSS/JS nie ładuje się

```bash
# Sprawdź czy pliki istnieją
ls -la ~/pethelp/public/build/

# Zbuduj ponownie
cd ~/pethelp
npm run build

# Sprawdź .env
grep -E "APP_URL|ASSET_URL" .env

# Powinno być:
# APP_URL=https://pethelp.pro-linuxpl.com
# ASSET_URL=https://pethelp.pro-linuxpl.com
```

### Problem 3: Błąd bazy danych

```bash
# Test połączenia
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# Sprawdź dane w .env
cat .env | grep DB_

# Sprawdź czy baza istnieje w cPanel
```

### Problem 4: "No application encryption key"

```bash
cd ~/pethelp
php artisan key:generate
php artisan config:cache
```

### Problem 5: Strona nie wyłączyła trybu konserwacji

```bash
# Ręcznie usuń plik
rm ~/pethelp/storage/framework/down

# Lub wymuś wyłączenie
php artisan up --force
```

### Problem 6: Composer timeout

```bash
# Zwiększ timeout dla Composera
export COMPOSER_PROCESS_TIMEOUT=600
composer install --no-dev --optimize-autoloader
```

### Problem 7: NPM build zabija proces (out of memory)

```bash
# Zwiększ limit pamięci Node.js
NODE_OPTIONS="--max-old-space-size=2048" npm run build

# Lub zbuduj lokalnie i wyślij pliki
# Lokalnie:
npm run build
scp -r public/build/* user@server:~/pethelp/public/build/
```

---

## 📊 Monitoring po wdrożeniu

### Sprawdź logi w czasie rzeczywistym

```bash
# Laravel logs
tail -f ~/pethelp/storage/logs/laravel.log

# Apache/Nginx logs (jeśli masz dostęp)
tail -f /var/log/apache2/error.log
```

### Sprawdź wielkość storage

```bash
# Całkowity rozmiar
du -sh ~/pethelp/storage

# Największe pliki w logach
find ~/pethelp/storage/logs -type f -exec ls -lh {} \; | sort -k5 -hr | head -10
```

### Wyczyść stare logi (opcjonalnie)

```bash
# Usuń logi starsze niż 7 dni
find ~/pethelp/storage/logs -name "*.log" -mtime +7 -delete

# Lub skompresuj
find ~/pethelp/storage/logs -name "*.log" -mtime +7 -exec gzip {} \;
```

---

## ⚡ Szybkie komendy

### Pojedyncza komenda deployment

Jeśli chcesz wszystko w jednej linii:

```bash
cd ~/pethelp && \
php artisan down && \
git pull origin master && \
composer install --no-dev --optimize-autoloader --no-interaction && \
npm ci && \
npm run build && \
php artisan migrate --force && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
chmod -R 775 storage bootstrap/cache && \
php artisan up && \
echo "✅ Deployment completed!"
```

### Tylko cache refresh (bez zmian w kodzie)

```bash
cd ~/pethelp && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan view:clear && \
php artisan route:clear && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
echo "✅ Cache refreshed!"
```

---

## 🔐 Bezpieczeństwo

### Sprawdź uprawnienia plików

```bash
# .env nie powinno być dostępne publicznie
chmod 600 ~/pethelp/.env

# Storage i cache
chmod -R 775 ~/pethelp/storage
chmod -R 775 ~/pethelp/bootstrap/cache

# Sprawdź czy .env nie jest w public_html
ls -la ~/public_html/.env  # Nie powinno istnieć!
```

### Wyłącz debug mode na produkcji

W pliku `.env`:

```bash
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
DEBUGBAR_ENABLED=false
```

---

## 📅 Automatyzacja (opcjonalnie)

### Cron dla Laravel Scheduler

Jeśli używasz Laravel Scheduler, dodaj w cPanel → Cron Jobs:

```bash
* * * * * cd /home/TWOJ_USER/pethelp && php artisan schedule:run >> /dev/null 2>&1
```

### Automatyczne backupy

```bash
# Dodaj w crontab
0 2 * * * mysqldump -u DB_USER -pDB_PASS DB_NAME | gzip > /home/user/backups/pethelp-$(date +\%Y\%m\%d).sql.gz
```

---

## 📞 Kontakt w razie problemów

Jeśli napotkasz problemy:

1. **Sprawdź logi**: `tail -100 ~/pethelp/storage/logs/laravel.log`
2. **Sprawdź dokumentację**: [HOSTING_SHARED_INSTALL.md](HOSTING_SHARED_INSTALL.md)
3. **Kontakt z hostigiem**: Support Cyberfolk

---

**Dokumentacja wygenerowana dla PetHelp v2.0**
Ostatnia aktualizacja: 2025-10-03
