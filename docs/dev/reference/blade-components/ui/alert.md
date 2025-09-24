# Blade Component: ui\alert

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla ui\alert.

## Lokalizacja
- **Plik**: `resources/views/components/ui\alert.blade.php`
- **Użycie**: `<x-ui\alert>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$title** - Slot dla title
- **$slot** - Slot dla slot
- **$actions** - Slot dla actions

## CSS Classes
```css
.flex items-start
.flex-shrink-0 mr-3
.text-base
.flex-1
.font-semibold mb-1 text-sm
.mt-3 flex space-x-2
.ml-4 flex-shrink-0 text-current opacity-70 hover:opacity-100 transition-opacity
.w-5 h-5
```

## Usage Example
```blade
<x-ui\alert
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-ui\alert>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*