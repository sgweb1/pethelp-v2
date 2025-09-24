# Blade Component: homepage\hero-stats

Hero Stats Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\hero-stats.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\hero-stats.blade.php`
- **Użycie**: `<x-homepage\hero.stats>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
Brak wykrytych slotów.

## CSS Classes
```css
.mt-12 grid grid-cols-3 gap-6 text-center lg:text-left
.text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white
.text-sm text-gray-500 dark:text-gray-400
```

## Usage Example
```blade
<x-homepage\hero.stats
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\hero.stats>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*