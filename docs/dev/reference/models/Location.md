# Model: Location

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący location w systemie.

## Lokalizacja
- **Plik**: `app/Models/Location.php`
- **Tabela**: locations

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
- **full_address** - Accessor
- **display_name** - Accessor

## Usage Examples

### Create
```php
$location = Location::create([
    // fields
]);
```

### Find
```php
$location = Location::find($id);
```

### Update
```php
$location->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*