# System szczegółowych profili zwierząt - Dokumentacja

## Przegląd systemu

System szczegółowych profili zwierząt to kompleksowe rozwiązanie do zarządzania informacjami o pupilach w aplikacji PetHelp. System umożliwia przechowywanie i zarządzanie wszystkimi aspektami opieki nad zwierzętami, od podstawowych informacji po szczegółową historię medyczną.

## Architektura systemu

### Backend (Laravel)

#### Modele

**Pet (rozszerzony)**
- Lokalizacja: `app/Models/Pet.php`
- Główny model zwierzęcia z rozszerzonymi informacjami
- Nowe pola: `birth_date`, `gender`, `weight`, `color`, `markings`, `behavioral_notes`, itp.
- Relationships: `medicalRecords`, `vaccinations`, `emergencyContacts`

**MedicalRecord**
- Lokalizacja: `app/Models/MedicalRecord.php`
- Zarządzanie historią medyczną i wizytami weterynaryjnymi
- Pola: `visit_date`, `type`, `diagnosis`, `treatment`, `cost`, `follow_up_date`
- Scopes: `needingFollowUp()`, `recentForPet()`, `byType()`

**Vaccination**
- Lokalizacja: `app/Models/Vaccination.php`
- System zarządzania szczepieniami z datami wygaśnięcia
- Pola: `vaccine_name`, `vaccination_date`, `expiration_date`, `is_required`
- Scopes: `expiringSoon()`, `expired()`, `current()`

**EmergencyContact**
- Lokalizacja: `app/Models/EmergencyContact.php`
- Kontakty awaryjne z systemem priorytetów
- Pola: `type`, `name`, `phone`, `priority_order`, `available_24h`
- Scopes: `primary()`, `available24h()`, `byType()`

#### Kontrolery

**MedicalRecordController**
- Lokalizacja: `app/Http/Controllers/MedicalRecordController.php`
- CRUD operacje dla zapisów medycznych
- Obsługa upload plików (faktury/dokumenty)
- Zarządzanie follow-up wizytami

**VaccinationController**
- Lokalizacja: `app/Http/Controllers/VaccinationController.php`
- CRUD operacje dla szczepień
- System monitorowania dat wygaśnięcia
- Obsługa certyfikatów szczepień

**EmergencyContactController**
- Lokalizacja: `app/Http/Controllers/EmergencyContactController.php`
- CRUD operacje dla kontaktów awaryjnych
- System zarządzania priorytetami
- Filtrowanie po typach kontaktów

#### Migracje bazy danych

**2025_09_07_133145_create_medical_records_table.php**
```sql
- id, pet_id, visit_date, type, veterinarian_name
- clinic_name, diagnosis, treatment, medications
- cost, follow_up_date, follow_up_completed
- invoice_file, notes, timestamps
```

**2025_09_07_133157_create_vaccinations_table.php**
```sql
- id, pet_id, vaccine_name, vaccination_date, expiration_date
- veterinarian_name, clinic_name, batch_number
- is_required, reminder_sent, certificate_file
- notes, timestamps
```

**2025_09_07_133211_create_emergency_contacts_table.php**
```sql
- id, pet_id, type, name, phone, email
- address, priority_order, is_primary, available_24h
- notes, timestamps
```

**2025_09_07_133314_add_detailed_fields_to_pets_table.php**
```sql
- birth_date, gender, weight, color, markings
- allergies, medications, medical_conditions
- behavioral_notes, good_with_kids/dogs/cats
- energy_level, training_notes, feeding_instructions
- exercise_needs, grooming_needs, special_instructions
- microchip_number, insurance_provider, insurance_policy_number
```

### Frontend (Vue.js)

#### Główny komponent

**DetailedPetProfile.vue**
- Lokalizacja: `resources/js/Components/Pet/DetailedPetProfile.vue`
- System tabów z nawigacją
- Tryb edycji/podglądu
- Zarządzanie stanem komponentu

#### Komponenty tabów

**BasicInfoTab.vue**
- Podstawowe informacje o zwierzęciu
- Informacje medyczne (alergie, leki, schorzenia)
- Edycja z walidacją formularzy

**MedicalRecordsTab.vue**
- Lista historii medycznej
- Przypomnienia o follow-up
- Obsługa plików (faktury, dokumenty)
- System statusów wizyt

