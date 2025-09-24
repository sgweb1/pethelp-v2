# Service: LocationSearchService

Automatycznie wygenerowana dokumentacja dla serwisu.

## Opis
Serwis obsługujący logikę biznesową związaną z location-search-service.

## Lokalizacja
- **Plik**: `app/Services/LocationSearchService.php`

## Methods
### searchLocations()
Opis metody searchLocations.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->searchLocations();
```

### searchHierarchical()
Opis metody searchHierarchical.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->searchHierarchical();
```

### getLocationDetails()
Opis metody getLocationDetails.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getLocationDetails();
```

### getCoordinates()
Opis metody getCoordinates.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getCoordinates();
```

## Usage Example
```php
use App\Services\LocationSearchService;

$service = app(LocationSearchService::class);
// lub przez DI
public function __construct(private LocationSearchService $service) {}
```

## Dependencies
Lista zależności używanych przez serwis.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*