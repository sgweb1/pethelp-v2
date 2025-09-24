# Model: EventRegistration

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący eventregistration w systemie.

## Lokalizacja
- **Plik**: `app/Models/EventRegistration.php`
- **Tabela**: event_registrations

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
- **Confirmed** - Query scope
- **Pending** - Query scope
- **WaitingList** - Query scope

## Mutators & Accessors
Brak wykrytych mutatorów/accessorów.

## Usage Examples

### Create
```php
$eventRegistration = EventRegistration::create([
    // fields
]);
```

### Find
```php
$eventRegistration = EventRegistration::find($id);
```

### Update
```php
$eventRegistration->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*