# 🌐 Instalacja PetHelp na Hostingu Współdzielonym

Instrukcja dla hostingu: **pethelp.pro-linuxpl.com**

---

## 📋 KROK 1: Sprawdzenie środowiska

Połącz się przez SSH:
```bash
ssh twoj-user@pethelp.pro-linuxpl.com
```

Sprawdź dostępne narzędzia:
```bash
# Wersja PHP
php -v
# Wymagane: PHP 8.2+ (najlepiej 8.3)

# Composer
composer --version

# Node.js i npm
node -v
npm -v

# Git
git --version

# Sprawdź gdzie jesteś
pwd

# Zobacz strukturę
ls -la
```

**Zapisz wyniki!** Będą nam potrzebne.

---

## 📁 KROK 2: Struktura katalogów

Typowa struktura hostingu:
```
/home/user/
├── public_html/          # To widzi świat (domena)
├── domains/              # Inne domeny (jeśli są)
├── logs/                 # Logi
└── tmp/                  # Temp
```

Zainstalujemy aplikację **poza public_html**:
```
/home/user/
├── pethelp/              # ← Tutaj Laravel
│   ├── app/
│   ├── public/           # ← To przekierujemy do public_html
│   ├── storage/
│   └── ...
└── public_html/          # ← Tutaj przekierowanie
```

---

## 🗄️ KROK 3: Baza danych (Panel cPanel)

### A. Utwórz bazę danych:
1. Zaloguj się do **cPanel**
2. **MySQL Databases** lub **phpMyAdmin**
3. Utwórz nową bazę:
   - Nazwa: `user_pethelp` (zwykle prefix_nazwa)
   - Collation: `utf8mb4_unicode_ci`

### B. Utwórz użytkownika:
1. W tym samym panelu **MySQL Databases**
2. **Add New User**:
   - Username: `user_pethelp`
   - Password: **[wygeneruj silne hasło]**
   - Zapisz hasło!

### C. Przypisz użytkownika do bazy:
1. **Add User To Database**
2. Wybierz użytkownika i bazę
3. Zaznacz: **ALL PRIVILEGES**

### D. Zapisz dane:
```
Host: localhost
Database: user_pethelp
Username: user_pethelp
Password: [twoje_hasło]
```

---

## 📥 KROK 4: Sklonowanie aplikacji

```bash
# Przejdź do katalogu domowego
cd ~

# Sklonuj repozytorium
git clone https://github.com/sgweb1/pethelp-v2.git pethelp

# Wejdź do katalogu
cd pethelp

# Sprawdź co masz
ls -la
```

---

## ⚙️ KROK 5: Konfiguracja .env

```bash
# Skopiuj przykładowy plik
cp .env.example .env

# Edytuj
nano .env
```

### Minimalna konfiguracja .env dla hostingu:

```bash
# === APLIKACJA ===
APP_NAME=PetHelp
APP_ENV=production
APP_KEY=            # Wygenerujemy za chwilę
APP_DEBUG=false
APP_TIMEZONE=Europe/Warsaw
APP_URL=https://pethelp.pro-linuxpl.com
ASSET_URL=https://pethelp.pro-linuxpl.com
APP_LOCALE=pl

# === BAZA DANYCH ===
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=user_pethelp        # Twoja nazwa bazy
DB_USERNAME=user_pethelp        # Twój user
DB_PASSWORD=TWOJE_HASLO         # Hasło z kroku 3

# === CACHE (hosting współdzielony - bez Redis) ===
CACHE_STORE=file
SESSION_DRIVER=database
SESSION_LIFETIME=720
QUEUE_CONNECTION=database

# === FILESYSTEM ===
FILESYSTEM_DISK=public

# === LOGGING ===
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

# === DEBUGBAR (WYŁĄCZONY NA PRODUKCJI) ===
DEBUGBAR_ENABLED=false

# === PAYU (tymczasowo sandbox) ===
PAYU_ENVIRONMENT=sandbox
PAYU_API_TYPE=rest
PAYU_MERCHANT_ID=496823
PAYU_SECRET_KEY=b3df2630c03644c1207c0435f200464e
PAYU_OAUTH_CLIENT_ID=496823
PAYU_OAUTH_CLIENT_SECRET=073bb6d75307cdbb7b9b8996514536ba

# === NGROK/LOCAL (WYŁĄCZONE) ===
USE_NGROK=false
LOCAL_DOMAIN=https://pethelp.pro-linuxpl.com
```

Zapisz: `CTRL+O`, `ENTER`, `CTRL+X`

---

## 🔧 KROK 6: Instalacja zależności

```bash
# Wróć do katalogu aplikacji
cd ~/pethelp

# Instalacja Composer (bez dev dependencies)
composer install --no-dev --optimize-autoloader --no-interaction

# Generowanie klucza aplikacji
php artisan key:generate

# Sprawdź czy klucz został wygenerowany
grep APP_KEY .env
```

---

## 🎨 KROK 7: Build assetów

```bash
# Instalacja npm
npm ci

# Build produkcyjny (może potrwać 2-5 minut)
npm run build

# Sprawdź czy powstały pliki
ls -la public/build/
```

---

## 🗃️ KROK 8: Migracje bazy danych

```bash
# Test połączenia z bazą
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# Jeśli test OK, uruchom migracje
php artisan migrate --force

# Sprawdź status migracji
php artisan migrate:status

# Opcjonalnie: załaduj kategorie usług
php artisan db:seed --class=ServiceCategorySeeder --force
```

---

## 🔗 KROK 9: Konfiguracja public_html

