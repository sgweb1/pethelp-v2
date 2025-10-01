#!/bin/bash

################################################################################
# 🚀 PetHelp Production Installation Script
# Automatyczny skrypt instalacji aplikacji na serwerze produkcyjnym
################################################################################

set -e  # Zatrzymaj przy pierwszym błędzie

# Kolory dla lepszej czytelności
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funkcje pomocnicze
info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

success() {
    echo -e "${GREEN}✅ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

error() {
    echo -e "${RED}❌ $1${NC}"
}

separator() {
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

# Funkcja sprawdzająca czy polecenie jest dostępne
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Logo
clear
echo -e "${GREEN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║        ██████╗ ███████╗████████╗██╗  ██╗███████╗██╗          ║
║        ██╔══██╗██╔════╝╚══██╔══╝██║  ██║██╔════╝██║          ║
║        ██████╔╝█████╗     ██║   ███████║█████╗  ██║          ║
║        ██╔═══╝ ██╔══╝     ██║   ██╔══██║██╔══╝  ██║          ║
║        ██║     ███████╗   ██║   ██║  ██║███████╗███████╗     ║
║        ╚═╝     ╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚══════╝     ║
║                                                               ║
║           Production Installation & Deployment Script        ║
║                       Version 1.0.0                          ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

separator

# Sprawdzenie uprawnień root
if [[ $EUID -ne 0 ]]; then
   error "Ten skrypt musi być uruchomiony jako root (sudo)"
   exit 1
fi

success "Skrypt uruchomiony z uprawnieniami root"

################################################################################
# KROK 1: ZBIERANIE INFORMACJI
################################################################################
separator
info "KROK 1: Zbieranie informacji o konfiguracji"
separator

# Domena
read -p "$(echo -e ${BLUE}Podaj domenę aplikacji (np. pethelp.pl): ${NC})" APP_DOMAIN
APP_DOMAIN=${APP_DOMAIN:-pethelp.pl}

# Dodatkowa subdomena www?
read -p "$(echo -e ${BLUE}Czy dodać subdomenę www.$APP_DOMAIN? (y/n): ${NC})" ADD_WWW
ADD_WWW=${ADD_WWW:-y}

# Nazwa bazy danych
read -p "$(echo -e ${BLUE}Nazwa bazy danych [pethelp_production]: ${NC})" DB_NAME
DB_NAME=${DB_NAME:-pethelp_production}

# Użytkownik bazy danych
read -p "$(echo -e ${BLUE}Użytkownik bazy danych [pethelp_user]: ${NC})" DB_USER
DB_USER=${DB_USER:-pethelp_user}

# Hasło bazy danych
read -sp "$(echo -e ${BLUE}Hasło do bazy danych (zostaw puste dla losowego): ${NC})" DB_PASSWORD
echo
if [ -z "$DB_PASSWORD" ]; then
    DB_PASSWORD=$(openssl rand -base64 32)
    info "Wygenerowano losowe hasło do bazy danych"
fi

# Email administratora
read -p "$(echo -e ${BLUE}Email administratora [admin@$APP_DOMAIN]: ${NC})" ADMIN_EMAIL
ADMIN_EMAIL=${ADMIN_EMAIL:-admin@$APP_DOMAIN}

# Katalog instalacji
read -p "$(echo -e ${BLUE}Katalog instalacji [/var/www/pethelp]: ${NC})" INSTALL_DIR
INSTALL_DIR=${INSTALL_DIR:-/var/www/pethelp}

# Typ bazy danych
read -p "$(echo -e ${BLUE}Typ bazy danych (mysql/pgsql) [mysql]: ${NC})" DB_TYPE
DB_TYPE=${DB_TYPE:-mysql}

# Czy zainstalować Redis?
read -p "$(echo -e ${BLUE}Czy zainstalować Redis dla cache i kolejek? (y/n) [n]: ${NC})" INSTALL_REDIS
INSTALL_REDIS=${INSTALL_REDIS:-n}

# Czy zainstalować Supervisor?
read -p "$(echo -e ${BLUE}Czy zainstalować Supervisor dla kolejek i WebSocket? (y/n) [y]: ${NC})" INSTALL_SUPERVISOR
INSTALL_SUPERVISOR=${INSTALL_SUPERVISOR:-y}

# Czy zainstalować SSL z Let's Encrypt?
read -p "$(echo -e ${BLUE}Czy zainstalować certyfikat SSL z Let's Encrypt? (y/n) [y]: ${NC})" INSTALL_SSL
INSTALL_SSL=${INSTALL_SSL:-y}

# PayU - tryb
read -p "$(echo -e ${BLUE}Tryb PayU (sandbox/secure) [sandbox]: ${NC})" PAYU_ENV
PAYU_ENV=${PAYU_ENV:-sandbox}

# Czy uruchomić seedy testowe?
read -p "$(echo -e ${BLUE}Czy załadować dane testowe? (y/n) [n]: ${NC})" LOAD_TEST_DATA
LOAD_TEST_DATA=${LOAD_TEST_DATA:-n}

separator
info "Podsumowanie konfiguracji:"
echo -e "  Domena:              ${GREEN}$APP_DOMAIN${NC}"
if [ "$ADD_WWW" = "y" ]; then
    echo -e "  Subdomena WWW:       ${GREEN}www.$APP_DOMAIN${NC}"
fi
echo -e "  Katalog:             ${GREEN}$INSTALL_DIR${NC}"
echo -e "  Baza danych:         ${GREEN}$DB_TYPE${NC}"
echo -e "  Nazwa bazy:          ${GREEN}$DB_NAME${NC}"
echo -e "  Użytkownik bazy:     ${GREEN}$DB_USER${NC}"
echo -e "  Redis:               ${GREEN}$([ "$INSTALL_REDIS" = "y" ] && echo "TAK" || echo "NIE")${NC}"
echo -e "  Supervisor:          ${GREEN}$([ "$INSTALL_SUPERVISOR" = "y" ] && echo "TAK" || echo "NIE")${NC}"
echo -e "  SSL (Let's Encrypt): ${GREEN}$([ "$INSTALL_SSL" = "y" ] && echo "TAK" || echo "NIE")${NC}"
echo -e "  PayU Environment:    ${GREEN}$PAYU_ENV${NC}"
separator

read -p "$(echo -e ${YELLOW}Czy kontynuować instalację? (y/n): ${NC})" CONFIRM
if [ "$CONFIRM" != "y" ]; then
    error "Instalacja anulowana przez użytkownika"
    exit 0
fi

################################################################################
# KROK 2: DETEKCJA SYSTEMU OPERACYJNEGO
################################################################################
separator
info "KROK 2: Wykrywanie systemu operacyjnego"
separator

if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$NAME
    OS_VERSION=$VERSION_ID
    success "Wykryto: $OS $OS_VERSION"
else
    error "Nie można wykryć systemu operacyjnego"
    exit 1
fi

# Ustalenie package managera
if command_exists apt-get; then
    PKG_MANAGER="apt"
    PKG_UPDATE="apt update -y"
    PKG_UPGRADE="apt upgrade -y"
    PKG_INSTALL="apt install -y"
elif command_exists dnf; then
    PKG_MANAGER="dnf"
    PKG_UPDATE="dnf check-update || true"
    PKG_UPGRADE="dnf upgrade -y"
    PKG_INSTALL="dnf install -y"
elif command_exists yum; then
    PKG_MANAGER="yum"
    PKG_UPDATE="yum check-update || true"
    PKG_UPGRADE="yum update -y"
    PKG_INSTALL="yum install -y"
else
    error "Nieobsługiwany package manager"
    exit 1
fi

success "Package manager: $PKG_MANAGER"

################################################################################
# KROK 3: AKTUALIZACJA SYSTEMU
################################################################################
separator
info "KROK 3: Aktualizacja systemu"
separator

info "Aktualizacja listy pakietów..."
$PKG_UPDATE

info "Aktualizacja zainstalowanych pakietów..."
$PKG_UPGRADE

success "System zaktualizowany"

################################################################################
# KROK 4: INSTALACJA PODSTAWOWYCH NARZĘDZI
################################################################################
separator
info "KROK 4: Instalacja podstawowych narzędzi"
separator

BASIC_PACKAGES="curl wget git unzip software-properties-common"

if [ "$PKG_MANAGER" = "apt" ]; then
    $PKG_INSTALL $BASIC_PACKAGES
elif [ "$PKG_MANAGER" = "dnf" ] || [ "$PKG_MANAGER" = "yum" ]; then
    $PKG_INSTALL epel-release
    $PKG_INSTALL $BASIC_PACKAGES
fi

success "Podstawowe narzędzia zainstalowane"

################################################################################
# KROK 5: INSTALACJA PHP 8.3
################################################################################
separator
info "KROK 5: Instalacja PHP 8.3 i rozszerzeń"
separator

if [ "$PKG_MANAGER" = "apt" ]; then
    if ! command_exists php || ! php -v | grep -q "8.3"; then
        info "Dodawanie repozytorium PHP..."
        $PKG_INSTALL software-properties-common
        add-apt-repository ppa:ondrej/php -y
        apt update -y

        info "Instalacja PHP 8.3..."
        $PKG_INSTALL php8.3 php8.3-fpm php8.3-cli php8.3-common \
            php8.3-mysql php8.3-pgsql php8.3-redis php8.3-curl \
            php8.3-dom php8.3-mbstring php8.3-xml php8.3-zip \
            php8.3-bcmath php8.3-gd php8.3-intl

        success "PHP 8.3 zainstalowane"
    else
        success "PHP 8.3 już zainstalowane"
    fi
elif [ "$PKG_MANAGER" = "dnf" ]; then
    if ! command_exists php || ! php -v | grep -q "8.3"; then
        info "Dodawanie repozytorium Remi..."
        $PKG_INSTALL https://rpms.remirepo.net/enterprise/remi-release-9.rpm
        dnf module reset php -y
        dnf module enable php:remi-8.3 -y

        info "Instalacja PHP 8.3..."
        $PKG_INSTALL php php-fpm php-cli php-common \
            php-mysqlnd php-pgsql php-redis php-curl \
            php-dom php-mbstring php-xml php-zip \
            php-bcmath php-gd php-intl

        success "PHP 8.3 zainstalowane"
    else
        success "PHP 8.3 już zainstalowane"
    fi
fi

# Sprawdzenie wersji PHP
PHP_VERSION=$(php -v | head -n 1)
info "Zainstalowana wersja: $PHP_VERSION"

################################################################################
# KROK 6: INSTALACJA COMPOSER
################################################################################
separator
info "KROK 6: Instalacja Composer"
separator

if ! command_exists composer; then
    info "Pobieranie Composer..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    success "Composer zainstalowany"
else
    success "Composer już zainstalowany"
    composer self-update
fi

composer --version

################################################################################
# KROK 7: INSTALACJA NODE.JS I NPM
################################################################################
separator
info "KROK 7: Instalacja Node.js 20.x i npm"
separator

if ! command_exists node || ! node -v | grep -q "v20"; then
    info "Instalacja Node.js 20.x..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    $PKG_INSTALL nodejs
    success "Node.js zainstalowany"
else
    success "Node.js już zainstalowany"
fi

node -v
npm -v

################################################################################
# KROK 8: INSTALACJA BAZY DANYCH
################################################################################
separator
info "KROK 8: Instalacja i konfiguracja bazy danych ($DB_TYPE)"
separator

if [ "$DB_TYPE" = "mysql" ]; then
    if ! command_exists mysql; then
        info "Instalacja MySQL Server..."
        $PKG_INSTALL mysql-server
        systemctl start mysql
        systemctl enable mysql
        success "MySQL zainstalowany"
    else
        success "MySQL już zainstalowany"
    fi

    # Tworzenie bazy i użytkownika
    info "Tworzenie bazy danych i użytkownika..."
    mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
    mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';" 2>/dev/null || true
    mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
    success "Baza danych MySQL skonfigurowana"

elif [ "$DB_TYPE" = "pgsql" ]; then
    if ! command_exists psql; then
        info "Instalacja PostgreSQL..."
        $PKG_INSTALL postgresql postgresql-contrib
        systemctl start postgresql
        systemctl enable postgresql
        success "PostgreSQL zainstalowany"
    else
        success "PostgreSQL już zainstalowany"
    fi

    # Tworzenie bazy i użytkownika
    info "Tworzenie bazy danych i użytkownika..."
    sudo -u postgres psql -c "CREATE DATABASE $DB_NAME;" 2>/dev/null || true
    sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASSWORD';" 2>/dev/null || true
    sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"
    success "Baza danych PostgreSQL skonfigurowana"
fi

################################################################################
# KROK 9: INSTALACJA REDIS (OPCJONALNIE)
################################################################################
if [ "$INSTALL_REDIS" = "y" ]; then
    separator
    info "KROK 9: Instalacja Redis"
    separator

    if ! command_exists redis-cli; then
        info "Instalacja Redis Server..."
        $PKG_INSTALL redis
        systemctl start redis
        systemctl enable redis
        success "Redis zainstalowany"
    else
        success "Redis już zainstalowany"
    fi

    redis-cli ping
fi

################################################################################
# KROK 10: INSTALACJA NGINX
################################################################################
separator
info "KROK 10: Instalacja i konfiguracja Nginx"
separator

if ! command_exists nginx; then
    info "Instalacja Nginx..."
    $PKG_INSTALL nginx
    systemctl start nginx
    systemctl enable nginx
    success "Nginx zainstalowany"
else
    success "Nginx już zainstalowany"
fi

nginx -v

################################################################################
# KROK 11: KLONOWANIE REPOZYTORIUM
################################################################################
separator
info "KROK 11: Klonowanie aplikacji z repozytorium"
separator

if [ -d "$INSTALL_DIR" ]; then
    warning "Katalog $INSTALL_DIR już istnieje"
    read -p "$(echo -e ${YELLOW}Czy usunąć istniejący katalog? (y/n): ${NC})" REMOVE_DIR
    if [ "$REMOVE_DIR" = "y" ]; then
        rm -rf "$INSTALL_DIR"
        info "Stary katalog usunięty"
    else
        error "Nie można kontynuować instalacji - katalog już istnieje"
        exit 1
    fi
fi

info "Klonowanie repozytorium..."
git clone https://github.com/sgweb1/pethelp-v2.git "$INSTALL_DIR"

cd "$INSTALL_DIR"
success "Repozytorium sklonowane do $INSTALL_DIR"

################################################################################
# KROK 12: KONFIGURACJA PLIKU .ENV
################################################################################
separator
info "KROK 12: Konfiguracja pliku środowiska (.env)"
separator

if [ ! -f .env.example ]; then
    error "Brak pliku .env.example w repozytorium!"
    exit 1
fi

cp .env.example .env
info "Skopiowano .env.example do .env"

# Generowanie klucza aplikacji
APP_KEY=$(php artisan key:generate --show)

# Konfiguracja .env
info "Konfiguracja zmiennych środowiskowych..."

sed -i "s|APP_NAME=.*|APP_NAME=PetHelp|g" .env
sed -i "s|APP_ENV=.*|APP_ENV=production|g" .env
sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|g" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|g" .env
sed -i "s|APP_URL=.*|APP_URL=https://$APP_DOMAIN|g" .env
sed -i "s|ASSET_URL=.*|ASSET_URL=https://$APP_DOMAIN|g" .env
sed -i "s|APP_LOCALE=.*|APP_LOCALE=pl|g" .env
sed -i "s|APP_TIMEZONE=.*|APP_TIMEZONE=Europe/Warsaw|g" .env

# Baza danych
sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=$DB_TYPE|g" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|g" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|g" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|g" .env

# Cache i kolejki
if [ "$INSTALL_REDIS" = "y" ]; then
    sed -i "s|CACHE_STORE=.*|CACHE_STORE=redis|g" .env
    sed -i "s|SESSION_DRIVER=.*|SESSION_DRIVER=redis|g" .env
    sed -i "s|QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|g" .env

    # Dodaj konfigurację Redis jeśli jej nie ma
    if ! grep -q "REDIS_HOST" .env; then
        echo "" >> .env
        echo "# Redis Configuration" >> .env
        echo "REDIS_HOST=127.0.0.1" >> .env
        echo "REDIS_PASSWORD=null" >> .env
        echo "REDIS_PORT=6379" >> .env
    fi
else
    sed -i "s|CACHE_STORE=.*|CACHE_STORE=file|g" .env
    sed -i "s|SESSION_DRIVER=.*|SESSION_DRIVER=database|g" .env
    sed -i "s|QUEUE_CONNECTION=.*|QUEUE_CONNECTION=database|g" .env
fi

# Logi
sed -i "s|LOG_CHANNEL=.*|LOG_CHANNEL=daily|g" .env
sed -i "s|LOG_LEVEL=.*|LOG_LEVEL=error|g" .env

# PayU
sed -i "s|PAYU_ENVIRONMENT=.*|PAYU_ENVIRONMENT=$PAYU_ENV|g" .env

# Debugbar wyłączony na produkcji
sed -i "s|DEBUGBAR_ENABLED=.*|DEBUGBAR_ENABLED=false|g" .env

success "Plik .env skonfigurowany"

################################################################################
# KROK 13: INSTALACJA ZALEŻNOŚCI
################################################################################
separator
info "KROK 13: Instalacja zależności PHP i JavaScript"
separator

info "Instalacja zależności Composer (może potrwać kilka minut)..."
composer install --no-dev --optimize-autoloader --no-interaction

info "Instalacja zależności npm (może potrwać kilka minut)..."
npm ci

info "Budowanie assetów produkcyjnych..."
npm run build

success "Wszystkie zależności zainstalowane"

################################################################################
# KROK 14: USTAWIENIE UPRAWNIEŃ
################################################################################
separator
info "KROK 14: Ustawienie uprawnień do plików"
separator

# Ustalenie użytkownika web serwera
WEB_USER="www-data"
if [ "$PKG_MANAGER" = "dnf" ] || [ "$PKG_MANAGER" = "yum" ]; then
    WEB_USER="nginx"
fi

chown -R $WEB_USER:$WEB_USER "$INSTALL_DIR"
chmod -R 755 "$INSTALL_DIR"
chmod -R 775 "$INSTALL_DIR/storage"
chmod -R 775 "$INSTALL_DIR/bootstrap/cache"

success "Uprawnienia ustawione dla użytkownika: $WEB_USER"

################################################################################
# KROK 15: KONFIGURACJA LARAVEL
################################################################################
separator
info "KROK 15: Konfiguracja Laravel"
separator

# Link do storage
info "Tworzenie linku symbolicznego do storage..."
php artisan storage:link

# Migracje
info "Uruchamianie migracji bazy danych..."
php artisan migrate --force

# Seedy (opcjonalnie)
if [ "$LOAD_TEST_DATA" = "y" ]; then
    info "Ładowanie danych testowych..."
    php artisan db:seed --class=ServiceCategorySeeder --force
    php artisan db:seed --class=ExtendedTestDataSeeder --force
    success "Dane testowe załadowane"
fi

# Cache
info "Budowanie cache Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

success "Laravel skonfigurowany"

################################################################################
# KROK 16: KONFIGURACJA NGINX
################################################################################
separator
info "KROK 16: Konfiguracja Nginx Virtual Host"
separator

NGINX_CONF="/etc/nginx/sites-available/pethelp"
NGINX_ENABLED="/etc/nginx/sites-enabled/pethelp"

# Dla CentOS/RHEL (brak sites-available/sites-enabled)
if [ ! -d "/etc/nginx/sites-available" ]; then
    mkdir -p /etc/nginx/sites-available
    mkdir -p /etc/nginx/sites-enabled

    # Dodaj include do nginx.conf jeśli nie istnieje
    if ! grep -q "sites-enabled" /etc/nginx/nginx.conf; then
        sed -i '/http {/a \    include /etc/nginx/sites-enabled/*;' /etc/nginx/nginx.conf
    fi
fi

info "Tworzenie konfiguracji Nginx..."

cat > "$NGINX_CONF" << EOF
server {
    listen 80;
    server_name $APP_DOMAIN$([ "$ADD_WWW" = "y" ] && echo " www.$APP_DOMAIN" || echo "");
    root $INSTALL_DIR/public;
    index index.php index.html;

    # Dodatkowe nagłówki bezpieczeństwa
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    # Kompresja gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Laravel
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
    }

    # Cache dla plików statycznych
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2?|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Bezpieczeństwo - blokada dostępu do wrażliwych katalogów
    location ~ /\. {
        deny all;
    }

    location ~ /(vendor|storage|bootstrap/cache) {
        deny all;
    }
}
EOF

# Aktywacja vhost
ln -sf "$NGINX_CONF" "$NGINX_ENABLED"

# Test konfiguracji Nginx
if nginx -t; then
    success "Konfiguracja Nginx prawidłowa"
    systemctl reload nginx
    success "Nginx przeładowany"
else
    error "Błąd w konfiguracji Nginx!"
    exit 1
fi

################################################################################
# KROK 17: INSTALACJA SSL (OPCJONALNIE)
################################################################################
if [ "$INSTALL_SSL" = "y" ]; then
    separator
    info "KROK 17: Instalacja certyfikatu SSL (Let's Encrypt)"
    separator

    if ! command_exists certbot; then
        info "Instalacja Certbot..."
        $PKG_INSTALL certbot python3-certbot-nginx
        success "Certbot zainstalowany"
    else
        success "Certbot już zainstalowany"
    fi

    info "Generowanie certyfikatu SSL..."
    info "UWAGA: Domena $APP_DOMAIN musi być skierowana na ten serwer!"

    if [ "$ADD_WWW" = "y" ]; then
        certbot --nginx -d "$APP_DOMAIN" -d "www.$APP_DOMAIN" --non-interactive --agree-tos --email "$ADMIN_EMAIL" --redirect
    else
        certbot --nginx -d "$APP_DOMAIN" --non-interactive --agree-tos --email "$ADMIN_EMAIL" --redirect
    fi

    if [ $? -eq 0 ]; then
        success "Certyfikat SSL zainstalowany"

        # Test automatycznego odnowienia
        certbot renew --dry-run
        success "Automatyczne odnawianie certyfikatu skonfigurowane"
    else
        warning "Nie udało się zainstalować certyfikatu SSL"
        warning "Możesz spróbować później poleceniem: certbot --nginx -d $APP_DOMAIN"
    fi
fi

################################################################################
# KROK 18: KONFIGURACJA SUPERVISOR (OPCJONALNIE)
################################################################################
if [ "$INSTALL_SUPERVISOR" = "y" ]; then
    separator
    info "KROK 18: Instalacja i konfiguracja Supervisor"
    separator

    if ! command_exists supervisorctl; then
        info "Instalacja Supervisor..."
        $PKG_INSTALL supervisor
        systemctl start supervisor
        systemctl enable supervisor
        success "Supervisor zainstalowany"
    else
        success "Supervisor już zainstalowany"
    fi

    info "Tworzenie konfiguracji worker dla kolejek..."

    # Ustaw typ kolejki w zależności od tego czy Redis jest zainstalowany
    if [ "$INSTALL_REDIS" = "y" ]; then
        QUEUE_TYPE="redis"
    else
        QUEUE_TYPE="database"
    fi

    cat > /etc/supervisor/conf.d/pethelp-worker.conf << EOF
[program:pethelp-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $INSTALL_DIR/artisan queue:work $QUEUE_TYPE --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=$WEB_USER
numprocs=2
redirect_stderr=true
stdout_logfile=$INSTALL_DIR/storage/logs/worker.log
stopwaitsecs=3600
EOF

    success "Konfiguracja Supervisor utworzona"

    # Przeładowanie Supervisor
    supervisorctl reread
    supervisorctl update
    supervisorctl start pethelp-worker:*

    success "Supervisor workers uruchomione"
fi

################################################################################
# KROK 19: KONFIGURACJA FIREWALL
################################################################################
separator
info "KROK 19: Konfiguracja firewall (UFW)"
separator

if command_exists ufw; then
    info "Konfiguracja UFW..."

    # Włącz porty
    ufw allow ssh
    ufw allow 'Nginx Full'

    # Włącz UFW
    echo "y" | ufw enable

    success "Firewall skonfigurowany"
    ufw status
else
    warning "UFW nie jest zainstalowane - pomiń konfigurację firewall"
fi

################################################################################
# KROK 20: KONFIGURACJA CRON
################################################################################
separator
info "KROK 20: Konfiguracja Cron dla Laravel Scheduler"
separator

# Dodaj Laravel scheduler do crontab
(crontab -l 2>/dev/null | grep -v "$INSTALL_DIR"; echo "* * * * * cd $INSTALL_DIR && php artisan schedule:run >> /dev/null 2>&1") | crontab -

success "Laravel Scheduler dodany do crontab"

################################################################################
# PODSUMOWANIE
################################################################################
separator
echo -e "${GREEN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║                   ✅ INSTALACJA ZAKOŃCZONA                    ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

separator
success "PetHelp został pomyślnie zainstalowany!"
separator

echo ""
info "📋 PODSUMOWANIE INSTALACJI:"
echo ""
echo -e "  🌐 Aplikacja:           ${GREEN}https://$APP_DOMAIN${NC}"
echo -e "  📁 Katalog:             ${GREEN}$INSTALL_DIR${NC}"
echo -e "  🗄️  Baza danych:         ${GREEN}$DB_TYPE - $DB_NAME${NC}"
echo -e "  👤 Użytkownik bazy:     ${GREEN}$DB_USER${NC}"
echo -e "  🔑 Hasło bazy:          ${YELLOW}$DB_PASSWORD${NC} ${RED}(ZAPISZ TO HASŁO!)${NC}"
echo -e "  📧 Email admin:         ${GREEN}$ADMIN_EMAIL${NC}"
echo -e "  🔒 SSL:                 ${GREEN}$([ "$INSTALL_SSL" = "y" ] && echo "Zainstalowany (Let's Encrypt)" || echo "Nie zainstalowany")${NC}"
echo -e "  💾 Redis:               ${GREEN}$([ "$INSTALL_REDIS" = "y" ] && echo "Zainstalowany" || echo "Nie zainstalowany")${NC}"
echo -e "  ⚙️  Supervisor:          ${GREEN}$([ "$INSTALL_SUPERVISOR" = "y" ] && echo "Zainstalowany" || echo "Nie zainstalowany")${NC}"
echo ""

separator
info "📝 NASTĘPNE KROKI:"
echo ""
echo "1. Skonfiguruj DNS dla domeny $APP_DOMAIN aby wskazywała na ten serwer"
echo "2. Skonfiguruj PayU w pliku .env jeśli chcesz używać płatności:"
echo "   - Edytuj: nano $INSTALL_DIR/.env"
echo "   - Ustaw: PAYU_MERCHANT_ID, PAYU_SECRET_KEY, PAYU_OAUTH_CLIENT_ID, PAYU_OAUTH_CLIENT_SECRET"
echo "3. Skonfiguruj wysyłkę email w .env (MAIL_*)"
echo "4. Sprawdź czy aplikacja działa: https://$APP_DOMAIN"
echo "5. Zmień hasło administratora w aplikacji"
echo ""

if [ "$LOAD_TEST_DATA" = "y" ]; then
    separator
    info "🧪 DANE TESTOWE:"
    echo ""
    echo "Zaloguj się używając jednego z testowych użytkowników:"
    echo ""
    echo -e "  Email:  ${GREEN}anna.kowalska@example.com${NC}"
    echo -e "  Hasło:  ${GREEN}password${NC}"
    echo ""
    echo "Pełna lista użytkowników testowych dostępna w dokumentacji."
    echo ""
fi

separator
info "📚 PRZYDATNE KOMENDY:"
echo ""
echo "  Sprawdź status serwisów:"
echo "    systemctl status nginx"
echo "    systemctl status php8.3-fpm"
echo "    systemctl status mysql    # lub postgresql"
if [ "$INSTALL_REDIS" = "y" ]; then
    echo "    systemctl status redis"
fi
if [ "$INSTALL_SUPERVISOR" = "y" ]; then
    echo "    supervisorctl status"
fi
echo ""
echo "  Logi aplikacji:"
echo "    tail -f $INSTALL_DIR/storage/logs/laravel.log"
echo "    tail -f /var/log/nginx/error.log"
echo ""
echo "  Aktualizacja aplikacji:"
echo "    cd $INSTALL_DIR"
echo "    git pull origin master"
echo "    composer install --no-dev --optimize-autoloader"
echo "    npm ci && npm run build"
echo "    php artisan migrate --force"
echo "    php artisan config:cache"
echo "    php artisan route:cache"
echo "    php artisan view:cache"
if [ "$INSTALL_SUPERVISOR" = "y" ]; then
    echo "    supervisorctl restart pethelp-worker:*"
fi
echo ""

separator
success "Dziękujemy za wybranie PetHelp! 🐾"
separator

# Zapisz podsumowanie do pliku
SUMMARY_FILE="$INSTALL_DIR/INSTALLATION_SUMMARY.txt"
{
    echo "═══════════════════════════════════════════════════════════════"
    echo "PetHelp - Podsumowanie instalacji"
    echo "Data instalacji: $(date)"
    echo "═══════════════════════════════════════════════════════════════"
    echo ""
    echo "KONFIGURACJA:"
    echo "  Domena:             $APP_DOMAIN"
    echo "  Katalog:            $INSTALL_DIR"
    echo "  Baza danych:        $DB_TYPE"
    echo "  Nazwa bazy:         $DB_NAME"
    echo "  Użytkownik bazy:    $DB_USER"
    echo "  Hasło bazy:         $DB_PASSWORD"
    echo "  Email admin:        $ADMIN_EMAIL"
    echo "  SSL:                $([ "$INSTALL_SSL" = "y" ] && echo "Tak" || echo "Nie")"
    echo "  Redis:              $([ "$INSTALL_REDIS" = "y" ] && echo "Tak" || echo "Nie")"
    echo "  Supervisor:         $([ "$INSTALL_SUPERVISOR" = "y" ] && echo "Tak" || echo "Nie")"
    echo ""
    echo "═══════════════════════════════════════════════════════════════"
    echo "UWAGA: Ten plik zawiera poufne dane - usuń go lub zabezpiecz!"
    echo "═══════════════════════════════════════════════════════════════"
} > "$SUMMARY_FILE"

chmod 600 "$SUMMARY_FILE"
info "Podsumowanie zapisane w: $SUMMARY_FILE"

echo ""
warning "WAŻNE: Hasło do bazy danych zostało zapisane w pliku $SUMMARY_FILE"
warning "Zabezpiecz ten plik lub usuń go po zapisaniu danych!"
echo ""

exit 0
