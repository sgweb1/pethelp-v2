# 📋 PetHelp Production Cheatsheet

Szybki ściągawka do codziennego zarządzania aplikacją na produkcji.

---

## 🚀 Instalacja (Jednorazowa)

```bash
curl -O https://raw.githubusercontent.com/sgweb1/pethelp-v2/master/install-production.sh
chmod +x install-production.sh
sudo ./install-production.sh
```

---

## 🔄 Aktualizacja Aplikacji

### Pełna aktualizacja (zalecane)
```bash
cd /var/www/pethelp
./update-production.sh
# Wybierz opcję: 1
```

### Szybka aktualizacja bez menu
```bash
cd /var/www/pethelp
php artisan down && \
git pull origin master && \
composer install --no-dev --optimize-autoloader && \
npm ci && npm run build && \
php artisan migrate --force && \
php artisan config:cache && php artisan route:cache && php artisan view:cache && \
supervisorctl restart pethelp-worker:* && \
php artisan up
```

---

## 🛠️ Podstawowe Operacje

### Tryb konserwacji
```bash
# Włącz
php artisan down

# Wyłącz
php artisan up
```

### Cache
```bash
# Wyczyść cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Zbuduj cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Kolejki i workery
```bash
# Restart kolejek
php artisan queue:restart

# Restart workerów Supervisor
sudo supervisorctl restart pethelp-worker:*

# Status workerów
sudo supervisorctl status

# Ręczne uruchomienie queue worker (debug)
php artisan queue:work --once
```

---

## 📊 Monitoring

### Logi
```bash
# Laravel logs (real-time)
tail -f /var/www/pethelp/storage/logs/laravel.log

# Nginx error log
tail -f /var/log/nginx/error.log

# PHP-FPM log
tail -f /var/log/php8.3-fpm.log

# Supervisor worker log
tail -f /var/www/pethelp/storage/logs/worker.log
```

### Status serwisów
```bash
# Wszystkie naraz
systemctl status nginx php8.3-fpm mysql
supervisorctl status

# Pojedyncze
systemctl status nginx
systemctl status php8.3-fpm
systemctl status mysql
```

### Restart serwisów
```bash
# Nginx
sudo systemctl restart nginx

# PHP-FPM
sudo systemctl restart php8.3-fpm

# MySQL
sudo systemctl restart mysql

# Supervisor
sudo systemctl restart supervisor
```

---

## 💾 Backup

### Szybki backup bazy danych
```bash
# MySQL
mysqldump -u pethelp_user -p pethelp_production > ~/backup-$(date +%Y%m%d-%H%M).sql

# PostgreSQL
pg_dump -U pethelp_user pethelp_production > ~/backup-$(date +%Y%m%d-%H%M).sql
```

### Backup plików
```bash
# Cała aplikacja
tar -czf ~/pethelp-full-$(date +%Y%m%d).tar.gz /var/www/pethelp

# Tylko storage (pliki użytkowników)
tar -czf ~/pethelp-storage-$(date +%Y%m%d).tar.gz /var/www/pethelp/storage/app
```

### Przywracanie z backupu
```bash
# Baza danych
mysql -u pethelp_user -p pethelp_production < backup.sql

# Pliki
tar -xzf pethelp-storage-backup.tar.gz -C /
```

---

## 🔒 Bezpieczeństwo

### SSL Certyfikat
```bash
# Sprawdź status
sudo certbot certificates

# Manualne odnowienie
sudo certbot renew

# Test odnowienia
sudo certbot renew --dry-run
```

### Uprawnienia plików
```bash
# Standardowe uprawnienia
sudo chown -R www-data:www-data /var/www/pethelp
sudo chmod -R 755 /var/www/pethelp
sudo chmod -R 775 /var/www/pethelp/storage
sudo chmod -R 775 /var/www/pethelp/bootstrap/cache
```

### Firewall (UFW)
```bash
# Status
sudo ufw status

# Włącz/wyłącz
sudo ufw enable
sudo ufw disable

# Podstawowe reguły
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
```

---

## 🗄️ Baza Danych

### Połączenie
```bash
# MySQL
mysql -u pethelp_user -p pethelp_production

# PostgreSQL
psql -U pethelp_user -d pethelp_production
```

### Migracje
```bash
# Uruchom migracje
php artisan migrate --force

# Sprawdź status
php artisan migrate:status

