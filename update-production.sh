#!/bin/bash

################################################################################
# 🔄 PetHelp Production Update Script
# Automatyczna aktualizacja aplikacji na serwerze produkcyjnym
################################################################################

set -e

# Kolory
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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

# Logo
clear
echo -e "${GREEN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║        🔄 PetHelp Production Update & Deployment             ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

separator

# Sprawdzenie czy jesteśmy w katalogu Laravel
if [ ! -f "artisan" ]; then
    error "Ten skrypt musi być uruchomiony z katalogu głównego aplikacji Laravel!"
    error "Nie znaleziono pliku 'artisan'"
    exit 1
fi

APP_DIR=$(pwd)
info "Katalog aplikacji: $APP_DIR"

# Sprawdzenie uprawnień
if [ "$EUID" -eq 0 ]; then
    warning "Skrypt uruchomiony jako root - to może nie być optymalne"
    warning "Lepiej uruchomić jako użytkownik aplikacji (www-data/nginx)"
    read -p "$(echo -e ${YELLOW}Czy kontynuować? (y/n): ${NC})" CONTINUE
    if [ "$CONTINUE" != "y" ]; then
        exit 0
    fi
fi

separator
info "Co chcesz zaktualizować?"
separator

echo "1) Pełna aktualizacja (zalecane)"
echo "2) Tylko kod (git pull)"
echo "3) Tylko zależności (composer + npm)"
echo "4) Tylko build assetów (npm run build)"
echo "5) Tylko migracje bazy danych"
echo "6) Tylko cache Laravel"
echo "7) Czyszczenie cache"
echo ""
read -p "$(echo -e ${BLUE}Wybierz opcję (1-7): ${NC})" UPDATE_OPTION

case $UPDATE_OPTION in
    1) UPDATE_TYPE="full" ;;
    2) UPDATE_TYPE="code" ;;
    3) UPDATE_TYPE="deps" ;;
    4) UPDATE_TYPE="build" ;;
    5) UPDATE_TYPE="migrate" ;;
    6) UPDATE_TYPE="cache" ;;
    7) UPDATE_TYPE="clear" ;;
    *)
        error "Nieprawidłowa opcja!"
        exit 1
        ;;
esac

separator
info "Rozpoczynam aktualizację typu: $UPDATE_TYPE"
separator

# Backup przed aktualizacją
read -p "$(echo -e ${BLUE}Czy utworzyć backup przed aktualizacją? (y/n) [y]: ${NC})" CREATE_BACKUP
CREATE_BACKUP=${CREATE_BACKUP:-y}

if [ "$CREATE_BACKUP" = "y" ]; then
    info "Tworzenie backupu..."

    BACKUP_DIR="$HOME/pethelp-backups"
    mkdir -p "$BACKUP_DIR"

    BACKUP_NAME="pethelp-backup-$(date +%Y%m%d-%H%M%S)"
    BACKUP_PATH="$BACKUP_DIR/$BACKUP_NAME"

    # Backup bazy danych
    if [ -f .env ]; then
        DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)
        DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
        DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)
        DB_TYPE=$(grep DB_CONNECTION .env | cut -d '=' -f2)

        if [ "$DB_TYPE" = "mysql" ]; then
            info "Backup bazy MySQL: $DB_NAME"
            mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_PATH-database.sql"
        elif [ "$DB_TYPE" = "pgsql" ]; then
            info "Backup bazy PostgreSQL: $DB_NAME"
            PGPASSWORD="$DB_PASS" pg_dump -U "$DB_USER" "$DB_NAME" > "$BACKUP_PATH-database.sql"
        fi

        success "Backup bazy danych: $BACKUP_PATH-database.sql"
    fi

    # Backup plików .env
    cp .env "$BACKUP_PATH.env"
    success "Backup .env: $BACKUP_PATH.env"

    # Backup storage (jeśli zawiera ważne pliki użytkownika)
    read -p "$(echo -e ${BLUE}Czy zrobić backup katalogu storage? (może być duży) (y/n) [n]: ${NC})" BACKUP_STORAGE
    BACKUP_STORAGE=${BACKUP_STORAGE:-n}

    if [ "$BACKUP_STORAGE" = "y" ]; then
        info "Backup storage (może potrwać)..."
        tar -czf "$BACKUP_PATH-storage.tar.gz" storage/
        success "Backup storage: $BACKUP_PATH-storage.tar.gz"
    fi

    success "Backup zakończony: $BACKUP_DIR/$BACKUP_NAME*"
