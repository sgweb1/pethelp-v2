# Blade Component: dropdown

Automatycznie wygenerowana dokumentacja dla komponentu Blade.

## Opis
Komponent Blade wyświetlający interfejs użytkownika dla dropdown.

## Lokalizacja
- **Plik**: `resources/views/components/dropdown.blade.php`
- **Użycie**: `<x-dropdown>`

## Parameters
Brak zdefiniowanych parametrów.

## Slots
- **$trigger** - Slot dla trigger
- **$width** - Slot dla width
- **$alignmentClasses** - Slot dla alignmentClasses
- **$contentClasses** - Slot dla contentClasses
- **$content** - Slot dla content

## CSS Classes
```css
.relative
.absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}
.rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}
```

## Usage Example
```blade
<x-dropdown
    :param="$value"
    :param2="$value2"
>
    Slot content
</x-dropdown>
```

## Dependencies
Brak wykrytych zależności.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*🤖 Generated from Blade comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*