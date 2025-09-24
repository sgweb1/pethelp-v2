# Blade Component: address-search

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla address-search.

## Lokalizacja
- **Plik**: `resources/views/components/address-search.blade.php`
- **Użycie**: `<x-address.search>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$wireModel** - Slot dla wireModel
- **$id** - Slot dla id
- **$label** - Slot dla label
- **$placeholder** - Slot dla placeholder

## CSS Classes
```css
.relative
.block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2
.text-red-500
.w-full px-4 py-3.5 pr-20 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white min-h-[48px] hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors cursor-text
.{ 
.absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-purple-500 hover:text-purple-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors
.w-5 h-5
.w-5 h-5 animate-spin
.opacity-25
.opacity-75
.absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg max-h-64 overflow-y-auto
.p-3 text-center text-gray-500 dark:text-gray-400
.flex items-center justify-center gap-2
.w-4 h-4 animate-spin
.w-full px-3 py-3 text-left hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0
.flex items-center gap-3
.flex-shrink-0
.w-4 h-4 text-purple-500
.flex-1 min-w-0
.text-sm font-medium text-gray-900 dark:text-white
.text-xs text-gray-500 dark:text-gray-400 flex items-center
.ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
.w-3 h-3 mr-1
.flex flex-col items-center gap-2
.w-8 h-8 text-gray-300 dark:text-gray-600
.text-sm
.text-xs
```

## Usage Example
```blade
<x-address.search
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-address.search>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:09:54*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*