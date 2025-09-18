# Kluczowe Funkcjonalności PetHelp

*Na podstawie analizy archiwum dokumentacji*

## 🎯 Model Biznesowy

### Monetyzacja (BEZ prowizji)
- **Plan Darmowy**: 3 ogłoszenia, podstawowy profil
- **Plan Premium** (49 PLN/mc): nieograniczone ogłoszenia + wyróżnienia
- **Plan Pro** (99 PLN/mc): wszystko + automatyzacja + API

### Dodatkowe Usługi
- **Weryfikacja podstawowa**: 29 PLN
- **Weryfikacja rozszerzona**: 79 PLN
- **Boost ogłoszenia**: 19 PLN za 7 dni

## 🐕 System Zarządzania Zwierzętami

### Model Pet (rozszerzony)
- **6 kategorii**: podstawowe, medyczne, szczepienia, kontakty, zachowanie, opieka
- **Historia medyczna**: wizyty, diagnozy, follow-up
- **System szczepień**: monitorowanie dat ważności, powiadomienia
- **Kontakty awaryjne**: hierarchia priorytetów, dostępność 24/7
- **Profil behawioralny**: osobowość, kompatybilność, poziom energii
- **Instrukcje opieki**: żywienie, aktywność, pielęgnacja

### Dodatkowe Modele
- **MedicalRecord**: wizyty weterynaryjne, koszty, follow-up
- **Vaccination**: szczepienia z datami wygaśnięcia
- **EmergencyContact**: kontakty awaryjne z priorytetami

## 👥 System Użytkowników

### Role
- **owner**: właściciel zwierząt
- **sitter**: opiekun zwierząt
- **both**: oba role
- **admin**: administrator

### Profile Opiekunów
- **Szczegółowe profile**: bio, doświadczenie, usługi, ceny
- **System dostępności**: is_available toggle
- **Filtry zaawansowane**: lokalizacja, typ usługi, cena, oceny
- **Integracja z ocenami**: ratings system

## 💬 System Komunikacji

### Funkcjonalności
- **Prywatne rozmowy**: 1-na-1 między właścicielem a opiekunem
- **Kontekst rezerwacji**: rozmowy powiązane z bookingiem
- **Załączniki**: zdjęcia i pliki (max 10MB)
- **Oznaczanie przeczytane**: tracking nieprzeczytanych
- **Edycja wiadomości**: 15-minutowe okno
- **Soft delete**: usuwanie wiadomości

### Modele
- **Conversation**: organizacja rozmów
- **Message**: pojedyncze wiadomości
- **MessageAttachment**: załączniki

## 📅 System Rezerwacji

### Status Flow
```
pending → confirmed → in_progress → completed
                   ↘ cancelled ↙
```

### Funkcjonalności
- **Zarządzanie dostępnością**: kalendarze opiekunów
- **Potwierdzanie/odrzucanie**: system decyzji
- **Tracking statusu**: pełny lifecycle
- **Automatyczne powiadomienia**: email + in-app

## 💳 System Płatności PayU

### Architektura
```
Booking → Order → Payment (PayU) → Potwierdzenie
```

### Funkcjonalności
- **Tworzenie płatności**: dla rezerwacji
- **Sprawdzanie statusu**: real-time monitoring
- **Potwierdzanie płatności**: WAITING_FOR_CONFIRMATION
- **Anulowanie/zwroty**: pełna obsługa
- **Webhook handling**: automatyczne aktualizacje

### Modele
- **Order**: implementuje PayuOrderInterface
- **Payment**: obsługa PayU
- **PaymentStatus**: tracking stanów

## ⭐ System Ocen

### Funkcjonalności
- **Dwukierunkowe oceny**: właściciel ↔ opiekun
- **Oceny 1-5**: numeryczne + komentarze tekstowe
- **Moderacja**: system zgłoszeń i weryfikacji
- **Agregacja**: średnie oceny, statystyki
- **Timeline**: chronologia opinii

### Bezpieczeństwo
- **Tylko po rezerwacji**: oceny tylko po ukończonych bookingach
- **Jednorazowe**: jedna ocena per rezerwacja
- **Moderacja treści**: anti-spam, inappropriate content

## 🛡️ System Moderacji

### Funkcjonalności
- **Panel administracyjny**: zarządzanie treścią
- **System zgłoszeń**: report system
- **Weryfikacja użytkowników**: dokumenty, tożsamość
- **Moderacja opinii**: review moderation
- **Banowanie/suspension**: user management

## 📱 Powiadomienia

### Typy
- **Email notifications**: rejestracja, rezerwacje, płatności
- **In-app notifications**: real-time updates
- **Push notifications**: mobilne (przyszłość)
- **SMS**: opcjonalne (przyszłość)

### Triggery
- Nowa rezerwacja, zmiana statusu, nowa wiadomość
- Przypomnienia o szczepieniach, follow-up medyczne
- Promocje, aktualizacje systemu

## 🔍 Wyszukiwarka Opiekunów

### Filtry
- **Lokalizacja**: promień od adresu
- **Typ usługi**: spacery, opieka w domu, hotel
- **Cena**: zakres cenowy
- **Oceny**: minimalna ocena
- **Dostępność**: daty, godziny
- **Typ zwierzęcia**: psy, koty, inne

### Sorting
- Odległość, ocena, cena, popularność
- Ostatnia aktywność, data dołączenia

**Status: GOTOWE DO IMPLEMENTACJI**