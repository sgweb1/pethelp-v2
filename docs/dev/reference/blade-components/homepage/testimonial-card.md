# Blade Component: homepage\testimonial-card

Testimonial Card Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\testimonial-card.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\testimonial-card.blade.php`
- **Użycie**: `<x-homepage\testimonial.card>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$review** - Slot dla review
- **$name** - Slot dla name
- **$location** - Slot dla location

## CSS Classes
```css
.bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm
.flex items-center mb-4
.flex text-yellow-400
.w-5 h-5
.text-gray-600 dark:text-gray-300 mb-4
.flex items-center
.w-10 h-10 rounded-full mr-3 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold text-sm
.font-semibold text-gray-900 dark:text-white
.text-sm text-gray-500 dark:text-gray-400
```

## Usage Example
```blade
<x-homepage\testimonial.card
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\testimonial.card>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*