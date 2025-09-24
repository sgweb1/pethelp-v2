# Blade Component: homepage\hero-image

Hero Image Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\hero-image.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\hero-image.blade.php`
- **Użycie**: `<x-homepage\hero.image>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
Brak wykrytych slotów.

## CSS Classes
```css
.relative max-w-lg mx-auto
.relative z-10 group
.aspect-square overflow-hidden rounded-3xl shadow-2xl bg-white dark:bg-gray-800 p-4 transform hover:scale-105 transition-transform duration-300
.w-full h-full object-cover rounded-2xl
.absolute -bottom-4 -left-4 bg-green-500 text-white rounded-full p-3 shadow-lg z-20
.w-6 h-6
.absolute -top-6 -right-6 w-20 h-20 bg-yellow-400/20 rounded-full animate-pulse delay-200
.absolute -bottom-6 -left-2 w-16 h-16 bg-purple-400/20 rounded-full animate-pulse delay-700
.absolute top-1/2 -right-4 w-12 h-12 bg-blue-400/20 rounded-full animate-pulse delay-1200
.absolute top-8 right-8 text-red-400 animate-bounce delay-300
```

## Usage Example
```blade
<x-homepage\hero.image
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\hero.image>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*