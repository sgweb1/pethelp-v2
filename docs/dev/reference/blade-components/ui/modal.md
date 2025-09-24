# Blade Component: ui\modal

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla ui\modal.

## Lokalizacja
- **Plik**: `resources/views/components/ui\modal.blade.php`
- **Użycie**: `<x-ui\modal>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$id** - Slot dla id
- **$modalSize** - Slot dla modalSize
- **$title** - Slot dla title
- **$slot** - Slot dla slot
- **$footer** - Slot dla footer

## CSS Classes
```css
.fixed inset-0 z-50 overflow-y-auto
.fixed inset-0 bg-black bg-opacity-50 transition-opacity
.flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0
.inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $modalSize }} sm:w-full
.bg-white px-4 py-3 border-b border-gray-200 sm:px-6 flex items-center justify-between
.text-lg leading-6 font-medium text-gray-900
.text-gray-400 hover:text-gray-600 transition-colors
.w-6 h-6
.bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4
.bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200
```

## Usage Example
```blade
<x-ui\modal
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-ui\modal>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*