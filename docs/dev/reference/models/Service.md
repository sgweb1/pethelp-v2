# Model: Service

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący service w systemie.

## Lokalizacja
- **Plik**: `app/Models/Service.php`
- **Tabela**: services

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
- **ByLocation** - Query scope
- **ByPetType** - Query scope
- **ByPetSize** - Query scope
- **ByServiceType** - Query scope
- **ByPriceRange** - Query scope
- **WithAvgRating** - Query scope
- **MinRating** - Query scope

## Mutators & Accessors
- **display_price** - Accessor
- **service_types** - Accessor
- **average_rating** - Accessor
- **reviews_count** - Accessor

## Usage Examples

### Create
```php
$service = Service::create([
    // fields
]);
```

### Find
```php
$service = Service::find($id);
```

### Update
```php
$service->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*