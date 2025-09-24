# Service: ServiceSearchService

Automatycznie wygenerowana dokumentacja dla serwisu.

## Opis
Serwis obsługujący logikę biznesową związaną z service-search-service.

## Lokalizacja
- **Plik**: `app/Services/ServiceSearchService.php`

## Methods
### search()
Opis metody search.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->search();
```

### clearCache()
Opis metody clearCache.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->clearCache();
```

## Usage Example
```php
use App\Services\ServiceSearchService;

$service = app(ServiceSearchService::class);
// lub przez DI
public function __construct(private ServiceSearchService $service) {}
```

## Dependencies
Lista zależności używanych przez serwis.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*