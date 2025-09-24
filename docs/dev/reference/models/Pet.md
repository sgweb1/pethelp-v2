# Model: Pet

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący pet w systemie.

## Lokalizacja
- **Plik**: `app/Models/Pet.php`
- **Tabela**: pets

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
- **ByType** - Query scope
- **ByPetTypeId** - Query scope
- **BySize** - Query scope
- **ForOwner** - Query scope

## Mutators & Accessors
- **type_label** - Accessor
- **size_label** - Accessor
- **gender_label** - Accessor
- **age** - Accessor
- **age_group** - Accessor
- **special_needs_list** - Accessor
- **photo_url** - Accessor

## Usage Examples

### Create
```php
$pet = Pet::create([
    // fields
]);
```

### Find
```php
$pet = Pet::find($id);
```

### Update
```php
$pet->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*