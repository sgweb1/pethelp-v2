# Model: ProfessionalService

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący professionalservice w systemie.

## Lokalizacja
- **Plik**: `app/Models/ProfessionalService.php`
- **Tabela**: professional_services

## Pola bazy danych
| Pole | Typ | Opis |
|------|-----|------|
| id | bigint | Klucz główny |
| created_at | timestamp | Data utworzenia |
| updated_at | timestamp | Data aktualizacji |

*📝 Uzupełnij rzeczywiste pola tabeli*

## Relationships
Brak wykrytych relacji.

## Scopes
- **Published** - Query scope
- **InCity** - Query scope
- **WithinServiceRadius** - Query scope
- **WithRating** - Query scope
- **Featured** - Query scope
- **WithEmergencyServices** - Query scope
- **WithOnlineBooking** - Query scope

## Mutators & Accessors
- **service_area** - Accessor
- **price_range** - Accessor
- **services_list** - Accessor
- **specialization_list** - Accessor
- **rating_display** - Accessor

## Usage Examples

### Create
```php
$professionalService = ProfessionalService::create([
    // fields
]);
```

### Find
```php
$professionalService = ProfessionalService::find($id);
```

### Update
```php
$professionalService->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*