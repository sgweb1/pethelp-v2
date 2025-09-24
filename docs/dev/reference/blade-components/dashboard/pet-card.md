# Blade Component: dashboard\pet-card

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla dashboard\pet-card.

## Lokalizacja
- **Plik**: `resources/views/components/dashboard\pet-card.blade.php`
- **Użycie**: `<x-dashboard\pet.card>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
Brak wykrytych slotów.

## CSS Classes
```css
.w-5 h-5
.bg-white/95 backdrop-blur-md rounded-2xl shadow-soft hover:shadow-medium transition-all duration-300 overflow-hidden group {{ $compact ? 
.aspect-square rounded-xl overflow-hidden mb-4 bg-gradient-to-br from-warm-100 to-nature-100
.w-full h-full object-cover group-hover:scale-105 transition-transform duration-300
.w-full h-full flex items-center justify-center
.w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center
.text-primary-600
.space-y-3
.flex items-center justify-between
.flex items-center space-x-2
.w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center text-primary-600
.font-semibold text-gray-900 {{ $compact ? 
.text-xs text-gray-500
.inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pet->is_active ? 
.grid grid-cols-2 gap-3 text-sm
.w-4 h-4 text-gray-400
.text-gray-600
.text-sm text-gray-600 line-clamp-2
.flex items-center space-x-2 p-2 bg-warning-50 border border-warning-200 rounded-lg
.w-4 h-4 text-warning-600
.text-xs text-warning-700
.pt-2
.w-4 h-4 mr-2
```

## Usage Example
```blade
<x-dashboard\pet.card
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-dashboard\pet.card>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*