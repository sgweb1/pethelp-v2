# Blade Component: toast-container

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla toast-container.

## Lokalizacja
- **Plik**: `resources/views/components/toast-container.blade.php`
- **Użycie**: `<x-toast.container>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
Brak wykrytych slotów.

## CSS Classes
```css
.fixed top-4 right-4 z-50 space-y-2
.getTypeClasses(notification.type)
.max-w-sm w-full shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden
.p-4
.flex items-start
.flex-shrink-0
.text-lg
.ml-3 w-0 flex-1 pt-0.5
.text-sm font-medium
.ml-4 flex-shrink-0 flex
.inline-flex text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white
.h-5 w-5
```

## Usage Example
```blade
<x-toast.container
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-toast.container>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*