# Rollback (OSTROŻNIE!)
php artisan migrate:rollback --step=1
```

### Laravel Tinker (konsola PHP)
```bash
php artisan tinker

# Przykłady:
>>> User::count()
>>> DB::connection()->getPdo()
>>> Cache::get('key')
```

---

## 🐛 Rozwiązywanie Problemów

### Błąd 500
```bash
# 1. Sprawdź logi
tail -f /var/www/pethelp/storage/logs/laravel.log

# 2. Wyczyść cache
php artisan config:clear
php artisan cache:clear

# 3. Sprawdź uprawnienia
ls -la /var/www/pethelp/storage
ls -la /var/www/pethelp/bootstrap/cache

# 4. Napraw uprawnienia
sudo chmod -R 775 /var/www/pethelp/storage
sudo chmod -R 775 /var/www/pethelp/bootstrap/cache
```

### CSS/JS nie ładuje się
```bash
# 1. Przebuduj assety
cd /var/www/pethelp
npm run build

# 2. Link do storage
php artisan storage:link

# 3. Sprawdź uprawnienia public
ls -la public/build
ls -la public/storage
```

### Kolejki nie działają
```bash
# 1. Sprawdź workery
sudo supervisorctl status pethelp-worker:*

# 2. Restart
sudo supervisorctl restart pethelp-worker:*
php artisan queue:restart

# 3. Test manualny
php artisan queue:work --once

# 4. Logi
tail -f /var/www/pethelp/storage/logs/worker.log
```

### Wolna aplikacja
```bash
# 1. Włącz cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Optymalizuj autoloader
composer dump-autoload --optimize

# 3. Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# 4. Sprawdź MySQL
mysql -u root -p -e "SHOW FULL PROCESSLIST;"
```

---

## ⚙️ Konfiguracja

### Edycja .env
```bash
cd /var/www/pethelp
nano .env

# Po edycji ZAWSZE:
php artisan config:cache
```

### Ważne ustawienia .env
```bash
APP_ENV=production       # ZAWSZE production!
APP_DEBUG=false         # ZAWSZE false na prod!
LOG_LEVEL=error         # error lub warning
CACHE_STORE=file        # file lub redis
QUEUE_CONNECTION=database # database lub redis
```

### Restart po zmianach w .env
```bash
php artisan config:cache
sudo systemctl restart php8.3-fpm
sudo supervisorctl restart pethelp-worker:*
```

---

## 📈 Wydajność

### Sprawdź wykorzystanie zasobów
```bash
# Pamięć
free -h

# Dysk
df -h

# CPU
top

# Procesy PHP-FPM
ps aux | grep php-fpm | wc -l
```

### Czyszczenie miejsca
```bash
# Stare logi Laravel
find /var/www/pethelp/storage/logs -name "*.log" -mtime +30 -delete

# npm cache
npm cache clean --force

# composer cache
composer clear-cache

# Stare backupy
find ~/backups -mtime +30 -delete
```

---

## 🧪 Testowanie

### Test aplikacji
```bash
# HTTP status
curl -I https://twoja-domena.pl

# Test z logowaniem
curl -X POST https://twoja-domena.pl/login \
  -d "email=test@example.com&password=password"

# Test API
curl https://twoja-domena.pl/api/health
```

### Diagnostyka Laravel
```bash
# Informacje o aplikacji
php artisan about

# Sprawdź konfigurację
php artisan config:show database
php artisan config:show cache

# Lista route'ów
php artisan route:list

# Test maila
php artisan mail:test admin@example.com
```

---

## 📞 Szybki Kontakt

**Dokumentacja:**
- Przewodnik: `/var/www/pethelp/DEPLOYMENT_GUIDE.md`
- Instrukcja: `/var/www/pethelp/INSTRUKCJA_INSTALACJI_PRODUKCJA.md`

**Logi:**
- Laravel: `/var/www/pethelp/storage/logs/laravel.log`
- Nginx: `/var/log/nginx/error.log`
- PHP: `/var/log/php8.3-fpm.log`

**Konfiguracja:**
- Env: `/var/www/pethelp/.env`
- Nginx: `/etc/nginx/sites-available/pethelp`
- Supervisor: `/etc/supervisor/conf.d/pethelp-worker.conf`

---

**Wydrukuj tę ściągawkę i trzymaj pod ręką! 📋**
