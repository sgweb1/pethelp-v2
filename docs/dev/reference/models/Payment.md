# Model: Payment

Automatycznie wygenerowana dokumentacja dla modelu Eloquent.

## Opis
Model reprezentujący payment w systemie.

## Lokalizacja
- **Plik**: `app/Models/Payment.php`
- **Tabela**: payments

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
- **Completed** - Query scope
- **Failed** - Query scope

## Mutators & Accessors
- **status_label** - Accessor
- **payment_method_label** - Accessor
- **sitter_amount** - Accessor

## Usage Examples

### Create
```php
$payment = Payment::create([
    // fields
]);
```

### Find
```php
$payment = Payment::find($id);
```

### Update
```php
$payment->update([
    // fields
]);
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*