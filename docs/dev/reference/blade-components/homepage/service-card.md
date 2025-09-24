# Blade Component: homepage\service-card

Service Card Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\service-card.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\service-card.blade.php`
- **Użycie**: `<x-homepage\service.card>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$iconColorClass** - Slot dla iconColorClass
- **$icon** - Slot dla icon
- **$title** - Slot dla title
- **$description** - Slot dla description

## CSS Classes
```css
.bg-white dark:bg-gray-900 rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300
.w-16 h-16 {{ $iconColorClass }} rounded-full flex items-center justify-center mx-auto mb-4
.w-8 h-8
.text-lg font-semibold text-gray-900 dark:text-white mb-2
.text-gray-600 dark:text-gray-300 text-sm
```

## Usage Example
```blade
<x-homepage\service.card
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\service.card>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*