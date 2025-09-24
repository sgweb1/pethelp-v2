# Model: UserProfile

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący userprofile w systemie.

## Lokalizacja
- **Plik**: `app/Models/UserProfile.php`
- **Tabela**: user_profiles

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
Brak wykrytych scope'ów.

## Mutators & Accessors
- **full_name** - Accessor
- **experience_display** - Accessor

## Usage Examples

### Create
```php
$userProfile = UserProfile::create([
    // fields
]);
```

### Find
```php
$userProfile = UserProfile::find($id);
```

### Update
```php
$userProfile->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*