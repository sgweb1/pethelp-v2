# Model: Event

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący event w systemie.

## Lokalizacja
- **Plik**: `app/Models/Event.php`
- **Tabela**: events

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
- **Upcoming** - Query scope
- **InCity** - Query scope
- **NearLocation** - Query scope
- **WithType** - Query scope

## Mutators & Accessors
- **available_spots** - Accessor

## Usage Examples

### Create
```php
$event = Event::create([
    // fields
]);
```

### Find
```php
$event = Event::find($id);
```

### Update
```php
$event->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*