Musimy przekierować `public_html` do `~/pethelp/public`

### Opcja A: Symlink (zalecane, jeśli hosting pozwala)

```bash
# Usuń lub zmień nazwę starego public_html
mv ~/public_html ~/public_html.old

# Utwórz symlink
ln -s ~/pethelp/public ~/public_html

# Sprawdź
ls -la ~/ | grep public_html
```

### Opcja B: .htaccess w public_html (jeśli symlink nie działa)

```bash
# Przywróć public_html jeśli usunąłeś
mv ~/public_html.old ~/public_html

# Utwórz .htaccess
nano ~/public_html/.htaccess
```

Zawartość:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ /home/TWOJ_USER/pethelp/public/$1 [L]
</IfModule>
```

**ZAMIEŃ `TWOJ_USER`** na swoją nazwę użytkownika (sprawdź przez `whoami`)

### Opcja C: Kopiowanie (najmniej optymalne)

```bash
# Skopiuj zawartość
cp -r ~/pethelp/public/* ~/public_html/
cp ~/pethelp/public/.htaccess ~/public_html/
```

**UWAGA:** Przy każdej aktualizacji musisz ponownie kopiować!

---

## 🔐 KROK 10: Uprawnienia

```bash
# Ustaw uprawnienia dla storage i cache
chmod -R 775 ~/pethelp/storage
chmod -R 775 ~/pethelp/bootstrap/cache

# Link do storage
cd ~/pethelp
php artisan storage:link
```

---

## 🚀 KROK 11: Finalizacja i cache

```bash
cd ~/pethelp

# Zbuduj cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optymalizacja autoloadera
composer dump-autoload --optimize
```

---

## ✅ KROK 12: Test aplikacji

### Sprawdzenie przez przeglądarkę:
```
https://pethelp.pro-linuxpl.com
```

Powinieneś zobaczyć stronę główną PetHelp!

### Sprawdzenie przez SSH:
```bash
# Test HTTP
curl -I https://pethelp.pro-linuxpl.com

# Sprawdź logi
tail -f ~/pethelp/storage/logs/laravel.log
```

---

## 🔧 KROK 13: Konfiguracja SSL (jeśli nie działa HTTPS)

Jeśli hosting nie ma automatycznego SSL:

### W cPanel:
1. **SSL/TLS** → **Manage SSL**
2. **Install an SSL Website** lub **AutoSSL**
3. Włącz dla domeny `pethelp.pro-linuxpl.com`

### Let's Encrypt (jeśli dostępne):
```bash
# Zwykle cPanel robi to automatycznie
# Jeśli nie, poproś support hostingu o włączenie SSL
```

---

## 🔄 AKTUALIZACJA APLIKACJI

Gdy będziesz chciał zaktualizować aplikację:

```bash
cd ~/pethelp

# Pobierz nowe zmiany
git pull origin master

# Aktualizuj zależności
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Migracje
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jeśli używasz Opcji C (kopiowanie):
cp -r ~/pethelp/public/* ~/public_html/
```

---

## 🆘 Rozwiązywanie problemów

### Problem: Błąd 500
```bash
# Sprawdź logi
tail -50 ~/pethelp/storage/logs/laravel.log

# Sprawdź uprawnienia
ls -la ~/pethelp/storage
ls -la ~/pethelp/bootstrap/cache

# Wyczyść cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Problem: CSS/JS nie ładuje się
```bash
# Sprawdź czy pliki istnieją
ls -la ~/pethelp/public/build/

# Jeśli nie ma - zbuduj ponownie
cd ~/pethelp
npm run build

# Sprawdź APP_URL i ASSET_URL w .env
grep -E "APP_URL|ASSET_URL" .env
```

### Problem: Błąd bazy danych
```bash
# Test połączenia
php artisan tinker
>>> DB::connection()->getPdo();

# Sprawdź dane w .env
cat .env | grep DB_

# Sprawdź w panelu czy baza i user istnieją
```

### Problem: "No application encryption key"
```bash
cd ~/pethelp
php artisan key:generate
php artisan config:cache
```

---

## 📊 Monitoring na hostingu współdzielonym

```bash
# Logi Laravel
tail -f ~/pethelp/storage/logs/laravel.log

# Sprawdź wielkość storage
du -sh ~/pethelp/storage

# Lista największych plików
find ~/pethelp/storage/logs -type f -exec ls -lh {} \; | sort -k5 -hr | head -10
```

---

## ⚠️ Ograniczenia hostingu współdzielonego

- ❌ **Brak Supervisor** - kolejki będą działać wolniej (używamy database)
- ❌ **Brak Redis** - cache w plikach (wolniejsze)
- ❌ **Limity pamięci** - może być problem z dużymi operacjami
- ❌ **Limity CPU** - może być wolniejsze przy dużym ruchu
- ⚠️ **Cron** - możesz skonfigurować w cPanel dla Laravel Scheduler

### Konfiguracja Cron (opcjonalnie):
W cPanel → Cron Jobs:
```
* * * * * cd /home/TWOJ_USER/pethelp && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🎯 Checklist końcowy

- [ ] Aplikacja odpowiada na https://pethelp.pro-linuxpl.com
- [ ] SSL działa (zielona kłódka)
- [ ] Można się zarejestrować
- [ ] Można się zalogować
- [ ] CSS/JS ładuje się poprawnie
- [ ] Brak błędów w logach
- [ ] Storage link działa
- [ ] PayU skonfigurowane (jeśli potrzebne)

---

**Gotowe! Aplikacja działa na hostingu współdzielonym! 🎉**
