# 🚀 Installation Guide - PetHelp

**Kompletny przewodnik instalacji środowiska deweloperskiego**

---

## 📋 Wymagania Systemowe

### **Backend Requirements**
- **PHP**: 8.3.16 lub nowszy
- **Composer**: 2.0 lub nowszy
- **MySQL**: 8.0 lub nowszy
- **Git**: Najnowsza wersja

### **Frontend Requirements**
- **Node.js**: 18.0 lub nowszy
- **npm**: 9.0 lub nowszy

### **Recommended Tools**
- **Laragon** (Windows) lub **Laravel Herd** (macOS/Windows)
- **VS Code** z rozszerzeniami PHP i Laravel
- **TablePlus** lub **Sequel Pro** (zarządzanie bazą danych)

---

## 🛠️ Instalacja Krok po Kroku

### **Krok 1: Klonowanie Repozytorium**
```bash
# Klonuj repozytorium
git clone https://github.com/your-org/pethelp.git
cd pethelp

# Sprawdź branch
git branch
git status
```

### **Krok 2: Instalacja Zależności Backend**
```bash
# Instalacja pakietów PHP
composer install

# W przypadku problemów:
composer install --ignore-platform-reqs
composer dump-autoload
```

### **Krok 3: Instalacja Zależności Frontend**
```bash
# Instalacja pakietów Node.js
npm install

# W przypadku błędów:
npm ci
npm audit fix
```

### **Krok 4: Konfiguracja Środowiska**
```bash
# Kopiuj plik konfiguracyjny
cp .env.example .env

# Generuj klucz aplikacji
php artisan key:generate

# Edytuj .env file
# Ustaw parametry bazy danych:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pethelp
# DB_USERNAME=root
# DB_PASSWORD=
```

### **Krok 5: Konfiguracja Bazy Danych**
```bash
# Utwórz bazę danych 'pethelp' w MySQL

# Uruchom migracje i seedy
php artisan migrate:fresh --seed

# Sprawdź czy wszystko działa
php artisan migrate:status
```

### **Krok 6: Build Assetów Frontend**
```bash
# Development build
npm run dev

# Production build
npm run build

# Watch mode (rozwój)
npm run dev &
```

### **Krok 7: Uruchomienie Aplikacji**

#### **Opcja A: Laragon (Rekomendowane dla Windows)**
1. Uruchom Laragon
2. Skopiuj projekt do `C:\laragon\www\pethelp`
3. Aplikacja dostępna na: `http://pethelp.test`

#### **Opcja B: Artisan Serve**
```bash
# Uruchom serwer deweloperski
php artisan serve

# Aplikacja dostępna na: http://localhost:8000
```

---

## ✅ Weryfikacja Instalacji

### **Sprawdź czy wszystko działa:**
```bash
# Test aplikacji
php artisan test

# Sprawdź routing
php artisan route:list

# Sprawdź status migracji
php artisan migrate:status

# Sprawdź konfigurację
php artisan config:show

# Test bazy danych
php artisan tinker
>>> App\Models\User::count();
```

### **Sprawdź frontend:**
```bash
# Sprawdź czy Vite działa
npm run dev

# Sprawdź build
npm run build

# Test w przeglądarce
# Przejdź do http://pethelp.test lub http://localhost:8000
```

---

## 🔧 Konfiguracja Środowiska Deweloperskiego

### **1. VS Code Extensions (Rekomendowane)**
```json
// Zainstaluj rozszerzenia:
{
    "extensions": [
        "bmewburn.vscode-intelephense-client",
        "MehediDracula.php-namespace-resolver",
        "onecentlin.laravel-blade",
        "amiralizadeh9480.laravel-extra-intellisense",
        "codingyu.laravel-goto-view",
        "ryannaddy.laravel-artisan"
    ]
}
```

### **2. Ustawienia .env dla Development**
```env
# Development Environment
APP_NAME="PetHelp"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_TIMEZONE=Europe/Warsaw
APP_URL=http://pethelp.test

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pethelp
DB_USERNAME=root
DB_PASSWORD=

# Cache & Sessions
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail (Development)
MAIL_MAILER=log

# Debugging
LOG_CHANNEL=daily
LOG_LEVEL=debug
```

### **3. Git Configuration**
```bash
# Ustaw informacje użytkownika
git config user.name "Your Name"
git config user.email "your.email@example.com"

# Ustaw Git hooks
chmod +x .git/hooks/post-commit
```

### **4. Debugging Tools**
```bash
# Uruchom monitor logów Laravel + JavaScript
node log-monitor.cjs &

# Monitor dokumentacji
./docs-monitor.sh

# Laravel Telescope (opcjonalnie)
# composer require laravel/telescope --dev
# php artisan telescope:install
```

---

## 📊 Narzędzia Pomocnicze

### **Artisan Commands**
```bash
# Dokumentacja
php artisan docs:status          # Status dokumentacji
php artisan docs:generate        # Generuj dokumentację

# Development
php artisan make:livewire Component
php artisan make:model ModelName -m
php artisan make:controller ApiController --api

# Testing
php artisan test                 # Uruchom testy
php artisan test --coverage     # Z pokryciem kodu

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **NPM Scripts**
```bash
npm run dev          # Development watch
npm run build        # Production build
npm run preview      # Preview production build
```

---

## 🚨 Rozwiązywanie Problemów

### **Problem 1: Błędy Composer**
```bash
# Błąd z platform requirements
composer install --ignore-platform-reqs

# Błąd z autoload
composer dump-autoload

# Błąd z cache
composer clear-cache
```

### **Problem 2: Błędy NPM**
```bash
# Wyczyść cache npm
npm cache clean --force

# Usuń node_modules i zainstaluj ponownie
rm -rf node_modules package-lock.json
npm install

# Problem z uprawnieniami (Linux/Mac)
sudo chown -R $USER ~/.npm
```

### **Problem 3: Błędy Bazy Danych**
```bash
# Sprawdź połączenie
php artisan tinker
>>> DB::connection()->getPdo();

# Reset migracji
php artisan migrate:fresh --seed

# Sprawdź czy tabele istnieją
php artisan db:table users
```

### **Problem 4: Błędy Permissions (Linux/Mac)**
```bash
# Ustaw uprawnienia dla storage i cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### **Problem 5: Błędy Vite**
```bash
# Sprawdź czy port 5173 jest wolny
lsof -ti:5173

# Wyczyść cache Vite
rm -rf node_modules/.vite

# Sprawdź konfigurację w vite.config.js
```

---

## 📝 Następne Kroki

Po pomyślnej instalacji:

1. **Przeczytaj**: [Environment Setup](environment.md)
2. **Poznaj**: [Architecture Overview](../architecture/overview.md)
3. **Zacznij**: [Coding Conventions](../development/conventions.md)
4. **Test**: [Testing Guide](../development/testing.md)

---

## 🆘 Pomoc

### Jeśli masz problemy:
1. Sprawdź [Troubleshooting](#-rozwiązywanie-problemów)
2. Sprawdź logi: `tail -f storage/logs/laravel.log`
3. Uruchom: `node log-monitor.cjs` dla live debugging
4. Skontaktuj się z zespołem lub otwórz issue

---

*📅 Ostatnia aktualizacja: 2025-09-24*
*🤖 Zarządzane przez: Documentation Specialist Agent*