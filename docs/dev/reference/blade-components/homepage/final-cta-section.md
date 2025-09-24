# Blade Component: homepage\final-cta-section

Final CTA Section Component

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla homepage\final-cta-section.

## Lokalizacja
- **Plik**: `resources/views/components/homepage\final-cta-section.blade.php`
- **Użycie**: `<x-homepage\final.cta.section>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
Brak wykrytych slotów.

## CSS Classes
```css
.py-16 lg:py-24 bg-gradient-to-r from-blue-600 to-purple-600
.max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center
.text-3xl lg:text-4xl font-bold text-white mb-6
.text-xl text-blue-100 mb-12 max-w-3xl mx-auto
.flex flex-col sm:flex-row gap-4 justify-center
.inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-600 bg-white hover:bg-gray-100 rounded-lg transition-colors duration-300
.w-5 h-5 mr-2
.inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-white hover:bg-white hover:text-blue-600 rounded-lg transition-all duration-300
```

## Usage Example
```blade
<x-homepage\final.cta.section
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-homepage\final.cta.section>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*