# Model: MapItem

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący mapitem w systemie.

## Lokalizacja
- **Plik**: `app/Models/MapItem.php`
- **Tabela**: map_items

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
- **InBounds** - Query scope
- **NearLocation** - Query scope
- **ByContentType** - Query scope
- **InCity** - Query scope
- **InVoivodeship** - Query scope
- **PriceRange** - Query scope
- **Featured** - Query scope
- **Urgent** - Query scope
- **Active** - Query scope
- **Upcoming** - Query scope
- **VisibleAtZoom** - Query scope
- **Search** - Query scope
- **PetSitters** - Query scope
- **ProfessionalServices** - Query scope
- **PublicEvents** - Query scope
- **PrivateEvents** - Query scope
- **OrderByBusinessPriority** - Query scope
- **UrgentFirst** - Query scope
- **WithinBounds** - Query scope
- **OptimizedForMap** - Query scope
- **GridClustered** - Query scope

## Mutators & Accessors
- **price_display** - Accessor
- **content_type_name** - Accessor

## Usage Examples

### Create
```php
$mapItem = MapItem::create([
    // fields
]);
```

### Find
```php
$mapItem = MapItem::find($id);
```

### Update
```php
$mapItem->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*