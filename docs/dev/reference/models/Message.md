# Model: Message

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący message w systemie.

## Lokalizacja
- **Plik**: `app/Models/Message.php`
- **Tabela**: messages

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
- **ForReceiver** - Query scope

## Mutators & Accessors
- **time_ago** - Accessor

## Usage Examples

### Create
```php
$message = Message::create([
    // fields
]);
```

### Find
```php
$message = Message::find($id);
```

### Update
```php
$message->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*