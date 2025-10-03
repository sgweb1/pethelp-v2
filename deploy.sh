#!/bin/bash

################################################################################
# PETHELP DEPLOYMENT SCRIPT
# Skrypt automatycznego wdrożenia dla serwera produkcyjnego
#
# Użycie: ./deploy.sh
# Autor: PetHelp Team
# Data: 2025-10-03
################################################################################

set -e  # Zatrzymaj na błędzie

# Kolory dla outputu
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funkcje pomocnicze
print_header() {
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}\n"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# Sprawdź czy jesteśmy w katalogu aplikacji
if [ ! -f "artisan" ]; then
    print_error "Nie znaleziono pliku 'artisan'. Upewnij się, że jesteś w katalogu głównym aplikacji Laravel."
    exit 1
fi

# Start
print_header "🚀 PETHELP DEPLOYMENT SCRIPT"
print_info "Rozpoczynam wdrożenie aplikacji..."
echo ""

################################################################################
# KROK 1: WŁĄCZ TRYB MAINTENANCE
################################################################################
print_header "KROK 1/10: Włączanie trybu konserwacji"
php artisan down --retry=60 --render="errors::503" || {
    print_warning "Nie udało się włączyć trybu konserwacji (może być już włączony)"
}
print_success "Tryb konserwacji włączony"

################################################################################
# KROK 2: GIT PULL
################################################################################
print_header "KROK 2/10: Pobieranie najnowszych zmian z repozytorium"
git pull origin master || {
    print_error "Nie udało się pobrać zmian z Git"
    php artisan up
    exit 1
}
print_success "Zmiany pobrane z Git"

################################################################################
# KROK 3: COMPOSER INSTALL
################################################################################
print_header "KROK 3/10: Instalacja zależności Composer"
composer install --no-dev --optimize-autoloader --no-interaction || {
    print_error "Nie udało się zainstalować zależności Composer"
    php artisan up
    exit 1
}
print_success "Zależności Composer zainstalowane"

################################################################################
# KROK 4: NPM INSTALL
################################################################################
print_header "KROK 4/10: Instalacja zależności NPM"
npm ci || {
    print_error "Nie udało się zainstalować zależności NPM"
    php artisan up
    exit 1
}
print_success "Zależności NPM zainstalowane"

################################################################################
# KROK 5: BUILD ASSETÓW
################################################################################
print_header "KROK 5/10: Budowanie assetów frontendu (może potrwać 2-5 min)"
npm run build || {
    print_error "Nie udało się zbudować assetów"
    php artisan up
    exit 1
}
print_success "Assety zbudowane"

################################################################################
# KROK 6: MIGRACJE BAZY DANYCH
################################################################################
print_header "KROK 6/10: Uruchamianie migracji bazy danych"

# Sprawdź czy są nowe migracje
if php artisan migrate:status | grep -q "Pending"; then
    print_info "Znaleziono nowe migracje. Uruchamiam..."
    php artisan migrate --force || {
        print_error "Błąd podczas migracji bazy danych"
        php artisan up
        exit 1
    }
    print_success "Migracje wykonane pomyślnie"
else
    print_info "Brak nowych migracji do wykonania"
fi

################################################################################
# KROK 7: STORAGE LINK
################################################################################
print_header "KROK 7/10: Tworzenie linku do storage"
php artisan storage:link || {
    print_warning "Link do storage już istnieje lub nie udało się go utworzyć"
}
print_success "Link do storage sprawdzony"

################################################################################
# KROK 8: CZYSZCZENIE CACHE
################################################################################
print_header "KROK 8/10: Czyszczenie starego cache"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
print_success "Stary cache wyczyszczony"

################################################################################
# KROK 9: BUDOWANIE CACHE
################################################################################
print_header "KROK 9/10: Budowanie nowego cache"
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Nowy cache zbudowany"

################################################################################
# KROK 10: UPRAWNIENIA
################################################################################
print_header "KROK 10/10: Ustawianie uprawnień"
chmod -R 775 storage
chmod -R 775 bootstrap/cache
print_success "Uprawnienia ustawione"

################################################################################
# WYŁĄCZ TRYB MAINTENANCE
################################################################################
print_header "FINALIZACJA: Wyłączanie trybu konserwacji"
php artisan up
print_success "Tryb konserwacji wyłączony"

################################################################################
# PODSUMOWANIE
################################################################################
echo ""
print_header "✅ WDROŻENIE ZAKOŃCZONE POMYŚLNIE!"
echo ""
print_success "Aplikacja PetHelp została zaktualizowana i działa!"
echo ""
print_info "Sprawdź logi w razie problemów:"
echo "  tail -100 storage/logs/laravel.log"
echo ""
print_info "URL aplikacji:"
echo "  https://pethelp.pro-linuxpl.com"
echo ""
print_info "Panel administracyjny:"
echo "  https://pethelp.pro-linuxpl.com/admin"
echo ""

# Opcjonalnie: Pokaż ostatnie 10 linii logów Laravel
if [ -f "storage/logs/laravel.log" ]; then
    print_header "📋 Ostatnie wpisy w logach Laravel:"
    tail -10 storage/logs/laravel.log || print_warning "Brak logów"
fi

echo ""
print_success "Deployment zakończony o $(date '+%Y-%m-%d %H:%M:%S')"
echo ""

exit 0
