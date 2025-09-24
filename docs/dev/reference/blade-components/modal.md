# Blade Component: modal

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla modal.

## Lokalizacja
- **Plik**: `resources/views/components/modal.blade.php`
- **Użycie**: `<x-modal>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$name** - Slot dla name
- **$maxWidth** - Slot dla maxWidth
- **$slot** - Slot dla slot

## CSS Classes
```css
.fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50
.fixed inset-0 transform transition-all
.absolute inset-0 bg-gray-500 opacity-75
.mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto
```

## Usage Example
```blade
<x-modal
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-modal>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*