**VaccinationsTab.vue**
- Lista szczepień z statusami
- Alert o wygasających szczepieniach
- Statystyki szczepień (aktualne/wygasające/wygasłe)
- Obsługa certyfikatów

**EmergencyContactsTab.vue**
- Lista kontaktów awaryjnych
- System priorytetów
- Szybki dostęp do kontaktów emergency
- Grupowanie według typów

**BehavioralInfoTab.vue**
- Informacje o osobowości zwierzęcia
- System znaczników cech charakteru
- Kompatybilność (dzieci/psy/koty)
- Poziom energii z wizualizacją

**CareInstructionsTab.vue**
- Instrukcje żywienia
- Potrzeby ruchowe
- Pielęgnacja
- Instrukcje specjalne
- Podsumowanie dla opiekuna

#### Modali

**MedicalRecordModal.vue**
- Dodawanie/edycja zapisów medycznych
- Upload plików dokumentów
- Walidacja dat i typów wizyt
- FormData handling

**VaccinationModal.vue**
- Zarządzanie szczepieniami
- Lista popularnych szczepionek
- Upload certyfikatów
- Walidacja dat wygaśnięcia

**EmergencyContactModal.vue**
- Dodawanie kontaktów awaryjnych
- Automatyczne priorytetowanie
- Walidacja danych kontaktowych
- Typy kontaktów z opisami

## API Endpoints

### Medical Records
```
GET    /api/pets/{pet}/medical-records         - Lista zapisów
POST   /api/pets/{pet}/medical-records         - Nowy zapis
GET    /api/pets/{pet}/medical-records/{id}    - Szczegóły zapisu
PUT    /api/pets/{pet}/medical-records/{id}    - Aktualizacja
DELETE /api/pets/{pet}/medical-records/{id}    - Usunięcie
POST   /api/pets/{pet}/medical-records/{id}/follow-up-complete - Oznacz follow-up

GET    /api/medical-records/follow-up-due      - Listy wymagające follow-up
```

### Vaccinations
```
GET    /api/pets/{pet}/vaccinations            - Lista szczepień
POST   /api/pets/{pet}/vaccinations            - Nowe szczepienie
GET    /api/pets/{pet}/vaccinations/{id}       - Szczegóły szczepienia
PUT    /api/pets/{pet}/vaccinations/{id}       - Aktualizacja
DELETE /api/pets/{pet}/vaccinations/{id}       - Usunięcie
POST   /api/pets/{pet}/vaccinations/{id}/reminder-sent - Oznacz reminder

GET    /api/vaccinations/expiring-soon         - Wygasające szczepienia
GET    /api/vaccinations/expired               - Wygasłe szczepienia
GET    /api/vaccinations/current               - Aktualne szczepienia
GET    /api/vaccinations/common-vaccines       - Lista popularnych szczepionek
```

### Emergency Contacts
```
GET    /api/pets/{pet}/emergency-contacts      - Lista kontaktów
POST   /api/pets/{pet}/emergency-contacts      - Nowy kontakt
GET    /api/pets/{pet}/emergency-contacts/{id} - Szczegóły kontaktu
PUT    /api/pets/{pet}/emergency-contacts/{id} - Aktualizacja
DELETE /api/pets/{pet}/emergency-contacts/{id} - Usunięcie
POST   /api/pets/{pet}/emergency-contacts/reorder - Zmiana priorytetów

GET    /api/pets/{pet}/emergency-contacts/list/emergency - Tylko emergency
GET    /api/emergency-contacts/types           - Typy kontaktów
```

## Funkcjonalności

### 1. Zarządzanie profilami zwierząt
- Szczegółowe informacje podstawowe (30+ pól)
- System upload zdjęć
- Automatyczne obliczanie wieku z daty urodzenia
- Zarządzanie rasami i typami zwierząt

### 2. Historia medyczna
- Pełna historia wizyt weterynaryjnych
- Klasyfikacja typów wizyt (kontrola, leczenie, operacja, nagły wypadek)
- System follow-up z przypomnieniami
- Upload i zarządzanie fakturami/dokumentami
- Śledzenie kosztów leczenia

