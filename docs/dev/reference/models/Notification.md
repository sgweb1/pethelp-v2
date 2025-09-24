# Model: Notification

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący notification w systemie.

## Lokalizacja
- **Plik**: `app/Models/Notification.php`
- **Tabela**: notifications

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
- **Unread** - Query scope
- **Read** - Query scope
- **Important** - Query scope
- **ByType** - Query scope

## Mutators & Accessors
- **icon** - Accessor

## Usage Examples

### Create
```php
$notification = Notification::create([
    // fields
]);
```

### Find
```php
$notification = Notification::find($id);
```

### Update
```php
$notification->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*