# Blade Component: guest-layout

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla guest-layout.

## Lokalizacja
- **Plik**: `resources/views/components/guest-layout.blade.php`
- **Użycie**: `<x-guest.layout>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$slot** - Slot dla slot

## CSS Classes
```css
.font-sans text-gray-900 antialiased
.min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-500 via-purple-500 to-purple-700
.mb-6
.flex items-center
.text-4xl mr-3
.text-4xl font-bold text-white
.w-full sm:max-w-md mt-6 px-6 py-4 bg-white/95 backdrop-blur-md shadow-md overflow-hidden sm:rounded-lg
```

## Usage Example
```blade
<x-guest.layout
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-guest.layout>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*