### 3. System szczepień
- Zarządzanie wszystkimi szczepieniami
- Automatyczne monitorowanie dat wygaśnięcia
- System powiadomień o wygasających szczepieniach
- Lista popularnych szczepionek
- Upload certyfikatów szczepień
- Oznaczanie szczepień wymaganych/opcjonalnych

### 4. Kontakty awaryjne
- System priorytetów kontaktów
- Różne typy kontaktów (weterynarz, emergency vet, kontrola zatruć)
- Oznaczanie kontaktów dostępnych 24/7
- Szybki dostęp do kontaktów awaryjnych
- Automatyczne formatowanie numerów telefonów

### 5. Informacje behawioralne
- System znaczników osobowości
- Poziomy energii z wizualizacją
- Kompatybilność z dziećmi, psami, kotami
- Notatki treningowe i behawioralne
- Własne cechy charakteru

### 6. Instrukcje opieki
- Szczegółowe instrukcje żywienia
- Potrzeby ruchowe i aktywność
- Wymagania pielęgnacyjne
- Instrukcje specjalne i sytuacje awaryjne
- Podsumowanie dla opiekuna

## Bezpieczeństwo

### Autoryzacja
- Wszystkie operacje wymagają autoryzacji użytkownika
- Sprawdzanie własności zwierzęcia przed operacjami
- Policy-based authorization

### Walidacja danych
- Walidacja wszystkich inputów na poziomie kontrolera
- Sprawdzanie typów plików i rozmiarów
- Walidacja dat (nie przyszłe dla szczepień)
- Wymagane pola oznaczone w formularzach

### Upload plików
- Ograniczenia typów plików (PDF, JPG, PNG)
- Maksymalny rozmiar pliku: 5MB
- Bezpieczne przechowywanie w storage/public
- Automatyczne czyszczenie przy usuwaniu rekordów

## Rozszerzalność

### Dodawanie nowych typów
- Łatwe dodawanie nowych typów wizyt medycznych
- Rozszerzalne typy kontaktów awaryjnych
- Możliwość dodawania własnych szczepionek
- Własne cechy osobowości

### Integracje
- Gotowe API do integracji z systemami veterynary
- Możliwość eksportu danych medycznych
- System powiadomień gotowy do rozszerzenia

### Lokalizacja
- Wszystkie teksty w języku polskim
- Formatowanie dat w formacie polskim
- Formatowanie numerów telefonów (polska notacja)

## Użycie

### Dla właścicieli zwierząt
1. Wypełnienie szczegółowego profilu zwierzęcia
2. Dodawanie historii medycznej i szczepień
3. Konfiguracja kontaktów awaryjnych
4. Udostępnianie profilu opiekunom

### Dla opiekunów/sitterów
1. Dostęp do pełnych informacji o pupilu
2. Kontakt awaryjny w sytuacjach kryzysowych
3. Instrukcje opieki krok po kroku
4. Historia medyczna do konsultacji z weterynarzem

### Dla weterynarzy (przyszłe rozszerzenie)
1. Dostęp do historii medycznej
2. Dodawanie nowych zapisów po wizytach
3. Zarządzanie terminami szczepień
4. System powiadomień o kontrolach

## Status implementacji

✅ **Zakończone:**
- Wszystkie modele i migracje
- Kontrolery z pełnym CRUD
- Wszystkie komponenty Vue
- System upload plików
- API endpoints
- Walidacja i autoryzacja

🔄 **W trakcie:**
- Testy integracyjne
- Dokumentacja API

📋 **Planowane:**
- System powiadomień o wygasających szczepieniach
- Eksport danych do PDF
- Aplikacja mobilna
- Integracja z systemami weterynary

## Metryki systemu

- **Modele:** 4 (Pet, MedicalRecord, Vaccination, EmergencyContact)
- **Tabele:** 4 + rozszerzenie pets
- **Kontrolery:** 3
- **Komponenty Vue:** 10
- **API Endpoints:** 25+
- **Pola w profilu:** 30+
- **Typy plików:** 3 (PDF, JPG, PNG)
- **Maksymalny rozmiar pliku:** 5MB

---

**Ostatnia aktualizacja:** 7 września 2025
**Wersja:** 1.0.0
**Autor:** Claude AI Assistant