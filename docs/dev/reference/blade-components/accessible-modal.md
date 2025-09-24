# Blade Component: accessible-modal

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla accessible-modal.

## Lokalizacja
- **Plik**: `resources/views/components/accessible-modal.blade.php`
- **Użycie**: `<x-accessible.modal>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$modalId** - Slot dla modalId
- **$attributes** - Slot dla attributes
- **$sizeClasses** - Slot dla sizeClasses
- **$title** - Slot dla title
- **$slot** - Slot dla slot
- **$footer** - Slot dla footer

## CSS Classes
```css
.modal-accessible
.modal-content-accessible {{ $sizeClasses }} w-full
.flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-gray-700
.text-xl font-semibold text-gray-900 dark:text-gray-100
.modal-close text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded p-1
.w-6 h-6
.modal-body
.flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700
```

## Usage Example
```blade
<x-accessible.modal
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-accessible.modal>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:09:54*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*