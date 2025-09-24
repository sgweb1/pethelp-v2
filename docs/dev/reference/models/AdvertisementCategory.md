# Model: AdvertisementCategory

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący advertisementcategory w systemie.

## Lokalizacja
- **Plik**: `app/Models/AdvertisementCategory.php`
- **Tabela**: advertisement_categories

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
- **RootCategories** - Query scope
- **ByType** - Query scope
- **Ordered** - Query scope

## Mutators & Accessors
- **full_name** - Accessor

## Usage Examples

### Create
```php
$advertisementCategory = AdvertisementCategory::create([
    // fields
]);
```

### Find
```php
$advertisementCategory = AdvertisementCategory::find($id);
```

### Update
```php
$advertisementCategory->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*