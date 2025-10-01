# 🚀 Przewodnik Wdrożenia PetHelp

## Szybkie Wdrożenie - 3 Proste Kroki

### Krok 1: Przygotuj serwer
Potrzebujesz serwera z:
- **Ubuntu 20.04+**, Debian 11+ lub CentOS 8+
- Dostęp root (sudo)
- Połączenie internetowe
- Dostępny port 80 i 443

### Krok 2: Pobierz i uruchom skrypt instalacyjny

```bash
# Pobierz skrypt
curl -O https://raw.githubusercontent.com/sgweb1/pethelp-v2/master/install-production.sh

# Nadaj uprawnienia
chmod +x install-production.sh

# Uruchom instalację
sudo ./install-production.sh
```

### Krok 3: Odpowiedz na pytania

Skrypt zapyta o:
- **Domenę** - np. `pethelp.pl`
- **Email administratora** - np. `admin@pethelp.pl`
- **Hasło do bazy danych** - może wygenerować automatycznie
- **Redis** - rekomendujemy **NIE** (zostaw domyślne `n`)
- **SSL** - rekomendujemy **TAK** (zostaw domyślne `y`)
- **Supervisor** - rekomendujemy **TAK** (zostaw domyślne `y`)

---

## Zalecana Konfiguracja dla Produkcji

### Minimalna (bez Redis)
```
Domena:              twoja-domena.pl
Redis:               NIE (n)
Supervisor:          TAK (y)
SSL:                 TAK (y)
Dane testowe:        NIE (n)
```

**Idealny dla:**
- Małych i średnich instalacji
- Budżetowych serwerów
- Standardowego hostingu

### Zaawansowana (z Redis)
```
Domena:              twoja-domena.pl
Redis:               TAK (y)
Supervisor:          TAK (y)
SSL:                 TAK (y)
Dane testowe:        NIE (n)
```

**Idealny dla:**
- Dużych instalacji
- Wysokiego ruchu
- Dedykowanych serwerów VPS/Cloud

---

## Aktualizacja Aplikacji

### Automatyczna aktualizacja

```bash
cd /var/www/pethelp
./update-production.sh
```

Wybierz opcję:
- **1** - Pełna aktualizacja (zalecane)
- **2** - Tylko kod
- **3** - Tylko zależności
- **4** - Tylko assety
- **5** - Tylko migracje
- **6** - Tylko cache
- **7** - Czyszczenie cache

### Szybka aktualizacja (jednoliniowa)

```bash
cd /var/www/pethelp && php artisan down && git pull && composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan queue:restart && php artisan up
```

---

## Konfiguracja Środowiska (.env)

### Edycja konfiguracji

```bash
cd /var/www/pethelp
nano .env
```

### Kluczowe ustawienia produkcyjne

```bash
# === PRODUKCJA ===
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error

# === BAZA DANYCH ===
DB_CONNECTION=mysql
DB_DATABASE=pethelp_production
DB_USERNAME=pethelp_user
DB_PASSWORD=TWOJE_BEZPIECZNE_HASŁO

# === CACHE (bez Redis) ===
CACHE_STORE=file
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# === CACHE (z Redis) ===
# CACHE_STORE=redis
# SESSION_DRIVER=redis
# QUEUE_CONNECTION=redis

# === PAYU (PRODUKCJA) ===
PAYU_ENVIRONMENT=secure
PAYU_MERCHANT_POS_ID=TWOJ_POS_ID
PAYU_SIGNATURE_KEY=TWOJ_KLUCZ
PAYU_CLIENT_ID=TWOJ_CLIENT_ID
PAYU_CLIENT_SECRET=TWOJ_SECRET

# === EMAIL ===
MAIL_MAILER=smtp
MAIL_HOST=smtp.twoja-domena.pl
MAIL_PORT=587
MAIL_USERNAME=noreply@twoja-domena.pl
MAIL_PASSWORD=HASŁO_SMTP
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@twoja-domena.pl
```

Po edycji:
```bash
php artisan config:cache
```

---

## Konfiguracja PayU (Płatności)

### 1. Uzyskaj dane dostępowe PayU

Zaloguj się do panelu PayU i uzyskaj:
- **Merchant POS ID** (np. `145227`)
- **Signature Key** (klucz MD5)
- **OAuth Client ID** (np. `145227`)
- **OAuth Client Secret**

### 2. Ustaw w pliku .env

```bash
nano /var/www/pethelp/.env
```

```bash
# PayU Production
PAYU_ENVIRONMENT=secure
PAYU_API_TYPE=rest
PAYU_MERCHANT_ID=TWOJ_POS_ID
PAYU_SECRET_KEY=TWOJ_SIGNATURE_KEY
PAYU_OAUTH_CLIENT_ID=TWOJ_CLIENT_ID
PAYU_OAUTH_CLIENT_SECRET=TWOJ_CLIENT_SECRET
```

### 3. Testowanie (Sandbox)

Do testowania możesz użyć trybu sandbox:

```bash
PAYU_ENVIRONMENT=sandbox
PAYU_MERCHANT_ID=145227
PAYU_SECRET_KEY=13a980d4f851f3d9a1cfc792fb1f5e50
PAYU_OAUTH_CLIENT_ID=145227
PAYU_OAUTH_CLIENT_SECRET=12f071174cb7eb79d4aac5bc2f07563f
```

### 4. Przebuduj cache

```bash
php artisan config:cache
```

---

## Monitorowanie i Utrzymanie

### Sprawdzanie statusu

```bash
# Status wszystkich serwisów
systemctl status nginx
systemctl status php8.3-fpm
systemctl status mysql
supervisorctl status

# Logi aplikacji
tail -f /var/www/pethelp/storage/logs/laravel.log

# Logi Nginx
tail -f /var/log/nginx/error.log

# Logi PHP
tail -f /var/log/php8.3-fpm.log
```

