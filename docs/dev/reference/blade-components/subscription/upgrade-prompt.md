# Blade Component: subscription\upgrade-prompt

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla subscription\upgrade-prompt.

## Lokalizacja
- **Plik**: `resources/views/components/subscription\upgrade-prompt.blade.php`
- **Użycie**: `<x-subscription\upgrade.prompt>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$title** - Slot dla title
- **$description** - Slot dla description
- **$feature** - Slot dla feature

## CSS Classes
```css
.flex items-start
.flex-shrink-0
.w-8 h-8 text-blue-600 dark:text-blue-400
.ml-4 flex-1
.text-lg font-medium text-gray-900 dark:text-white
.mt-1 text-gray-600 dark:text-gray-400
.mt-2
.inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
.mt-4 flex flex-col sm:flex-row gap-3
.inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors
.w-4 h-4 mr-2
.inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors
.mt-6 pt-6 border-t border-blue-200 dark:border-blue-700
.text-sm font-medium text-gray-900 dark:text-white mb-3
.grid grid-cols-1 md:grid-cols-3 gap-4
.bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600
.font-medium text-gray-900 dark:text-white
.text-2xl font-bold text-gray-900 dark:text-white
.text-sm text-gray-500
.text-sm text-gray-600 dark:text-gray-400
.bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-500 ring-1 ring-blue-500
```

## Usage Example
```blade
<x-subscription\upgrade.prompt
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-subscription\upgrade.prompt>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*