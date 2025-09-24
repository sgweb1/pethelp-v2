# Model: EventLocation

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący eventlocation w systemie.

## Lokalizacja
- **Plik**: `app/Models/EventLocation.php`
- **Tabela**: event_locations

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
Brak wykrytych scope'ów.

## Mutators & Accessors
- **display_location** - Accessor

## Usage Examples

### Create
```php
$eventLocation = EventLocation::create([
    // fields
]);
```

### Find
```php
$eventLocation = EventLocation::find($id);
```

### Update
```php
$eventLocation->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*