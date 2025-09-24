# Blade Component: homepage\faq-item

FAQ Item Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\faq-item.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\faq-item.blade.php`
- **Użycie**: `<x-homepage\faq.item>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$question** - Slot dla question
- **$answer** - Slot dla answer

## CSS Classes
```css
.bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm
.text-lg font-semibold text-gray-900 dark:text-white mb-3
.text-gray-600 dark:text-gray-300
```

## Usage Example
```blade
<x-homepage\faq.item
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\faq.item>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*