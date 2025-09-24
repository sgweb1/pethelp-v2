# Blade Component: accessible-button

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla accessible-button.

## Lokalizacja
- **Plik**: `resources/views/components/accessible-button.blade.php`
- **Użycie**: `<x-accessible.button>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$type** - Slot dla type
- **$ariaLabel** - Slot dla ariaLabel
- **$ariaDescribedBy** - Slot dla ariaDescribedBy
- **$role** - Slot dla role
- **$loadingText** - Slot dla loadingText
- **$slot** - Slot dla slot

## CSS Classes
```css
.animate-spin -ml-1 mr-2 h-4 w-4 text-current
.opacity-25
.opacity-75
.sr-only
```

## Usage Example
```blade
<x-accessible.button
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-accessible.button>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:09:54*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*