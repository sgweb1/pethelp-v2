# Blade Component: accessible-form-field

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla accessible-form-field.

## Lokalizacja
- **Plik**: `resources/views/components/accessible-form-field.blade.php`
- **Użycie**: `<x-accessible.form.field>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$fieldId** - Slot dla fieldId
- **$label** - Slot dla label
- **$name** - Slot dla name
- **$placeholder** - Slot dla placeholder
- **$ariaDescribedBy** - Slot dla ariaDescribedBy
- **$inputClasses** - Slot dla inputClasses
- **$value** - Slot dla value
- **$slot** - Slot dla slot
- **$type** - Slot dla type
- **$errorId** - Slot dla errorId
- **$error** - Slot dla error
- **$helpId** - Slot dla helpId
- **$helpText** - Slot dla helpText

## CSS Classes
```css
.form-field-accessible
.block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2
.required text-red-500
.relative
.{{ $inputClasses }} min-h-[100px]
.{{ $inputClasses }}
.error-message mt-1 text-sm text-red-600 dark:text-red-400
.help-text mt-1 text-sm text-gray-600 dark:text-gray-400
```

## Usage Example
```blade
<x-accessible.form.field
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-accessible.form.field>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:09:54*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*