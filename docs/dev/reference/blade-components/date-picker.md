# Blade Component: date-picker

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla date-picker.

## Lokalizacja
- **Plik**: `resources/views/components/date-picker.blade.php`
- **Użycie**: `<x-date.picker>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$wireModel** - Slot dla wireModel
- **$minDate** - Slot dla minDate
- **$maxDate** - Slot dla maxDate
- **$id** - Slot dla id
- **$label** - Slot dla label
- **$placeholder** - Slot dla placeholder

## CSS Classes
```css
.relative
.block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2
.text-red-500
.w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white bg-white text-left flex items-center justify-between min-h-[48px] hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors
.{ 
.truncate mr-2
.w-5 h-5 text-purple-500 flex-shrink-0
.absolute z-50 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl p-5 w-80 max-w-full left-0 right-0 mx-auto
.flex items-center justify-between mb-5
.p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors
.w-5 h-5 text-gray-600 dark:text-gray-400
.text-base font-semibold text-gray-900 dark:text-white
.grid grid-cols-7 gap-1 mb-3
.py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400
.grid grid-cols-7 gap-1
.relative p-2.5 text-sm rounded-lg transition-all duration-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 min-h-[36px] flex items-center justify-center
.{
                        
.font-medium
.absolute bottom-1 left-1/2 transform -translate-x-1/2 w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full
.mt-5 pt-4 border-t border-gray-200 dark:border-gray-700
.flex gap-3
.flex-1 px-4 py-2.5 text-sm font-medium bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors
.flex-1 px-4 py-2.5 text-sm font-medium bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors
```

## Usage Example
```blade
<x-date.picker
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-date.picker>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*