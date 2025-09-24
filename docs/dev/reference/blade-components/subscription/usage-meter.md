# Blade Component: subscription\usage-meter

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla subscription\usage-meter.

## Lokalizacja
- **Plik**: `resources/views/components/subscription\usage-meter.blade.php`
- **Użycie**: `<x-subscription\usage.meter>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$label** - Slot dla label
- **$current** - Slot dla current
- **$percentage** - Slot dla percentage

## CSS Classes
```css
.flex items-center justify-between mb-2
.text-sm font-medium text-gray-700 dark:text-gray-300
.text-sm text-gray-500 dark:text-gray-400
.w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2
.h-2 rounded-full transition-all duration-300 {{ $isAtLimit ? 
.mt-2 flex items-center justify-between
.text-sm text-red-600 dark:text-red-400 font-medium
.text-sm text-yellow-600 dark:text-yellow-400 font-medium
.text-sm text-green-600 dark:text-green-400
.text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium
.h-2 rounded-full bg-green-500 w-full
.mt-2
.text-sm text-green-600 dark:text-green-400 font-medium
.w-4 h-4 inline mr-1
```

## Usage Example
```blade
<x-subscription\usage.meter
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-subscription\usage.meter>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*