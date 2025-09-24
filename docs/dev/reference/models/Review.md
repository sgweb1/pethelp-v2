# Model: Review

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący review w systemie.

## Lokalizacja
- **Plik**: `app/Models/Review.php`
- **Tabela**: reviews

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
- **Visible** - Query scope
- **ForUser** - Query scope
- **ByUser** - Query scope
- **ForSitter** - Query scope
- **Recent** - Query scope

## Mutators & Accessors
- **stars** - Accessor
- **rating_label** - Accessor

## Usage Examples

### Create
```php
$review = Review::create([
    // fields
]);
```

### Find
```php
$review = Review::find($id);
```

### Update
```php
$review->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*