# Model: Availability

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący availability w systemie.

## Lokalizacja
- **Plik**: `app/Models/Availability.php`
- **Tabela**: availabilities

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
- **Available** - Query scope
- **ForDate** - Query scope
- **ForSitter** - Query scope
- **ForTimeSlot** - Query scope
- **ForServiceType** - Query scope

## Mutators & Accessors
- **time_range** - Accessor
- **time_slot_label** - Accessor
- **service_type_label** - Accessor

## Usage Examples

### Create
```php
$availability = Availability::create([
    // fields
]);
```

### Find
```php
$availability = Availability::find($id);
```

### Update
```php
$availability->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*