fi

# Tryb maintenance
read -p "$(echo -e ${BLUE}Czy włączyć tryb maintenance? (y/n) [y]: ${NC})" ENABLE_MAINTENANCE
ENABLE_MAINTENANCE=${ENABLE_MAINTENANCE:-y}

if [ "$ENABLE_MAINTENANCE" = "y" ]; then
    info "Włączanie trybu maintenance..."
    php artisan down
    success "Aplikacja w trybie maintenance"
fi

################################################################################
# AKTUALIZACJA
################################################################################

separator
info "Rozpoczynam aktualizację..."
separator

# Funkcja przywracania po błędzie
cleanup_on_error() {
    error "Wystąpił błąd podczas aktualizacji!"
    if [ "$ENABLE_MAINTENANCE" = "y" ]; then
        warning "Wyłączanie trybu maintenance..."
        php artisan up
    fi
    error "Aktualizacja anulowana"
    exit 1
}

trap cleanup_on_error ERR

# Pełna aktualizacja lub kod
if [ "$UPDATE_TYPE" = "full" ] || [ "$UPDATE_TYPE" = "code" ]; then
    info "Pobieranie najnowszego kodu z repozytorium..."

    # Sprawdzenie czy są niezacommitowane zmiany
    if [ -n "$(git status --porcelain)" ]; then
        warning "Wykryto niezacommitowane zmiany!"
        git status --short
        read -p "$(echo -e ${YELLOW}Czy kontynuować? Zmiany mogą zostać utracone. (y/n): ${NC})" CONTINUE_GIT
        if [ "$CONTINUE_GIT" != "y" ]; then
            cleanup_on_error
        fi
    fi

    git fetch origin
    git pull origin master

    success "Kod zaktualizowany"
fi

# Pełna aktualizacja lub zależności
if [ "$UPDATE_TYPE" = "full" ] || [ "$UPDATE_TYPE" = "deps" ]; then
    info "Instalacja/aktualizacja zależności Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction
    success "Zależności Composer zaktualizowane"

    info "Instalacja/aktualizacja zależności npm..."
    npm ci
    success "Zależności npm zaktualizowane"
fi

# Pełna aktualizacja lub build
if [ "$UPDATE_TYPE" = "full" ] || [ "$UPDATE_TYPE" = "deps" ] || [ "$UPDATE_TYPE" = "build" ]; then
    info "Budowanie assetów produkcyjnych..."
    npm run build
    success "Assety zbudowane"
fi

# Pełna aktualizacja lub migracje
if [ "$UPDATE_TYPE" = "full" ] || [ "$UPDATE_TYPE" = "migrate" ]; then
    info "Uruchamianie migracji bazy danych..."

    # Sprawdzenie czy są nowe migracje
    PENDING_MIGRATIONS=$(php artisan migrate:status | grep -c "Pending" || echo "0")

    if [ "$PENDING_MIGRATIONS" != "0" ]; then
        warning "Znaleziono $PENDING_MIGRATIONS oczekujących migracji"
        php artisan migrate:status

        read -p "$(echo -e ${YELLOW}Czy uruchomić migracje? (y/n) [y]: ${NC})" RUN_MIGRATIONS
        RUN_MIGRATIONS=${RUN_MIGRATIONS:-y}

        if [ "$RUN_MIGRATIONS" = "y" ]; then
            php artisan migrate --force
            success "Migracje wykonane"
        else
            warning "Migracje pominięte"
        fi
    else
        info "Brak nowych migracji"
    fi
fi

# Czyszczenie cache
if [ "$UPDATE_TYPE" = "clear" ]; then
    info "Czyszczenie cache..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    success "Cache wyczyszczony"
fi

# Pełna aktualizacja lub budowanie cache
if [ "$UPDATE_TYPE" = "full" ] || [ "$UPDATE_TYPE" = "cache" ]; then
    info "Budowanie cache Laravel..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    success "Cache zbudowany"
fi

