# 🚀 Instrukcja instalacji PetHelp na produkcji

## 📋 Wymagania systemowe

### Minimalne wymagania serwera:
- **CPU**: 2 rdzenie (4 zalecane)
- **RAM**: 4GB (8GB zalecane)
- **Dysk**: 20GB wolnego miejsca (SSD zalecane)
- **System**: Ubuntu 20.04+ / CentOS 8+ / Debian 11+

### Wymagane oprogramowanie:
- **PHP 8.3+** z rozszerzeniami:
  - BCMath, Ctype, Curl, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML, GD
- **Composer 2.x**
- **Node.js 18+** i **npm**
- **MySQL 8.0+** lub **PostgreSQL 14+**
- **Redis 6.0+** (opcjonalnie dla cache i sessions)
- **Nginx** lub **Apache**
- **Git**

---

## 🔧 Krok 1: Przygotowanie serwera

### Ubuntu/Debian:
```bash
# Aktualizacja systemu
sudo apt update && sudo apt upgrade -y

# Instalacja PHP i rozszerzeń
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-pgsql php8.3-redis \
    php8.3-curl php8.3-dom php8.3-mbstring php8.3-xml php8.3-zip \
    php8.3-bcmath php8.3-gd php8.3-intl php8.3-cli

# Instalacja Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalacja Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Instalacja MySQL
sudo apt install -y mysql-server

# Instalacja Redis (opcjonalnie)
sudo apt install -y redis-server

# Instalacja Nginx
sudo apt install -y nginx

# Instalacja Git
sudo apt install -y git
```

### CentOS/RHEL:
```bash
# Aktualizacja systemu
sudo dnf update -y

# Dodanie repo PHP 8.3
sudo dnf install -y epel-release
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
sudo dnf module enable php:remi-8.3

# Instalacja PHP
sudo dnf install -y php php-fpm php-mysqlnd php-pgsql php-redis \
    php-curl php-dom php-mbstring php-xml php-zip \
    php-bcmath php-gd php-intl php-cli

# Pozostałe kroki podobnie jak dla Ubuntu
```

---

## 📁 Krok 2: Pobranie kodu z repozytorium

```bash
# Przejście do katalogu web serwera
cd /var/www

# Sklonowanie repozytorium
sudo git clone https://github.com/sgweb1/pethelp-v2.git pethelp

# Zmiana właściciela plików
sudo chown -R www-data:www-data pethelp
sudo chmod -R 755 pethelp

# Przejście do katalogu aplikacji
cd pethelp
```

---

## 🔐 Krok 3: Konfiguracja pliku środowiska

```bash
# Kopiowanie pliku konfiguracyjnego
cp .env.example .env

# Edycja konfiguracji
nano .env
```

### Przykładowa konfiguracja `.env`:
```bash
# === PODSTAWOWE USTAWIENIA ===
APP_NAME="PetHelp"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_TIMEZONE=Europe/Warsaw
APP_URL=https://twoja-domena.com
APP_LOCALE=pl
APP_FALLBACK_LOCALE=en

# === BAZA DANYCH ===
# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pethelp_production
DB_USERNAME=pethelp_user
DB_PASSWORD=TWOJE_SILNE_HASLO

# PostgreSQL (alternatywnie)
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=pethelp_production
# DB_USERNAME=pethelp_user
# DB_PASSWORD=TWOJE_SILNE_HASLO

# === CACHE I SESSIONS ===
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# === REDIS ===
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# === MAIL ===
MAIL_MAILER=smtp
MAIL_HOST=smtp.twoja-domena.com
MAIL_PORT=587
MAIL_USERNAME=noreply@twoja-domena.com
MAIL_PASSWORD=HASLO_EMAIL
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@twoja-domena.com
MAIL_FROM_NAME="PetHelp"

# === FILESYSTEM ===
FILESYSTEM_DISK=public
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# === BROADCASTING (Laravel Reverb) ===
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=pethelp
REVERB_APP_KEY=YOUR_REVERB_KEY
REVERB_APP_SECRET=YOUR_REVERB_SECRET
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# === PŁATNOŚCI PAYU ===
PAYU_ENVIRONMENT=secure  # secure dla produkcji, sandbox dla testów
PAYU_MERCHANT_POS_ID=YOUR_POS_ID
PAYU_SIGNATURE_KEY=YOUR_SIGNATURE_KEY
PAYU_CLIENT_ID=YOUR_CLIENT_ID
PAYU_CLIENT_SECRET=YOUR_CLIENT_SECRET

# === MAPA (LEAFLET) ===
LEAFLET_TILE_URL="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
LEAFLET_ATTRIBUTION="© OpenStreetMap contributors"

# === LOGGING ===
LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# === VITE ===
VITE_APP_NAME="${APP_NAME}"
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## 🗄️ Krok 4: Konfiguracja bazy danych

### MySQL:
```bash
# Logowanie do MySQL
sudo mysql -u root -p

