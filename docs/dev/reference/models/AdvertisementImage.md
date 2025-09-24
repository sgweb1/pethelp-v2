# Model: AdvertisementImage

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący advertisementimage w systemie.

## Lokalizacja
- **Plik**: `app/Models/AdvertisementImage.php`
- **Tabela**: advertisement_images

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
- **Primary** - Query scope
- **Ordered** - Query scope

## Mutators & Accessors
- **url** - Accessor
- **thumbnail_url** - Accessor
- **file_size_human** - Accessor

## Usage Examples

### Create
```php
$advertisementImage = AdvertisementImage::create([
    // fields
]);
```

### Find
```php
$advertisementImage = AdvertisementImage::find($id);
```

### Update
```php
$advertisementImage->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*