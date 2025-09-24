# Model: User

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący user w systemie.

## Lokalizacja
- **Plik**: `app/Models/User.php`
- **Tabela**: users

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
Brak wykrytych mutatorów/accessorów.

## Usage Examples

### Create
```php
$user = User::create([
    // fields
]);
```

### Find
```php
$user = User::find($id);
```

### Update
```php
$user->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*