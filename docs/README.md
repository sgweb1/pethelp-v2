# 📚 Dokumentacja PetHelp

**Kompleksowa dokumentacja platformy opieki nad zwierzętami**
*Laravel 12 + Livewire 3 + Tailwind CSS*

---

## 🧭 Nawigacja Dokumentacji

### 🔧 [Dokumentacja Deweloperska](dev/)
**Dla programistów i zespołu technicznego**

| Kategoria | Opis | Pliki |
|-----------|------|-------|
| **Setup** | Instalacja i konfiguracja | [Installation Guide](dev/setup/installation.md) |
| **Architecture** | Architektura systemu | [Database Schema](dev/architecture/database-schema.md) |
| **Development** | Rozwój aplikacji | [Coding Standards](dev/development/conventions.md) |
| **Reference** | Dokumentacja referencyjna | [API Endpoints](dev/reference/api-endpoints.md) |

### 👥 [Dokumentacja Użytkownika](user/)
**Dla użytkowników końcowych platformy**

| Kategoria | Opis | Pliki |
|-----------|------|-------|
| **Getting Started** | Pierwsze kroki | [Registration Guide](user/getting-started/registration.md) |
| **Features** | Funkcjonalności | [Pet Management](user/features/pet-management.md) |
| **Guides** | Przewodniki | [Owner's Guide](user/guides/pet-owner-guide.md) |
| **Support** | Pomoc i wsparcie | [FAQ](user/support/faq.md) |

---

## 🎯 Dokumentacja Projektu (Legacy)

### 📋 Planowanie i Strategia
- [**PLAN_WDROZENIA_PETHELP.md**](PLAN_WDROZENIA_PETHELP.md) - Główny plan wdrożenia projektu z 6 etapami
- [**KLUCZOWE_FUNKCJONALNOSCI.md**](KLUCZOWE_FUNKCJONALNOSCI.md) - Lista kluczowych funkcjonalności systemu
- [**BUSINESS_STRUCTURE.md**](BUSINESS_STRUCTURE.md) - Struktura biznesowa aplikacji

### 🏗️ Architektura i Technologie
- [**STRUKTURA_BAZY_DANYCH.md**](STRUKTURA_BAZY_DANYCH.md) - Schemat bazy danych z relacjami
- [**TECHNICZNE_DETALE.md**](TECHNICZNE_DETALE.md) - Szczegóły techniczne implementacji
- [**UNIFIED_MAP_ARCHITECTURE.md**](UNIFIED_MAP_ARCHITECTURE.md) - Architektura systemu map
- [**COMPONENT_STRUCTURE.md**](COMPONENT_STRUCTURE.md) - Struktura komponentów

### 🎨 Interfejs Użytkownika
- [**KOMPONENTY_UI.md**](KOMPONENTY_UI.md) - Dokumentacja komponentów UI
- [**HOME_PAGE_ANALYSIS.md**](HOME_PAGE_ANALYSIS.md) - Analiza strony głównej
- [**SVG_STANDARDS.md**](SVG_STANDARDS.md) - Standardy SVG

### 🔧 API i Integracje
- [**API_REFERENCE.md**](API_REFERENCE.md) - Dokumentacja API
- [**SUBSCRIPTION_SYSTEM.md**](SUBSCRIPTION_SYSTEM.md) - System subskrypcji
- [**MODUL_WYDARZENIA.md**](MODUL_WYDARZENIA.md) - Moduł wydarzeń

### 📁 Archiwum
W katalogu [**archive/**](archive/) znajdują się starsze wersje dokumentacji i nieaktualne pliki

---

## 🚀 Aktualny Status Projektu

### ✅ Ukończone (ETAP 1 & 2)
- [x] **Fundament** - Laravel 12 + MySQL + Livewire 3 + Tailwind
- [x] **Baza danych** - Pełna struktura z relacjami (25+ tabel)
- [x] **Uwierzytelnianie** - Laravel Breeze + role system
- [x] **Komponenty UI** - System komponentów w stylu Bootstrap
- [x] **Dashboard** - Responsywny dashboard z funkcjonalnościami
- [x] **Zarządzanie zwierzętami** - Profile zwierząt z galerią
- [x] **System usług** - Tworzenie i zarządzanie usługami opieki
- [x] **Wyszukiwarka** - Zaawansowana wyszukiwarka z mapą
- [x] **Kalendarz dostępności** - System rezerwacji terminów

### 🔄 W trakcie (ETAP 3)
- [ ] **System płatności** - Integracja PayU
- [ ] **Komunikator** - Chat między użytkownikami
- [ ] **System ocen** - Opinie i recenzje
- [ ] **Powiadomienia** - System notyfikacji push
- [ ] **Mobile optimization** - Finalne dopracowanie mobile

### 📋 Kolejne etapy
- **ETAP 4:** Zaawansowane funkcje (geolokalizacja, AI matching)
- **ETAP 5:** Monetyzacja i subskrypcje premium
- **ETAP 6:** Skalowanie i optymalizacja wydajności

---

## 🛠️ Struktura Dokumentacji

```
docs/
├── README.md                    # Ten plik - główna nawigacja
├── dev/                         # 🔧 Dokumentacja deweloperska
│   ├── setup/                   # Instalacja i konfiguracja
│   ├── architecture/            # Architektura systemu
│   ├── development/             # Proces rozwoju
│   └── reference/               # Dokumentacja referencyjna
├── user/                        # 👥 Dokumentacja użytkownika
│   ├── getting-started/         # Pierwsze kroki
│   ├── features/                # Funkcjonalności
│   ├── guides/                  # Przewodniki
│   └── support/                 # Pomoc techniczna
├── assets/                      # 📎 Zasoby dokumentacji
│   ├── diagrams/                # Diagramy i schematy
│   ├── screenshots/             # Zrzuty ekranu
│   └── mockups/                 # Mockupy interfejsu
├── archive/                     # 📁 Archiwum dokumentacji
└── [Legacy files]               # Istniejące pliki projektu
```

---

## 🤖 Documentation Specialist Agent

**Automatyzacja dokumentacji:**
Utworzony został specjalistyczny [**Documentation Specialist Agent**](../.claude/agents/documentation-specialist.md) który:

- ✅ **Automatycznie wykrywa** zmiany wymagające aktualizacji dokumentacji
- ✅ **Generuje dokumentację** API endpoints, komponentów Livewire, modeli
- ✅ **Utrzymuje synchronizację** między kodem a dokumentacją
- ✅ **Monitoruje jakość** dokumentacji i wskazuje braki
- ✅ **Tworzy przewodniki** użytkownika na podstawie funkcjonalności

### Jak korzystać z agenta:
```bash
# Sprawdź status dokumentacji
php artisan docs:status

# Wygeneruj brakującą dokumentację
php artisan docs:generate --missing

# Zaktualizuj dokumentację po zmianach
./docs-monitor.sh
```

---

## 📞 Jak korzystać z dokumentacji

### 👨‍💻 **Dla deweloperów:**
1. Zacznij od [**Setup Guide**](dev/setup/installation.md)
2. Przeczytaj [**Architecture Overview**](dev/architecture/overview.md)
3. Sprawdź [**API Reference**](dev/reference/api-endpoints.md)
4. Skorzystaj z [**Development Guidelines**](dev/development/conventions.md)

### 👤 **Dla użytkowników:**
1. Rozpocznij od [**Registration Guide**](user/getting-started/registration.md)
2. Poznaj [**Key Features**](user/features/pet-management.md)
3. Przeczytaj [**User Guides**](user/guides/pet-owner-guide.md)
4. W razie problemów: [**FAQ**](user/support/faq.md) i [**Support**](user/support/contact.md)

### 🔍 **Szukasz czegoś konkretnego?**
- **API endpoints** → [dev/reference/api-endpoints.md](dev/reference/api-endpoints.md)
- **Komponenty UI** → [dev/reference/ui-components.md](dev/reference/ui-components.md)
- **Baza danych** → [dev/architecture/database-schema.md](dev/architecture/database-schema.md)
- **Błędy i problemy** → [user/support/troubleshooting.md](user/support/troubleshooting.md)

---

## 📈 Wskaźniki Dokumentacji

| Kategoria | Status | Coverage |
|-----------|--------|----------|
| **API Endpoints** | 🟡 Częściowe | ~60% |
| **Livewire Components** | 🟡 Częściowe | ~45% |
| **Database Models** | 🟢 Dobre | ~80% |
| **User Guides** | 🔴 Początkowe | ~25% |
| **Setup Guides** | 🟢 Kompletne | ~90% |

---

*📅 Ostatnia aktualizacja: 2025-09-24*
*🤖 Zarządzane przez: Documentation Specialist Agent*