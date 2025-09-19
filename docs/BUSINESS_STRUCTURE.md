# Struktura Biznesowa PetHelp

## Główne Kategorie Użytkowników i Usług

### 1. 🐕‍🦺 PET SITTERZY - Kategoria Premium
**Status:** Kluczowa kategoria użytkowników serwisu
**Opis:** Zarejestrowane osoby świadczące usługi opieki nad zwierzętami

#### Funkcjonalności specjalne:
- ✅ **System rezerwacji** - klienci mogą rezerwować terminy
- ✅ **Chat w czasie rzeczywistym** - komunikacja z właścicielami
- ✅ **Lokalizowanie GPS** - śledzenie spacerów i lokalizacji
- ✅ **Profile użytkowników** - oceny, zdjęcia, doświadczenie
- ✅ **Kalendarz dostępności** - zarządzanie terminami
- ✅ **System płatności** - automatyczne rozliczenia
- ✅ **Weryfikacja tożsamości** - sprawdzone profile

#### Podkategorie usług pet sitterów:
- **Spacery z psami** (1-3 godziny)
- **Opieka w domu właściciela** (całodniowa)
- **Opieka w domu pet sittera** (nocleg)
- **Wizyty podczas wakacji** (kilka dni)
- **Transport zwierząt** (do weterynarza, groomersa)
- **Ćwiczenia i treningi** (podstawowe szkolenie)

#### Model danych:
```
content_type: 'pet_sitter'
category_name: ['Spacery', 'Opieka w domu', 'Nocleg', 'Transport', 'Trening']
Specjalne pola: user_profile_id, availability_calendar, price_per_hour
```

---

### 2. 🏥 USŁUGI PROFESJONALNE - Firmy i Specjaliści
**Status:** Kategoria biznesowa
**Opis:** Profesjonalne usługi świadczone przez firmy i wykwalifikowanych specjalistów

#### Podkategorie:
##### 🩺 **Opieka weterynaryjna**
- Kliniki weterynaryjne
- Szpitale weterynaryjne
- Przychodnie weterynaryjne
- Centra weterynaryjne
- Rehabilitacja zwierząt
- Weterynarze mobilni

##### 🛍️ **Sklepy zoologiczne**
- Sklepy stacjonarne
- Sklepy online z dostawą
- Hurtownie zoologiczne
- Sklepy specjalistyczne (reptilia, ryby)

##### ✂️ **Grooming i pielęgnacja**
- Salony groomerskie
- Fryzjerzy dla zwierząt
- Pedicure dla psów
- Spa dla zwierząt

##### 🏨 **Hotele i pensjonaty**
- Hotele dla zwierząt
- Pensjonaty długoterminowe
- Ośrodki wypoczynkowe dla zwierząt

##### 🎾 **Rekreacja i sport**
- Parki dla psów
- Plaże dla psów
- Szkółki psie
- Kluby sportowe (agility, dog dancing)

#### Model danych:
```
content_type: 'service'
category_name: ['Weterynaria', 'Sklep zoologiczny', 'Grooming', 'Hotel', 'Rekreacja']
Specjalne pola: business_hours, contact_phone, website
```

---

### 3. 🗓️ WYDARZENIA - Community & Private Features
**Status:** Funkcja społecznościowa + prywatna komunikacja
**Opis:** Wydarzenia organizowane przez społeczność i prywatne spotkania między użytkownikami

#### A) **Wydarzenia Publiczne** (Community)
- **Spotkania rasowe** - właściciele konkretnych ras
- **Spacery grupowe** - wspólne wyjścia
- **Warsztaty edukacyjne** - szkolenia, wykłady
- **Wystawy i konkursy** - pokazy zwierząt
- **Akcje charytatywne** - zbiórki dla schronisk
- **Dni adopcyjne** - wydarzenia adopcyjne

#### B) **Wydarzenia Prywatne** (Private Events)
- **Spotkania 1-na-1** - prywatne spacery między właścicielami
- **Playdate dla psów** - umówione zabawy między pupilami
- **Wspólne wyjścia** - prywatne grupy znajomych
- **Trengi grupowe** - prywatne sesje treningowe
- **Pet sitter meetups** - spotkania opiekunów z klientami
- **Konsultacje behawioralne** - prywatne sesje z ekspertami

#### Funkcjonalności specjalne:
- **Public Events:**
  - Otwarte dla wszystkich użytkowników
  - System rejestracji i limitów uczestników
  - Komentarze i oceny wydarzenia
  - Udostępnianie w social media

- **Private Events:**
  - Tylko między zaproszonymi użytkownikami
  - Prywatny chat do organizacji
  - System potwierdzeń obecności
  - Integracja z kalendarzem użytkownika
  - Przypomnienia i notyfikacje

#### Model danych:
```
content_type: 'event_public' / 'event_private'
category_name: ['Spotkania rasowe', 'Spacery', 'Warsztaty', 'Playdate', 'Trening']
Specjalne pola:
- starts_at, ends_at, max_participants, registration_required
- is_private (boolean), invited_users (json), organizer_id
- chat_channel_id, location_sharing_enabled
```

---

### 4. 🏠 ADOPCJA - Misja Społeczna
**Status:** Funkcja społeczna
**Opis:** Pomoc w znajdowaniu domów dla bezdomnych zwierząt

#### Kategorie:
- **Psy** - różne rozmiary i rasy
- **Koty** - domowe i półdzikie
- **Pozostałe** - króliki, gryzonie, ptaki
- **Schroniska** - profile instytucji
- **Tymczasowy dom** - opieka przejściowa

