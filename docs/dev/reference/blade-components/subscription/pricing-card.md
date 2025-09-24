# Blade Component: subscription\pricing-card

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla subscription\pricing-card.

## Lokalizacja
- **Plik**: `resources/views/components/subscription\pricing-card.blade.php`
- **Użycie**: `<x-subscription\pricing.card>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$label** - Slot dla label

## CSS Classes
```css
.relative bg-white dark:bg-gray-800 rounded-lg border {{ $isPopular ? 
.absolute -top-3 left-1/2 transform -translate-x-1/2
.bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium
.absolute -top-3 right-4
.bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium
.text-center
.text-xl font-bold text-gray-900 dark:text-white
.text-gray-600 dark:text-gray-400 mt-2
.mt-4
.text-4xl font-bold text-gray-900 dark:text-white
.text-gray-600 dark:text-gray-400
.text-sm text-green-600 dark:text-green-400 mt-1
.mt-6
.space-y-3
.flex items-center
.w-5 h-5 text-green-500 mr-3
.text-gray-700 dark:text-gray-300
.mt-8
.w-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 py-3 px-4 rounded-lg font-medium
.w-full
.w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg font-medium transition-colors
.w-full {{ $isPopular ? 
.text-xs text-gray-500 dark:text-gray-400 text-center mt-3
```

## Usage Example
```blade
<x-subscription\pricing.card
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-subscription\pricing.card>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*