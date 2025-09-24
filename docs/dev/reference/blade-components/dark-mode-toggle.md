# Blade Component: dark-mode-toggle

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla dark-mode-toggle.

## Lokalizacja
- **Plik**: `resources/views/components/dark-mode-toggle.blade.php`
- **Użycie**: `<x-dark.mode.toggle>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$iconSize** - Slot dla iconSize
- **$sizeClasses** - Slot dla sizeClasses

## CSS Classes
```css
.flex items-center gap-2
.text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:inline
.relative
.flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
                               bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               hover:bg-gray-200 dark:hover:bg-gray-600
.{ 
.{{ $iconSize }}
.hidden sm:inline
.w-4 h-4 transition-transform duration-200
.absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50
.py-1
.flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700
.w-4 h-4
.w-4 h-4 ml-auto text-blue-600 dark:text-blue-400
.px-4 py-2 border-t border-gray-200 dark:border-gray-700
.text-xs text-gray-500 dark:text-gray-400
.{{ $sizeClasses }} flex items-center justify-center rounded-lg transition-all duration-200
                       bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                       hover:bg-gray-200 dark:hover:bg-gray-600 hover:scale-105 active:scale-95
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800
.ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:inline
```

## Usage Example
```blade
<x-dark.mode.toggle
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-dark.mode.toggle>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:09:54*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*