# Service: SearchCacheService

Automatycznie wygenerowana dokumentacja dla serwisu.

## Opis
Serwis obsługujący logikę biznesową związaną z search-cache-service.

## Lokalizacja
- **Plik**: `app/Services/SearchCacheService.php`

## Methods
### getCachedSearchResults()
Opis metody getCachedSearchResults.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getCachedSearchResults();
```

### performSearch()
Opis metody performSearch.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->performSearch();
```

### invalidateSearchCache()
Opis metody invalidateSearchCache.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->invalidateSearchCache();
```

### getSearchAnalytics()
Opis metody getSearchAnalytics.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getSearchAnalytics();
```

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

### getCachedMapStatistics()
Opis metody getCachedMapStatistics.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getCachedMapStatistics();
```

## Usage Example
```php
use App\Services\SearchCacheService;

$service = app(SearchCacheService::class);
// lub przez DI
public function __construct(private SearchCacheService $service) {}
```

## Dependencies
Lista zależności używanych przez serwis.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*