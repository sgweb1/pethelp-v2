# Blade Component: ui\accordion

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla ui\accordion.

## Lokalizacja
- **Plik**: `resources/views/components/ui\accordion.blade.php`
- **Użycie**: `<x-ui\accordion>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$index** - Slot dla index
- **$slot** - Slot dla slot

## CSS Classes
```css
.{{ $flush ? 
.w-full px-4 py-3 text-left bg-white hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors duration-200 flex items-center justify-between
.{ 
.font-medium text-gray-900
.w-5 h-5 text-gray-500 transform transition-transform duration-200
.bg-white
.px-4 py-3 text-gray-700 border-t border-gray-200
```

## Usage Example
```blade
<x-ui\accordion
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-ui\accordion>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*