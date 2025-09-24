# Model: Photo

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący photo w systemie.

## Lokalizacja
- **Plik**: `app/Models/Photo.php`
- **Tabela**: photos

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
- **ForUser** - Query scope
- **ForPet** - Query scope
- **InAlbum** - Query scope
- **Public** - Query scope
- **Featured** - Query scope
- **Ordered** - Query scope

## Mutators & Accessors
- **url** - Accessor
- **thumbnail_url** - Accessor
- **file_size_human** - Accessor
- **dimensions** - Accessor

## Usage Examples

### Create
```php
$photo = Photo::create([
    // fields
]);
```

### Find
```php
$photo = Photo::find($id);
```

### Update
```php
$photo->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*