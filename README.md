# PetHelp - Platforma Opieki nad Zwierzętami

**Stack:** Laravel 12 + MySQL + Livewire 3 + Tailwind CSS + Alpine.js

## 🚀 Szybki Start

### 💻 Rozwój lokalny

1. **Instalacja:**
   ```bash
   composer install
   npm install && npm run build
   php artisan key:generate
   ```

2. **Baza danych:**
   ```bash
   # Utwórz bazę 'pethelp' w MySQL
   php artisan migrate:fresh
   ```

3. **Uruchom:**
   ```bash
   # Opcja 1: Laragon (automatyczna domena)
   http://pethelp.test

   # Opcja 2: Artisan serve
   php artisan serve
   http://localhost:8000
   ```

### 🌐 Wdrożenie produkcyjne

**Automatyczna instalacja jednym poleceniem:**

```bash
curl -O https://raw.githubusercontent.com/sgweb1/pethelp-v2/master/install-production.sh
chmod +x install-production.sh
sudo ./install-production.sh
```

Skrypt automatycznie:
- ✅ Zainstaluje PHP 8.3, MySQL, Nginx, Node.js
- ✅ Skonfiguruje bazę danych
- ✅ Zainstaluje certyfikat SSL (Let's Encrypt)
- ✅ Skonfiguruje Supervisor dla kolejek
- ✅ Uruchomi aplikację

**📖 Dokumentacja wdrożenia:**
- [📘 Przewodnik Wdrożenia](DEPLOYMENT_GUIDE.md) - Szybki start i FAQ
- [📕 Szczegółowa Instrukcja](INSTRUKCJA_INSTALACJI_PRODUKCJA.md) - Pełna dokumentacja

**🔄 Aktualizacja:**
```bash
cd /var/www/pethelp
./update-production.sh
```

## ✨ Funkcjonalności

### ✅ Zaimplementowane (ETAP 1)
- 🗄️ **Baza danych** - 10 tabel z pełnymi relacjami
- 🔐 **Uwierzytelnianie** - Laravel Breeze
- 🎨 **Komponenty UI** - 10 komponentów w stylu Bootstrap
- ⚙️ **Vite** - konfiguracja do obsługi komponentów

### 🔄 W trakcie (ETAP 2)
- 📱 **Layout responsywny** - mobile-first
- 🏠 **Landing page** - bazująca na mockupach
- 🧭 **Nawigacja** - zgodna z UX mockupów

### ✅ Zaimplementowane (ETAP 2)
- 🐾 **Zarządzanie zwierzętami** - profile zwierząt z pełnymi danymi medycznymi
- 📅 **Moduł "Spotkajmy się"** - system wydarzeń i spotkań dla właścicieli zwierząt

### 📋 Planowane
- 👥 **System użytkowników** - rozbudowa profili właścicieli i opiekunów
- 🔍 **Wyszukiwarka** - znajdź opiekuna w okolicy
- 📅 **Rezerwacje** - system bookingu usług opieki
- 💳 **Płatności** - integracja z PayU
- ⭐ **Oceny** - system opinii i ocen

## 🏗️ Struktura Projektu

```
├── app/Models/                    # Modele Eloquent z relacjami
├── database/migrations/           # 10 tabel bazy danych
├── resources/
│   ├── views/components/ui/       # 10 komponentów UI
│   ├── views/layouts/            # Layout aplikacji
│   └── js/components.js          # JavaScript dla komponentów
├── docs/                         # Dokumentacja
│   ├── README.md                 # Główna dokumentacja
│   ├── KOMPONENTY_UI.md          # Przewodnik po komponentach
│   └── archive/                  # Archiwum (20+ plików)
├── mockup/                       # 6 mockupów HTML
├── CLAUDE.md                     # Konwencje projektu
└── README.md                     # Ten plik
```

## 🎨 Komponenty UI

Dostępne komponenty w `resources/views/components/ui/`:

| Komponent | Opis | Użycie |
|-----------|------|--------|
| `button` | Przyciski z wariantami | `<x-ui.button variant="primary">` |
| `card` | Karty z header/footer | `<x-ui.card>` |
| `alert` | Powiadomienia | `<x-ui.alert type="success">` |
| `modal` | Okna modalne | `<x-ui.modal id="test">` |
| `input` | Pola formularza | `<x-ui.input label="Email">` |
| `dropdown` | Menu rozwijane | `<x-ui.dropdown>` |
| `navbar` | Nawigacja | `<x-ui.navbar>` |
| `badge` | Etykiety | `<x-ui.badge variant="info">` |
| `accordion` | Rozwijane sekcje | `<x-ui.accordion>` |
| `toast` | Powiadomienia toast | `showToast(message, type)` |

📖 **Pełna dokumentacja:** [docs/KOMPONENTY_UI.md](docs/KOMPONENTY_UI.md)

## 📊 Status Implementacji

| Etap | Status | Opis |
|------|--------|------|
| **ETAP 1** | ✅ 100% | Fundament (Laravel + Livewire + UI) |
| **ETAP 2** | 🔄 60% | Landing page + Layout responsywny |
| **ETAP 3** | ⏳ 0% | System użytkowników |
| **ETAP 4** | ⏳ 0% | Wyszukiwarka opiekunów |
| **ETAP 5** | ⏳ 0% | System rezerwacji |
| **ETAP 6** | ⏳ 0% | Płatności i finalizacja |

## 📚 Dokumentacja

- 📖 [**docs/README.md**](docs/README.md) - Główna dokumentacja
- 🎯 [**docs/PLAN_WDROZENIA_PETHELP.md**](docs/PLAN_WDROZENIA_PETHELP.md) - Plan 6 etapów
- 🎨 [**docs/KOMPONENTY_UI.md**](docs/KOMPONENTY_UI.md) - Przewodnik po komponentach
- 🗄️ [**docs/STRUKTURA_BAZY_DANYCH.md**](docs/STRUKTURA_BAZY_DANYCH.md) - Schemat bazy
- 📅 [**docs/MODUL_WYDARZENIA.md**](docs/MODUL_WYDARZENIA.md) - Dokumentacja modułu wydarzeń
- 📁 [**docs/archive/**](docs/archive/) - Archiwum dokumentacji

## 🎯 Mockupy

W katalogu `mockup/` znajdują się wzorce UI:
- Landing page (desktop + mobile)
- Dashboard (desktop + mobile)
- Wyszukiwarka (desktop + mobile)

---

**🐾 PetHelp** - Łączymy właścicieli zwierząt z zaufanymi opiekunami