# Tworzenie bazy danych i użytkownika
CREATE DATABASE pethelp_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pethelp_user'@'localhost' IDENTIFIED BY 'TWOJE_SILNE_HASLO';
GRANT ALL PRIVILEGES ON pethelp_production.* TO 'pethelp_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### PostgreSQL:
```bash
# Przełączenie na użytkownika postgres
sudo -u postgres psql

# Tworzenie bazy danych i użytkownika
CREATE DATABASE pethelp_production;
CREATE USER pethelp_user WITH PASSWORD 'TWOJE_SILNE_HASLO';
GRANT ALL PRIVILEGES ON DATABASE pethelp_production TO pethelp_user;
\q
```

---

## 📦 Krok 5: Instalacja zależności

```bash
# Instalacja zależności PHP
composer install --no-dev --optimize-autoloader

# Generowanie klucza aplikacji
php artisan key:generate

# Instalacja zależności Node.js
npm ci

# Budowanie assetów produkcyjnych
npm run build

# Optymalizacja Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔄 Krok 6: Migracje i seeding

```bash
# Uruchomienie migracji
php artisan migrate --force

# Seeding danych podstawowych (opcjonalnie)
php artisan db:seed --class=ServiceCategorySeeder
php artisan db:seed --class=PetSeeder

# Tworzenie linku do storage
php artisan storage:link
```

---

## 🌐 Krok 7: Konfiguracja Nginx

```bash
# Tworzenie konfiguracji vhost
sudo nano /etc/nginx/sites-available/pethelp
```

### Konfiguracja Nginx:
```nginx
server {
    listen 80;
    listen 443 ssl http2;
    server_name twoja-domena.com www.twoja-domena.com;
    root /var/www/pethelp/public;
    index index.php index.html;

    # SSL Configuration (zalecane)
    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Laravel application
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Laravel Reverb WebSocket (proxy)
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2?|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Security - deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /(vendor|storage|bootstrap/cache) {
        deny all;
    }

    # Redirect HTTP to HTTPS
    if ($scheme != "https") {
        return 301 https://$host$request_uri;
    }
}
```

```bash
# Aktywacja vhost
sudo ln -s /etc/nginx/sites-available/pethelp /etc/nginx/sites-enabled/

# Test konfiguracji
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

---

## 🔧 Krok 8: Konfiguracja PHP-FPM

```bash
# Edycja konfiguracji pool
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

### Zalecane ustawienia:
```ini
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.3-fpm.sock
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

# Security
php_admin_value[expose_php] = Off
php_admin_value[allow_url_fopen] = Off
php_admin_value[allow_url_include] = Off
```

```bash
# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

---

## 🚀 Krok 9: Uruchomienie Laravel Reverb

```bash
# Instalacja supervisor dla zarządzania procesami
sudo apt install -y supervisor

# Konfiguracja worker dla queue
sudo nano /etc/supervisor/conf.d/pethelp-worker.conf
```

### Konfiguracja Supervisor:
```ini
[program:pethelp-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pethelp/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pethelp/storage/logs/worker.log
stopwaitsecs=3600

[program:pethelp-reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pethelp/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/pethelp/storage/logs/reverb.log
```

```bash
# Przeładowanie Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pethelp-worker:*
sudo supervisorctl start pethelp-reverb:*
```

---

## 🔄 Krok 10: Skrypt aktualizacji

```bash
# Tworzenie skryptu deployment
nano /var/www/pethelp/deploy.sh
```

### Skrypt deploy.sh:
```bash
#!/bin/bash

echo "🚀 Rozpoczynanie deployment PetHelp..."

# Przejście do katalogu aplikacji
cd /var/www/pethelp

# Włączenie trybu maintenance
php artisan down

# Pobieranie najnowszych zmian
git pull origin master

# Instalacja/aktualizacja zależności
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migracje bazy danych
php artisan migrate --force

# Restart kolejek
php artisan queue:restart
sudo supervisorctl restart pethelp-worker:*
sudo supervisorctl restart pethelp-reverb:*

# Wyłączenie trybu maintenance
php artisan up

echo "✅ Deployment zakończony pomyślnie!"
```

```bash
# Uprawnienia do skryptu
chmod +x /var/www/pethelp/deploy.sh
```

---

## 📊 Krok 11: Monitoring i logi

### Konfiguracja logrotate:
```bash
sudo nano /etc/logrotate.d/pethelp
```

```
/var/www/pethelp/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        sudo supervisorctl restart pethelp-worker:*
    endscript
}
```

### Monitoring zdrowia aplikacji:
```bash
# Dodanie do crontab
sudo crontab -e

# Dodanie wpisu (sprawdzanie co 5 minut)
*/5 * * * * /usr/bin/curl -f http://localhost/health-check || echo "PetHelp down" | mail -s "PetHelp Alert" admin@twoja-domena.com
```

---

## 🔒 Krok 12: Bezpieczeństwo

### Firewall (UFW):
```bash
# Włączenie firewall
sudo ufw enable

# Podstawowe reguły
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow 8080  # Laravel Reverb

# Sprawdzenie statusu
sudo ufw status
```

