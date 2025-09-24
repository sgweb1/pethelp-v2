# Model: EventType

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący eventtype w systemie.

## Lokalizacja
- **Plik**: `app/Models/EventType.php`
- **Tabela**: event_types

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
- **Active** - Query scope
- **Ordered** - Query scope

## Mutators & Accessors
Brak wykrytych mutatorów/accessorów.

## Usage Examples

### Create
```php
$eventType = EventType::create([
    // fields
]);
```

### Find
```php
$eventType = EventType::find($id);
```

### Update
```php
$eventType->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*