# Model: SubscriptionPlan

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący subscriptionplan w systemie.

## Lokalizacja
- **Plik**: `app/Models/SubscriptionPlan.php`
- **Tabela**: subscription_plans

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
- **Monthly** - Query scope
- **Yearly** - Query scope

## Mutators & Accessors
- **formatted_price** - Accessor
- **monthly_price** - Accessor
- **feature_list** - Accessor

## Usage Examples

### Create
```php
$subscriptionPlan = SubscriptionPlan::create([
    // fields
]);
```

### Find
```php
$subscriptionPlan = SubscriptionPlan::find($id);
```

### Update
```php
$subscriptionPlan->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*