#### Model danych:
```
content_type: 'adoption'
category_name: ['Psy', 'Koty', 'Pozostałe zwierzęta', 'Schronisko', 'Dom tymczasowy']
Specjalne pola: animal_age, animal_size, vaccination_status, special_needs
```

---

### 5. 💰 SPRZEDAŻ - Marketplace
**Status:** Funkcja komercyjna
**Opis:** Sprzedaż zwierząt i akcesoriów przez prywatne osoby i firmy

#### Kategorie sprzedaży:
##### 🐕 **Zwierzęta**
- Psy rasowe
- Koty rasowe
- Ptaki egzotyczne
- Gryzonie i króliki
- Ryby i gadы

##### 🛍️ **Akcesoria używane**
- Klatki i legowiska
- Zabawki
- Ubranka
- Sprzęt transportowy
- Książki i materiały edukacyjne

#### Model danych:
```
content_type: 'sale'
category_name: ['Psy', 'Koty', 'Ptaki', 'Gryzonie', 'Akcesoria']
Specjalne pola: price_from, price_to, condition, negotiable
```

---

### 6. 😢😊 ZAGINIONE/ZNALEZIONE - Alert System
**Status:** System alarmowy społeczności
**Opis:** Pomoc w odnajdywaniu zaginionych zwierząt

#### Funkcjonalności specjalne:
- **Alerty SMS/Email** - powiadomienia w obszarze
- **Mapa zaginionych** - wizualizacja przypadków
- **Udostępnianie społecznościowe** - szybkie rozpowszechnianie
- **System nagród** - motywacja do poszukiwań

#### Model danych:
```
content_type: 'lost_pet' / 'found_pet'
category_name: ['Psy', 'Koty', 'Inne zwierzęta']
Specjalne pola: lost_date, last_seen_location, reward_amount, contact_urgency
```

---

## Architektura Techniczna

### Tabela map_items - Unified Content Structure
```sql
-- Enum dla głównych kategorii biznesowych
content_type ENUM(
    'pet_sitter',    -- 🐕‍🦺 Główna kategoria - ludzie (opieka, spacery)
    'service',       -- 🏥 Firmy i specjaliści (weterynarze, sklepy)
    'event_public',  -- 🗓️ Wydarzenia publiczne (community)
    'event_private', -- 👥 Wydarzenia prywatne (między użytkownikami)
    'adoption',      -- 🏠 Adopcja (misja społeczna)
    'sale',          -- 💰 Marketplace (komercyjny)
    'lost_pet',      -- 😢 System alarmowy
    'found_pet'      -- 😊 System alarmowy
)
```

### Routing Structure
```
/pet-sitters/          -- Główna kategoria użytkowników
├── /browse            -- Przeglądanie pet sitterów
├── /profile/{id}      -- Profile + rezerwacje + chat
├── /book/{id}         -- System rezerwacji
└── /messages          -- Chat system

/services/             -- Usługi profesjonalne
├── /veterinary        -- Weterynarze
├── /shops            -- Sklepy zoologiczne
├── /grooming         -- Fryzjerzy
└── /hotels           -- Hotele dla zwierząt

/community/           -- Funkcje społecznościowe
├── /events           -- Wydarzenia publiczne
├── /private-events   -- Wydarzenia prywatne (zaproszenia)
├── /adoptions        -- Adopcje
└── /alerts           -- Zaginione/znalezione

/marketplace/         -- Sprzedaż komercyjna
└── /listings         -- Ogłoszenia sprzedaży
```

---

## Search Logic - Wyszukiwarka

### Hierarchia wyników wyszukiwania:
1. **Pet Sitterzy** - najwyższy priorytet (główny business)
2. **Usługi profesjonalne** - średni priorytet
3. **Wydarzenia** - kontekstowo (daty)
4. **Adopcja** - wysoki priorytet społeczny
5. **Sprzedaż** - niski priorytet
6. **Zaginione** - najwyższy priorytet (alert!)

### Filtry specjalne:
- **Dostępność czasowa** (pet sitterzy, usługi)
- **Zasięg cenowy** (pet sitterzy, sprzedaż)
- **Oceny użytkowników** (pet sitterzy, usługi)
- **Weryfikacja** (pet sitterzy, schroniska)
- **Pilność** (zaginione zwierzęta)

---

## Implementation Priority

### Phase 1: Pet Sitters (CORE)
- ✅ Podstawowe profile i search
- 🔄 System rezerwacji
- 🔄 Chat system
- 🔄 GPS tracking podczas spacerów
- 🔄 System płatności

### Phase 2: Professional Services
- ✅ Profile firm i lokalizacje
- 🔄 Godziny otwarcia i kontakt
- 🔄 Integracja z systemami rezerwacji firm

### Phase 3: Community Features
- ✅ Wydarzenia i adopcje
- 🔄 Alert system dla zaginionych
- 🔄 Social features (komentarze, udostępnianie)

---

## Wnioski Biznesowe

1. **Pet Sitterzy = Core Business**
   - Najwięcej funkcji premium
   - Największy potencjał przychodów
   - Wymaga największej uwagi w UX

2. **Usługi Profesjonalne = Stabilny Business**
   - Profile firm + podstawowe informacje
   - Mniej funkcji interaktywnych
   - Model opłat za widoczność

3. **Community Features = User Engagement**
   - Budowanie społeczności
   - Zwiększanie retencji użytkowników
   - Generowanie contentu

4. **Marketplace = Additional Revenue**
   - Dodatkowy stream przychodów
   - Mniejsze zaangażowanie w development

5. **Alert System = Social Mission**
   - Funkcja społeczna (CSR)
   - Marketing wizerunkowy
   - Szybka implementacja, duży impact