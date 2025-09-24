# 🔧 Przewodnik konfiguracji Trello dla PetHelp

## 1. Uzyskanie kluczy API

### Krok 1: Klucz API
1. Idź na https://trello.com/app-key
2. Zaloguj się do swojego konta Trello
3. Skopiuj **API Key** (32-znakowy ciąg)

### Krok 2: Token autoryzacyjny
1. Na tej samej stronie kliknij link "Token"
2. Zatwierdź uprawnienia dla aplikacji
3. Skopiuj **Token** (długi ciąg znaków)

## 2. Konfiguracja Laravel

### Edytuj plik .env:
```bash
# Zastąp these values:
TRELLO_API_KEY=your_32_character_api_key
TRELLO_TOKEN=your_64_character_token

# Opcjonalne (zostaną utworzone automatycznie):
TRELLO_BOARD_ID=
TRELLO_BOARD_URL=
```

### Wyczyść cache konfiguracji:
```bash
php artisan config:cache
```

## 3. Uruchomienie automatycznej konfiguracji

```bash
# Utworzy tablicę, listy i etykiety
php artisan trello:setup

# Opcjonalnie: Skonfiguruj webhook
php artisan trello:webhook create --url=https://your-domain.com/api/trello/webhook
```

## 4. Weryfikacja działania

### Sprawdź konfigurację:
```bash
php artisan config:show trello
```

### Sprawdź widget na dashboardzie:
Widget Trello powinien się załadować i pokazać statystyki tablicy.

## 5. Funkcje integracji

### Automatyczne akcje:
- ✨ Nowe usługi → automatyczne karty Trello
- 🔄 Zmiany statusu usług → przenoszenie kart między listami
- 📊 Widget dashboardu → statystyki postępu w czasie rzeczywistym

### Dostępne komendy:
```bash
# Zarządzanie tablicą
php artisan trello:setup [--force] [--import-tasks]

# Zarządzanie webhookami
php artisan trello:webhook create --url=YOUR_WEBHOOK_URL
php artisan trello:webhook list
php artisan trello:webhook delete
```

## 6. Rozwiązywanie problemów

### "Invalid key" error:
- Sprawdź czy API key i token są poprawnie skopiowane
- Uruchom `php artisan config:cache` po zmianie .env

### "Board not found":
- Usuń TRELLO_BOARD_ID z .env
- Uruchom ponownie `php artisan trello:setup`

### Webhook nie działa:
- Sprawdź czy URL jest publicznie dostępny
- Sprawdź logi Laravel: `tail -f storage/logs/laravel.log`

## 7. Bezpieczeństwo

⚠️ **WAŻNE:**
- Klucze API są poufne - nie commituj ich do repozytorium
- Używaj różnych kluczy dla środowisk deweloperskiego i produkcyjnego
- Webhook URL powinien używać HTTPS w produkcji