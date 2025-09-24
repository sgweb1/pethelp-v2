# Blade Component: ui\toast

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla ui\toast.

## Lokalizacja
- **Plik**: `resources/views/components/ui\toast.blade.php`
- **Użycie**: `<x-ui\toast>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$timeout** - Slot dla timeout
- **$positionClass** - Slot dla positionClass
- **$title** - Slot dla title
- **$slot** - Slot dla slot

## CSS Classes
```css
.w-5 h-5 text-green-400
.w-5 h-5 text-red-400
.w-5 h-5 text-yellow-400
.w-5 h-5 text-blue-400
.fixed {{ $positionClass }} z-50 max-w-sm w-full bg-white border rounded-lg shadow-lg pointer-events-auto {{ $config[
.p-4
.flex items-start
.flex-shrink-0
.ml-3 w-0 flex-1
.text-sm font-medium
.mt-1 text-sm opacity-90
.text-sm
.ml-4 flex-shrink-0 flex
.inline-flex text-current opacity-70 hover:opacity-100 focus:outline-none transition-opacity
.w-4 h-4
```

## Usage Example
```blade
<x-ui\toast
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-ui\toast>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*