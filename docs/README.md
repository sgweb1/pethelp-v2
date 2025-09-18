# Dokumentacja PetHelp

## 📋 Spis Dokumentacji

### 🎯 Planowanie i Strategia
- [**PLAN_WDROZENIA_PETHELP.md**](PLAN_WDROZENIA_PETHELP.md) - Główny plan wdrożenia projektu z 6 etapami
- [**KLUCZOWE_FUNKCJONALNOSCI.md**](KLUCZOWE_FUNKCJONALNOSCI.md) - Lista kluczowych funkcjonalności systemu

### 🏗️ Architektura i Technologie
- [**STRUKTURA_BAZY_DANYCH.md**](STRUKTURA_BAZY_DANYCH.md) - Schemat bazy danych z relacjami
- [**TECHNICZNE_DETALE.md**](TECHNICZNE_DETALE.md) - Szczegóły techniczne implementacji

### 🎨 Interfejs Użytkownika
- [**KOMPONENTY_UI.md**](KOMPONENTY_UI.md) - Dokumentacja komponentów UI w stylu Bootstrap

### 📁 Archiwum
W katalogu `archive/` znajdują się starsze wersje dokumentacji i nieaktualne pliki:
- Stare plany i harmonogramy
- Dokumentacja nieimplementowanych funkcji
- Duplikaty i kopie zapasowe

---

## 🚀 Aktualny Status Projektu

### ✅ Ukończone (ETAP 1)
- [x] Konfiguracja Laravel 12 + MySQL + Livewire
- [x] Migracje bazy danych (10 tabel)
- [x] Modele Eloquent z relacjami
- [x] System uwierzytelniania (Laravel Breeze)
- [x] Własne komponenty UI w stylu Bootstrap (10 komponentów)
- [x] Konfiguracja Vite do obsługi komponentów

### 🔄 W trakcie (ETAP 2)
- [ ] Layout bazowy z nawigacją bazującą na mockupach
- [ ] System responsywny (mobile-first)
- [ ] Landing page zgodnie z mockupami

### 📋 Następne etapy
- **ETAP 3:** System użytkowników (właściciele + opiekunowie)
- **ETAP 4:** Wyszukiwarka opiekunów z filtrami
- **ETAP 5:** System rezerwacji i płatności
- **ETAP 6:** Płatności PayU i finalizacja

---

## 🛠️ Struktura Projektu

### Mockupy
Dostępne mockupy w katalogu `mockup/`:
- `mockup_desktop_landing.html` - Strona główna desktop
- `mockup_mobile_landing.html` - Strona główna mobile
- `mockup_desktop_dashboard.html` - Dashboard desktop
- `mockup_mobile_dashboard.html` - Dashboard mobile
- `mockup_desktop_search.html` - Wyszukiwarka desktop
- `mockup_mobile_search.html` - Wyszukiwarka mobile

### Komponenty UI
Dostępne komponenty w `resources/views/components/ui/`:
- Button, Card, Alert, Modal, Input
- Dropdown, Navbar, Badge, Accordion, Toast

### Baza danych
10 tabel z pełnymi relacjami:
- users, user_profiles, pets, services, bookings
- reviews, payments, locations, availability, notifications

---

## 📞 Dokumenty referencyjne

- **CLAUDE.md** - Instrukcje i konwencje projektu
- **mockup/** - Wzorce UI do implementacji
- **archive/** - Szczegółowa dokumentacja (20+ plików)

---

*Ostatnia aktualizacja: 2025-09-17*