# Model: Conversation

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący conversation w systemie.

## Lokalizacja
- **Plik**: `app/Models/Conversation.php`
- **Tabela**: conversations

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
- **ForUser** - Query scope
- **Recent** - Query scope

## Mutators & Accessors
Brak wykrytych mutatorów/accessorów.

## Usage Examples

### Create
```php
$conversation = Conversation::create([
    // fields
]);
```

### Find
```php
$conversation = Conversation::find($id);
```

### Update
```php
$conversation->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*