### SSL/TLS (Let's Encrypt):
```bash
# Instalacja Certbot
sudo apt install -y certbot python3-certbot-nginx

# Generowanie certyfikatu
sudo certbot --nginx -d twoja-domena.com -d www.twoja-domena.com

# Test odnowienia
sudo certbot renew --dry-run
```

---

## ✅ Krok 13: Sprawdzenie instalacji

```bash
# Sprawdzenie statusu serwisów
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql
sudo systemctl status redis
sudo supervisorctl status

# Sprawdzenie logów
tail -f /var/www/pethelp/storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Test aplikacji
curl -I https://twoja-domena.com
```

### Lista kontrolna:
- [ ] Aplikacja odpowiada na HTTPS
- [ ] WebSocket działa (sprawdź Developer Tools → Network)
- [ ] Możliwa rejestracja nowego użytkownika
- [ ] Działają płatności PayU (w trybie testowym)
- [ ] Funkcjonuje real-time chat
- [ ] Logi nie pokazują błędów krytycznych

---

## 🔄 Aktualizacja aplikacji

```bash
# Użycie skryptu deployment
./deploy.sh

# Lub manualne kroki:
php artisan down
git pull origin master
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan queue:restart
php artisan up
```

---

## 🆘 Rozwiązywanie problemów

### Problem z uprawnieniami:
```bash
sudo chown -R www-data:www-data /var/www/pethelp
sudo chmod -R 755 /var/www/pethelp
sudo chmod -R 775 /var/www/pethelp/storage
sudo chmod -R 775 /var/www/pethelp/bootstrap/cache
```

### Problem z cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Problem z kolejkami:
```bash
sudo supervisorctl restart pethelp-worker:*
php artisan queue:restart
```

### Przydatne komendy diagnostyczne:
```bash
# Status aplikacji
php artisan about

# Sprawdzenie konfiguracji
php artisan config:show

# Test połączenia z bazą
php artisan tinker
>>> DB::connection()->getPdo();

# Test maili
php artisan mail:test admin@twoja-domena.com
```

---

## 🔐 Dane logowania do testowania

Po pomyślnej instalacji i uruchomieniu seedów, w aplikacji dostępni są następujący użytkownicy testowi:

### Opiekunowie (Sitters):
1. **Anna Kowalska**
   - Email: `anna.kowalska@example.com`
   - Hasło: `password`
   - Miasto: Warszawa
   - Usługi: Opieka nad psami w domu

2. **Marek Nowak**
   - Email: `marek.nowak@example.com`
   - Hasło: `password`
   - Miasto: Kraków
   - Usługi: Spacery z psami

3. **Katarzyna Wiśniewska**
   - Email: `katarzyna.wisniewska@example.com`
   - Hasło: `password`
   - Miasto: Gdańsk
   - Usługi: Opieka całodobowa

4. **Tomasz Kaczmarek**
   - Email: `tomasz.kaczmarek@example.com`
   - Hasło: `password`
   - Miasto: Wrocław
   - Usługi: Transport zwierząt

5. **Agnieszka Lewandowska**
   - Email: `agnieszka.lewandowska@example.com`
   - Hasło: `password`
   - Miasto: Poznań
   - Usługi: Szkolenie psów

### Właściciele zwierząt (Owners):
1. **Jan Kowalski**
   - Email: `jan.kowalski@example.com`
   - Hasło: `password`
   - Miasto: Warszawa
   - Zwierzęta: Rex (Golden Retriever), Luna (Maine Coon)

2. **Maria Nowak**
   - Email: `maria.nowak@example.com`
   - Hasło: `password`
   - Miasto: Kraków
   - Zwierzęta: Bella (Labrador)

3. **Piotr Wiśniewski**
   - Email: `piotr.wisniewski@example.com`
   - Hasło: `password`
   - Miasto: Gdańsk
   - Zwierzęta: Max (Husky), Mila (kot perski), Kiwi (papuga)

### Informacje dodatkowe:
- Wszyscy użytkownicy mają zweryfikowane adresy email (`email_verified_at`)
- Każdy opiekun ma przypisane lokalizacje i usługi zgodne z miastem
- Usługi obejmują różne kategorie: opieka w domu, spacery, transport, szkolenie
- Ceny usług wahają się od 15 do 30 zł/h i od 100 do 200 zł/dzień

### Testowanie funkcjonalności:
1. **Logowanie** - użyj dowolnego z powyższych emaili z hasłem `password`
2. **Wyszukiwanie** - wyszukaj opiekunów w różnych miastach
3. **Rezerwacje** - przetestuj proces rezerwacji usług
4. **Profile** - sprawdź profile opiekunów i ich usługi

---

## 📞 Wsparcie

W przypadku problemów sprawdź:
1. Logi Laravel: `/var/www/pethelp/storage/logs/laravel.log`
2. Logi Nginx: `/var/log/nginx/error.log`
3. Logi PHP-FPM: `/var/log/php8.3-fpm.log`
4. Status serwisów: `sudo systemctl status nazwa-serwisu`

**Pomyślnej instalacji! 🎉**