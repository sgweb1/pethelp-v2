# Livewire Component: AvailabilityCalendar

Automatycznie wygenerowana dokumentacja dla komponentu Livewire.

## Opis
Komponent Livewire obsługujący funkcjonalność availability-calendar.

## Lokalizacja
- **Klasa**: `app/Livewire/AvailabilityCalendar.php`
- **Widok**: `resources/views/livewire/availability-calendar.blade.php`



## Properties
- **`array` $selected_services** - 

## Methods
### toggleService()
Tablica wybranych usług dostępnych w slocie z ich typami

**Parameters:**
- `int $serviceId` - ID usługi do przełączenia

**Returns:**
- `void` 


### updateServiceType()
Aktualizuje typ usługi dla wybranej usługi

**Parameters:**
- `int $serviceId` - ID usługi
- `string $serviceType` - Nowy typ usługi

**Returns:**
- `void` 



## Usage Example
```blade
<livewire:AvailabilityCalendar
    wire:key="AvailabilityCalendar-{{ $id }}"
/>
```

## Events

### Events Emitted
- **availability-saved**
- **availability-deleted**
- **show-confirmation**
- **show-success-alert**

### Events Listened
- Brak wykrytych wydarzeń nasłuchiwanych

---
*Auto-generated documentation - last updated: 2025-09-24 10:09:28*
*🤖 Generated from PHPDoc comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*