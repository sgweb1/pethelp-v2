# Service: MapCacheService

Automatycznie wygenerowana dokumentacja dla serwisu.

## Opis
Serwis obsługujący logikę biznesową związaną z map-cache-service.

## Lokalizacja
- **Plik**: `app/Services/MapCacheService.php`

## Methods
### getCachedMapItems()
Opis metody getCachedMapItems.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getCachedMapItems();
```

### getCachedClusterData()
Opis metody getCachedClusterData.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getCachedClusterData();
```

### getCachedStatistics()
Opis metody getCachedStatistics.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getCachedStatistics();
```

### invalidateMapCache()
Opis metody invalidateMapCache.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->invalidateMapCache();
```

## Usage Example
```php
use App\Services\MapCacheService;

$service = app(MapCacheService::class);
// lub przez DI
public function __construct(private MapCacheService $service) {}
```

## Dependencies
Lista zależności używanych przez serwis.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*