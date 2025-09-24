# Model: ServiceCategory

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący servicecategory w systemie.

## Lokalizacja
- **Plik**: `app/Models/ServiceCategory.php`
- **Tabela**: service_categories

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
- **name** - Mutator
- **services_count** - Accessor

## Usage Examples

### Create
```php
$serviceCategory = ServiceCategory::create([
    // fields
]);
```

### Find
```php
$serviceCategory = ServiceCategory::find($id);
```

### Update
```php
$serviceCategory->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*