### Czyszczenie logów

```bash
# Wyczyść stare logi Laravel (starsze niż 7 dni)
find /var/www/pethelp/storage/logs -name "*.log" -mtime +7 -delete

# Wyczyść cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Backup

#### Backup bazy danych

```bash
# MySQL
mysqldump -u pethelp_user -p pethelp_production > ~/backup-$(date +%Y%m%d).sql

# PostgreSQL
pg_dump -U pethelp_user pethelp_production > ~/backup-$(date +%Y%m%d).sql
```

#### Backup plików

```bash
# Backup całej aplikacji
tar -czf ~/pethelp-backup-$(date +%Y%m%d).tar.gz /var/www/pethelp

# Tylko storage (zdjęcia, pliki użytkowników)
tar -czf ~/pethelp-storage-$(date +%Y%m%d).tar.gz /var/www/pethelp/storage/app
```

#### Automatyczny backup (cron)

```bash
crontab -e
```

Dodaj:
```bash
# Backup bazy danych codziennie o 2:00
0 2 * * * mysqldump -u pethelp_user -pHASŁO pethelp_production > ~/backups/db-$(date +\%Y\%m\%d).sql

# Backup storage co tydzień w niedzielę o 3:00
0 3 * * 0 tar -czf ~/backups/storage-$(date +\%Y\%m\%d).tar.gz /var/www/pethelp/storage/app

# Usuwanie starych backupów (starszych niż 30 dni)
0 4 * * * find ~/backups -name "*.sql" -mtime +30 -delete
0 4 * * * find ~/backups -name "*.tar.gz" -mtime +30 -delete
```

---

## Rozwiązywanie Problemów

### Problem: Błąd 500 po instalacji

```bash
# Sprawdź logi
tail -f /var/www/pethelp/storage/logs/laravel.log

# Sprawdź uprawnienia
sudo chown -R www-data:www-data /var/www/pethelp
sudo chmod -R 755 /var/www/pethelp
sudo chmod -R 775 /var/www/pethelp/storage
sudo chmod -R 775 /var/www/pethelp/bootstrap/cache

# Przebuduj cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Problem: Strona nie ładuje CSS/JS

```bash
# Przebuduj assety
cd /var/www/pethelp
npm run build

# Sprawdź link do storage
php artisan storage:link

# Sprawdź uprawnienia
ls -la public/build
ls -la public/storage
```

### Problem: Kolejki nie działają

```bash
# Sprawdź status Supervisor
sudo supervisorctl status pethelp-worker:*

# Restart workerów
sudo supervisorctl restart pethelp-worker:*

# Sprawdź logi
tail -f /var/www/pethelp/storage/logs/worker.log

# Test kolejki
php artisan queue:work --once
```

### Problem: Błąd połączenia z bazą danych

```bash
# Sprawdź status MySQL
systemctl status mysql

# Test połączenia
mysql -u pethelp_user -p pethelp_production

# Sprawdź konfigurację
php artisan config:show database

# Przebuduj cache konfiguracji
php artisan config:clear
php artisan config:cache
```

### Problem: SSL/HTTPS nie działa

```bash
# Sprawdź certyfikat
sudo certbot certificates

# Odnów certyfikat
sudo certbot renew

# Test Nginx
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

---

## Optymalizacja Wydajności

### PHP-FPM

```bash
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

### MySQL

```bash
sudo nano /etc/mysql/my.cnf
```

```ini
[mysqld]
max_connections = 100
query_cache_size = 32M
query_cache_limit = 2M
innodb_buffer_pool_size = 512M
```

### OPcache

```bash
sudo nano /etc/php/8.3/fpm/conf.d/10-opcache.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

Po zmianach:
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl restart mysql
```

---

## Checklist Wdrożenia

### Przed wdrożeniem

- [ ] Serwer spełnia wymagania systemowe
- [ ] Domena wskazuje na serwer (DNS)
- [ ] Dostęp root/sudo do serwera
- [ ] Dane PayU (jeśli płatności)
- [ ] Dane SMTP (jeśli email)

### Po instalacji

- [ ] Aplikacja odpowiada na HTTPS
- [ ] SSL działa poprawnie
- [ ] Możliwe logowanie/rejestracja
- [ ] Baza danych działa
- [ ] Kolejki działają (Supervisor)
- [ ] Sprawdzone logi (brak błędów)
- [ ] PayU skonfigurowane i przetestowane
- [ ] Email działa (test resetowania hasła)
- [ ] Backup skonfigurowany

### Bezpieczeństwo

- [ ] Firewall włączony (UFW)
- [ ] SSL certyfikat zainstalowany
- [ ] APP_DEBUG=false w .env
- [ ] Silne hasła (baza, admin)
- [ ] Backup automatyczny
- [ ] Monitoring logów

---

## Wsparcie

**Dokumentacja:**
- [Instrukcja instalacji](INSTRUKCJA_INSTALACJI_PRODUKCJA.md)
- [System Documentation](SYSTEM_DOCUMENTATION.md)

**Logi:**
- Laravel: `/var/www/pethelp/storage/logs/laravel.log`
- Nginx: `/var/log/nginx/error.log`
- PHP-FPM: `/var/log/php8.3-fpm.log`

**Przydatne komendy:**
```bash
# Status aplikacji
php artisan about

# Test konfiguracji
php artisan config:show

# Przegląd tras
php artisan route:list

# Sprawdzenie migracji
php artisan migrate:status
```

---

**Powodzenia z wdrożeniem! 🚀🐾**