# Restart kolejek jeśli używane
if command -v supervisorctl >/dev/null 2>&1; then
    info "Restart workerów kolejek..."
    php artisan queue:restart
    sleep 2

    # Sprawdzenie czy są procesy pethelp
    if supervisorctl status | grep -q "pethelp"; then
        supervisorctl restart pethelp-worker:* 2>/dev/null || warning "Nie udało się zrestartować workerów Supervisor"
        success "Workery zrestartowane"
    fi
fi

# Opcjonalne: OPcache reset (jeśli dostępne)
if command -v cachetool >/dev/null 2>&1; then
    info "Czyszczenie OPcache..."
    cachetool opcache:reset --fcgi=/var/run/php/php8.3-fpm.sock || warning "Nie udało się wyczyścić OPcache"
fi

################################################################################
# FINALIZACJA
################################################################################

separator
info "Weryfikacja instalacji..."
separator

# Sprawdzenie uprawnień
info "Sprawdzanie uprawnień..."
WEB_USER=$(ps aux | grep -E 'nginx|httpd|apache' | grep -v root | head -1 | awk '{print $1}')

if [ -n "$WEB_USER" ]; then
    info "Użytkownik web serwera: $WEB_USER"
    # Opcjonalnie napraw uprawnienia
    # chown -R $WEB_USER:$WEB_USER storage bootstrap/cache
fi

# Test aplikacji
info "Test dostępności aplikacji..."
if command -v curl >/dev/null 2>&1; then
    APP_URL=$(grep APP_URL .env | cut -d '=' -f2)
    HTTP_CODE=$(curl -o /dev/null -s -w "%{http_code}\n" "$APP_URL" || echo "000")

    if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
        success "Aplikacja odpowiada (HTTP $HTTP_CODE)"
    else
        warning "Aplikacja zwraca kod HTTP: $HTTP_CODE"
    fi
fi

# Wyłącz tryb maintenance
if [ "$ENABLE_MAINTENANCE" = "y" ]; then
    info "Wyłączanie trybu maintenance..."
    php artisan up
    success "Aplikacja znowu dostępna"
fi

################################################################################
# PODSUMOWANIE
################################################################################

separator
echo -e "${GREEN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║             ✅ AKTUALIZACJA ZAKOŃCZONA POMYŚLNIE              ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

separator
success "PetHelp został pomyślnie zaktualizowany!"
separator

echo ""
info "📋 WYKONANE KROKI:"
echo ""

case $UPDATE_TYPE in
    "full")
        echo "  ✅ Pobrany najnowszy kod"
        echo "  ✅ Zaktualizowane zależności"
        echo "  ✅ Zbudowane assety"
        echo "  ✅ Uruchomione migracje"
        echo "  ✅ Zbudowany cache"
        ;;
    "code")
        echo "  ✅ Pobrany najnowszy kod"
        ;;
    "deps")
        echo "  ✅ Zaktualizowane zależności"
        echo "  ✅ Zbudowane assety"
        ;;
    "build")
        echo "  ✅ Zbudowane assety"
        ;;
    "migrate")
        echo "  ✅ Uruchomione migracje"
        ;;
    "cache")
        echo "  ✅ Zbudowany cache"
        ;;
    "clear")
        echo "  ✅ Wyczyszczony cache"
        ;;
esac

echo ""

if [ "$CREATE_BACKUP" = "y" ]; then
    separator
    info "📦 BACKUP:"
    echo ""
    echo "  Lokalizacja: $BACKUP_DIR/$BACKUP_NAME*"
    echo ""
    warning "Pamiętaj aby okresowo czyścić stare backupy!"
    echo ""
fi

separator
info "📊 ZALECANE SPRAWDZENIA:"
echo ""
echo "1. Sprawdź logi aplikacji:"
echo "   tail -f storage/logs/laravel.log"
echo ""
echo "2. Sprawdź logi serwera:"
echo "   tail -f /var/log/nginx/error.log"
echo ""
echo "3. Sprawdź status kolejek (jeśli używane):"
echo "   php artisan queue:monitor"
echo ""
echo "4. Przetestuj kluczowe funkcje aplikacji"
echo ""

separator
success "Aktualizacja zakończona pomyślnie! 🎉"
separator

exit 0
