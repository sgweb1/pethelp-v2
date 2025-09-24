# Model: Booking

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący booking w systemie.

## Lokalizacja
- **Plik**: `app/Models/Booking.php`
- **Tabela**: bookings

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
- **Pending** - Query scope
- **Confirmed** - Query scope
- **Completed** - Query scope
- **ForOwner** - Query scope
- **ForSitter** - Query scope
- **Upcoming** - Query scope
- **Past** - Query scope

## Mutators & Accessors
- **duration_in_hours** - Accessor
- **duration_in_days** - Accessor
- **status_label** - Accessor
- **status_color** - Accessor

## Usage Examples

### Create
```php
$booking = Booking::create([
    // fields
]);
```

### Find
```php
$booking = Booking::find($id);
```

### Update
```php
$booking->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*