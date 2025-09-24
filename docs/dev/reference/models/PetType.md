# Model: PetType

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący pettype w systemie.

## Lokalizacja
- **Plik**: `app/Models/PetType.php`
- **Tabela**: pet_types

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
- **display_name** - Accessor

## Usage Examples

### Create
```php
$petType = PetType::create([
    // fields
]);
```

### Find
```php
$petType = PetType::find($id);
```

### Update
```php
$petType->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*