# Blade Component: homepage\process-step

Process Step Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\process-step.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\process-step.blade.php`
- **Użycie**: `<x-homepage\process.step>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$gradientClass** - Slot dla gradientClass
- **$step** - Slot dla step
- **$title** - Slot dla title
- **$description** - Slot dla description

## CSS Classes
```css
.text-center group
.w-16 h-16 bg-gradient-to-r {{ $gradientClass }} rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6 group-hover:scale-110 transition-transform duration-300
.text-xl font-semibold text-gray-900 dark:text-white mb-4
.text-gray-600 dark:text-gray-300
```

## Usage Example
```blade
<x-homepage\process.step
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\process.step>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*