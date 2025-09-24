# Model: Advertisement

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący advertisement w systemie.

## Lokalizacja
- **Plik**: `app/Models/Advertisement.php`
- **Tabela**: advertisements

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
- **Active** - Query scope
- **InCity** - Query scope
- **ByType** - Query scope
- **PriceRange** - Query scope
- **Featured** - Query scope
- **Urgent** - Query scope

## Mutators & Accessors
- **pet_age** - Accessor
- **contact_info** - Accessor

## Usage Examples

### Create
```php
$advertisement = Advertisement::create([
    // fields
]);
```

### Find
```php
$advertisement = Advertisement::find($id);
```

### Update
```php
$advertisement->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*