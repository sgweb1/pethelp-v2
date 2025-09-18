# 🚀 Quick Testing Guide - PetHelp

Szybki przewodnik po uruchamianiu testów w aplikacji PetHelp.

## ⚡ Szybki Start

### Backend (Laravel)
```bash
# Wszystkie testy
php artisan test

# Z pokryciem
php artisan test --coverage

# Tylko konkretną grupę
php artisan test --filter=Feature
php artisan test --filter=Unit
```

### Frontend (Vue)
```bash
# Wszystkie testy  
npm run test

# Jednokrotne uruchomienie
npm run test:run

# Z interfejsem UI
npm run test:ui
```

### Wszystko naraz
```bash
# Linux/Mac
./scripts/test.sh

# Windows
scripts\test.bat
```

## 📊 Co testujemy?

### ✅ Backend (43 testy)
- **PetController** - CRUD zwierząt, autoryzacja, upload zdjęć
- **LanguageController** - przełączanie języków PL/EN
- **Pet Model** - relacje, business logic, casting

### ✅ Frontend (20 testów)
- **LanguageSwitcher** - komponent przełącznika języków
- **useTranslations** - composable do tłumaczeń

## 🐛 Debugowanie

### Backend
```php
// Debug w testach
$this->dump($response->json());
$this->assertDatabaseHas('pets', ['name' => 'Buddy']);
```

### Frontend  
```typescript
// Debug komponentów
console.log(wrapper.html())
console.log(wrapper.vm.$data)
```

## 🔧 Konfiguracja

### Wymagania
- PHP 8.3+
- Node.js 20+
- SQLite (dla testów)

### Setup
```bash
# Backend
composer install
php artisan key:generate

# Frontend  
npm install --legacy-peer-deps
```

## 📈 CI/CD

Automatyczne testy uruchamiają się na:
- Push do `main` i `develop`
- Pull Requests

Status: [GitHub Actions Badge]

---

**Pełna dokumentacja:** `TESTING.md`