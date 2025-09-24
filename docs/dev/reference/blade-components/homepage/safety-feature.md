# Blade Component: homepage\safety-feature

Safety Feature Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\safety-feature.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\safety-feature.blade.php`
- **Użycie**: `<x-homepage\safety.feature>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$title** - Slot dla title
- **$description** - Slot dla description

## CSS Classes
```css
.flex items-start
.flex-shrink-0
.w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center
.w-5 h-5 text-green-600 dark:text-green-400
.ml-4
.text-lg font-semibold text-gray-900 dark:text-white
.text-gray-600 dark:text-gray-300
```

## Usage Example
```blade
<x-homepage\safety.feature
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\safety.feature>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*