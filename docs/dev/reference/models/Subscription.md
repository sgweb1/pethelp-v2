# Model: Subscription

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący subscription w systemie.

## Lokalizacja
- **Plik**: `app/Models/Subscription.php`
- **Tabela**: subscriptions

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
- **Expired** - Query scope
- **Cancelled** - Query scope
- **UpForRenewal** - Query scope

## Mutators & Accessors
- **days_remaining** - Accessor
- **formatted_price** - Accessor
- **status_label** - Accessor
- **next_billing_amount** - Accessor
- **remaining_listings** - Accessor

## Usage Examples

### Create
```php
$subscription = Subscription::create([
    // fields
]);
```

### Find
```php
$subscription = Subscription::find($id);
```

### Update
```php
$subscription->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*