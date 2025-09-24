# Blade Component: ui\input

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla ui\input.

## Lokalizacja
- **Plik**: `resources/views/components/ui\input.blade.php`
- **Użycie**: `<x-ui\input>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$id** - Slot dla id
- **$label** - Slot dla label
- **$type** - Slot dla type
- **$hint** - Slot dla hint
- **$error** - Slot dla error

## CSS Classes
```css
.block text-sm font-medium text-gray-700 mb-2
.text-danger-500 ml-1
.relative
.absolute inset-y-0 {{ $iconPosition === 
.h-5 w-5 text-gray-400
.absolute left-4 -top-2.5 bg-white px-2 text-sm font-medium text-gray-700 transition-all duration-200
.mt-2 text-sm text-gray-600
.mt-2 text-sm text-danger-600 flex items-center
.w-4 h-4 mr-1 flex-shrink-0
```

## Usage Example
```blade
<x-ui\input
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-ui\input>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*