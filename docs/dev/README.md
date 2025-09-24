# 🔧 Dokumentacja Deweloperska - PetHelp

**Kompleksowa dokumentacja techniczna dla zespołu deweloperskiego**

---

## 📋 Spis Treści

### 🚀 Setup & Installation
- [**Installation Guide**](setup/installation.md) - Pełna instalacja środowiska deweloperskiego
- [**Environment Setup**](setup/environment.md) - Konfiguracja środowiska i zmiennych
- [**Database Setup**](setup/database.md) - Konfiguracja bazy danych i migracji

### 🏗️ Architecture & Design
- [**Architecture Overview**](architecture/overview.md) - Przegląd architektury systemu
- [**Database Schema**](architecture/database-schema.md) - Schemat bazy danych i relacje
- [**API Structure**](architecture/api-structure.md) - Struktura API i konwencje
- [**Component Architecture**](architecture/components.md) - Architektura komponentów Livewire

### 💻 Development Guidelines
- [**Coding Conventions**](development/conventions.md) - Standardy i konwencje kodowania
- [**Testing Guide**](development/testing.md) - Przewodnik testowania
- [**Debugging Guide**](development/debugging.md) - Narzędzia i techniki debugowania
- [**Deployment Guide**](development/deployment.md) - Proces wdrażania

### 📖 Reference Documentation
- [**API Endpoints**](reference/api-endpoints.md) - Dokumentacja wszystkich endpoints API
- [**Livewire Components**](reference/ui-components.md) - Dokumentacja komponentów Livewire
- [**Database Models**](reference/database-models.md) - Dokumentacja modeli Eloquent
- [**Services**](reference/services.md) - Dokumentacja serwisów aplikacji

---

## 🛠️ Quick Start dla Deweloperów

### 1. **Pierwsza instalacja**
```bash
# Sklonuj repozytorium
git clone https://github.com/your-org/pethelp.git
cd pethelp

# Instalacja zależności
composer install
npm install

# Konfiguracja
cp .env.example .env
php artisan key:generate

# Baza danych
php artisan migrate:fresh --seed
```

### 2. **Środowisko deweloperskie**
```bash
# Uruchom serwer deweloperski
php artisan serve &
npm run dev &

# Monitor logów
node log-monitor.cjs &

# Monitor dokumentacji
./docs-monitor.sh
```

### 3. **Przydatne komendy**
```bash
# Status dokumentacji
php artisan docs:status

# Generowanie dokumentacji
php artisan docs:generate --missing

# Testowanie
php artisan test --coverage

# Formatowanie kodu
./vendor/bin/pint
```

---

## 📊 Wskaźniki Projektu

### Stack Technologiczny
- **Backend**: PHP 8.3.16, Laravel 12
- **Frontend**: Livewire 3, Alpine.js 3, Tailwind CSS 3
- **Database**: MySQL 8.0
- **Tools**: Vite, Laravel Pint, Pest Testing

### Coverage Stats
| Kategoria | Status | Progress |
|-----------|--------|----------|
| **API Endpoints** | 🟡 Częściowe | ~60% |
| **Livewire Components** | 🟡 W trakcie | ~45% |
| **Models** | 🟢 Dobre | ~80% |
| **Tests** | 🟡 Rozwój | ~55% |

---

## 🚨 Ważne Informacje

### ⚠️ **Przed każdym kodem:**
1. Przeczytaj [**CLAUDE.md**](../../CLAUDE.md) - zawiera kluczowe konwencje projektu
2. Sprawdź [**Coding Conventions**](development/conventions.md)
3. Uruchom testy: `php artisan test`
4. Sprawdź linting: `./vendor/bin/pint --test`

### 🔍 **Debugging Tools:**
- **Laravel Logs**: `tail -f storage/logs/laravel.log`
- **JS Errors**: Dostępne przez `http://pethelp.test/logs/js`
- **Enhanced Monitor**: `node log-monitor.cjs`
- **Database**: Laravel Telescope (jeśli zainstalowany)

### 🤖 **Documentation Specialist Agent:**
System automatycznie monitoruje zmiany w kodzie i sugeruje aktualizacje dokumentacji. Po każdym commit sprawdzaj komunikaty o wymaganych aktualizacjach docs.

---

## 🧭 Nawigacja dla Deweloperów

### 🆕 **Nowy developer?**
1. Start → [Installation Guide](setup/installation.md)
2. Przeczytaj → [Architecture Overview](architecture/overview.md)
3. Zapoznaj się → [Coding Conventions](development/conventions.md)
4. Testuj → [Testing Guide](development/testing.md)

### 🔧 **Pracujesz nad API?**
- [API Structure](architecture/api-structure.md)
- [API Endpoints Reference](reference/api-endpoints.md)
- [Testing API](development/testing.md#api-testing)

### 🎨 **Pracujesz nad frontendem?**
- [Component Architecture](architecture/components.md)
- [UI Components Reference](reference/ui-components.md)
- [Livewire Best Practices](development/conventions.md#livewire)

### 🗄️ **Pracujesz z bazą danych?**
- [Database Schema](architecture/database-schema.md)
- [Models Reference](reference/database-models.md)
- [Migration Guidelines](development/conventions.md#migrations)

---

## 🆘 Pomoc i Wsparcie

### 📝 **Dokumentacja nieaktualna?**
```bash
# Sprawdź status dokumentacji
php artisan docs:status

# Zaktualizuj automatycznie
php artisan docs:generate --missing

# Sprawdź co wymaga aktualizacji
./docs-monitor.sh
```

### 🐛 **Problem z kodem?**
1. Sprawdź [Debugging Guide](development/debugging.md)
2. Przeszukaj [istniejące issues](../../docs/dev/reference/)
3. Użyj narzędzi debugowania z [CLAUDE.md](../../CLAUDE.md)

### 📖 **Brakuje dokumentacji?**
Documentation Specialist Agent automatycznie wykrywa braki i generuje podstawową dokumentację. Uzupełnij szczegóły ręcznie.

---

*📅 Ostatnia aktualizacja: 2025-09-24*
*🤖 Zarządzane przez: Documentation Specialist Agent*
*👥 Zespół: PetHelp Development Team*