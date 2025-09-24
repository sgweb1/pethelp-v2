# Blade Component: dashboard\stats-card

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla dashboard\stats-card.

## Lokalizacja
- **Plik**: `resources/views/components/dashboard\stats-card.blade.php`
- **Użycie**: `<x-dashboard\stats.card>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$cardClass** - Slot dla cardClass
- **$title** - Slot dla title
- **$value** - Slot dla value
- **$description** - Slot dla description

## CSS Classes
```css
.bg-white/95 backdrop-blur-md border {{ $cardClass }} rounded-2xl p-6 shadow-soft hover:shadow-medium transition-all duration-300
.flex items-center justify-between
.flex-1
.text-sm font-medium text-gray-600 mb-1
.flex items-baseline space-x-2
.text-2xl font-bold text-gray-900
.text-xs font-medium {{ $trend[
.inline w-3 h-3 mr-1
.text-xs text-gray-500 mt-1
.w-12 h-12 rounded-xl {{ $cardClass }} flex items-center justify-center
```

## Usage Example
```blade
<x-dashboard\stats.card
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-dashboard\stats.card>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*