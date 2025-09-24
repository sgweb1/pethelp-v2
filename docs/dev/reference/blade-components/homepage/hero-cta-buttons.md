# Blade Component: homepage\hero-cta-buttons

Hero CTA Buttons Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\hero-cta-buttons.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\hero-cta-buttons.blade.php`
- **Użycie**: `<x-homepage\hero.cta.buttons>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
Brak wykrytych slotów.

## CSS Classes
```css
.flex flex-col sm:flex-row gap-4 justify-center lg:justify-start
.group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1
.w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300
.w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300
.group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 border-2 border-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-md hover:shadow-lg
```

## Usage Example
```blade
<x-homepage\hero.cta.buttons
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\hero.cta.buttons>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*