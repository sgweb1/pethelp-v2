# API Controller: TrelloWebhookController

Automatycznie wygenerowana dokumentacja dla kontrolera API.

## Opis
Kontroler API obsługujący operacje związane z trello-webhook-controller.





## Endpoints

- **GET** `/api/trello-webhook-controller` - Handle Trello webhook callbacks
- **GET** `/api/trello-webhook-controller` - Handle HEAD requests for webhook verification

## Methods

### handleWebhook()
Handle Trello webhook callbacks


### verifyWebhook()
Handle HEAD requests for webhook verification



## Przykłady użycia

### Curl Examples
```bash
# GET request example
curl -X GET \
  'http://pethelp.test/api/trello-webhook-controller' \
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