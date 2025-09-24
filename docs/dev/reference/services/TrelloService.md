# Service: TrelloService

Automatycznie wygenerowana dokumentacja dla serwisu.

## Opis
Serwis obsługujący logikę biznesową związaną z trello-service.

## Lokalizacja
- **Plik**: `app/Services/TrelloService.php`

## Methods
### createPetHelpBoard()
Opis metody createPetHelpBoard.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->createPetHelpBoard();
```

### createProjectLists()
Opis metody createProjectLists.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->createProjectLists();
```

### createProjectLabels()
Opis metody createProjectLabels.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->createProjectLabels();
```

### createCard()
Opis metody createCard.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->createCard();
```

### moveCard()
Opis metody moveCard.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->moveCard();
```

### addComment()
Opis metody addComment.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->addComment();
```

### updateCardProgress()
Opis metody updateCardProgress.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->updateCardProgress();
```

### getBoardStats()
Opis metody getBoardStats.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getBoardStats();
```

### importCurrentTasks()
Opis metody importCurrentTasks.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->importCurrentTasks();
```

### createWebhook()
Opis metody createWebhook.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->createWebhook();
```

### getWebhooks()
Opis metody getWebhooks.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->getWebhooks();
```

### deleteWebhook()
Opis metody deleteWebhook.

**Parameters:**
- Lista parametrów

**Returns:**
- Typ zwracany

**Example:**
```php
$result = $this->deleteWebhook();
```

## Usage Example
```php
use App\Services\TrelloService;

$service = app(TrelloService::class);
// lub przez DI
public function __construct(private TrelloService $service) {}
```

## Dependencies
Lista zależności używanych przez serwis.

---
*Auto-generated documentation - last updated: 2025-09-24 10:15:47*
*📝 Edytuj ten plik aby dodać dodatkowe informacje*