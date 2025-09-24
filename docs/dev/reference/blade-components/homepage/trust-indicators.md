# Blade Component: homepage\trust-indicators

Trust Indicators Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\trust-indicators.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\trust-indicators.blade.php`
- **Użycie**: `<x-homepage\trust.indicators>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
Brak wykrytych slotów.

## CSS Classes
```css
.flex flex-wrap items-center justify-center lg:justify-start gap-6 text-sm
.flex items-center bg-green-50 dark:bg-green-900/20 px-4 py-2 rounded-full border border-green-200 dark:border-green-800
.w-5 h-5 text-green-600 dark:text-green-400 mr-2
.font-medium text-green-700 dark:text-green-300
.flex items-center bg-red-50 dark:bg-red-900/20 px-4 py-2 rounded-full border border-red-200 dark:border-red-800
.w-5 h-5 text-red-600 dark:text-red-400 mr-2
.font-medium text-red-700 dark:text-red-300
.flex items-center bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-full border border-blue-200 dark:border-blue-800
.w-5 h-5 text-blue-600 dark:text-blue-400 mr-2
.font-medium text-blue-700 dark:text-blue-300
```

## Usage Example
```blade
<x-homepage\trust.indicators
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\trust.indicators>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*