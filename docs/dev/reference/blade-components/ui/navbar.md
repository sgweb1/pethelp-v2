# Blade Component: ui\navbar

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla ui\navbar.

## Lokalizacja
- **Plik**: `resources/views/components/ui\navbar.blade.php`
- **Użycie**: `<x-ui\navbar>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$brand** - Slot dla brand
- **$slot** - Slot dla slot

## CSS Classes
```css
.{{ $container ? 
.flex items-center justify-between h-16
.flex-shrink-0
.hidden md:block
.ml-10 flex items-baseline space-x-4
.md:hidden
.inline-flex items-center justify-center p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500
.h-6 w-6
.px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-200
```

## Usage Example
```blade
<x-ui\navbar
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-ui\navbar>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*