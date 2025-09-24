# Blade Component: ui\dropdown

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla ui\dropdown.

## Lokalizacja
- **Plik**: `resources/views/components/ui\dropdown.blade.php`
- **Użycie**: `<x-ui\dropdown>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$trigger** - Slot dla trigger
- **$positionClass** - Slot dla positionClass
- **$width** - Slot dla width
- **$slot** - Slot dla slot

## CSS Classes
```css
.relative inline-block text-left
.absolute {{ $positionClass }} {{ $width }} z-50
.bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none
```

## Usage Example
```blade
<x-ui\dropdown
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-ui\dropdown>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*