# API Controller: UnifiedSearchController

🚀 Unified Search API Controller - Airbnb-style single endpoint

## Opis
Kontroler API obsługujący operacje związane z unified-search-controller.





## Endpoints

- **GET** `/api/unified-search-controller` - 🚀 Unified Search API Controller - Airbnb-style single endpoint
- **GET** `/api/unified-search-controller` - 🗺️ Format results for map display

## Methods

### search()
🚀 Unified Search API Controller - Airbnb-style single endpoint


### stats()
🗺️ Format results for map display



## Przykłady użycia

### Curl Examples
```bash
# GET request example
curl -X GET \
  'http://pethelp.test/api/unified-search-controller' \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer YOUR_TOKEN'
```

## Response Formats

### Success Response
```json
{
  "data": {},
  "message": "Success",
  "status": 200
}
```

### Error Response
```json
{
  "message": "Error message",
  "errors": {},
  "status": 422
}
```

---
*Auto-generated documentation - last updated: 2025-09-24 10:10:20*
*🤖 Generated from PHPDoc comments*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*