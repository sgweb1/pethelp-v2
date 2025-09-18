# System Czatu/Wiadomości - PetHelp

## 💬 Przegląd Systemu

System czatu PetHelp umożliwia bezpieczną komunikację między właścicielami zwierząt a opiekunami. Wiadomości są organizowane w konwersacje, mogą być powiązane z konkretną rezerwacją i obsługują załączniki.

## 🎯 Kluczowe Funkcjonalności

### Komunikacja
- **Prywatne rozmowy** - 1-na-1 między właścicielem a opiekunem
- **Kontekst rezerwacji** - Rozmowy mogą być powiązane z konkretną rezerwacją
- **Załączniki** - Obsługa zdjęć i plików (max 10MB)
- **Oznaczanie jako przeczytane** - Tracking nieprzeczytanych wiadomości
- **Edycja wiadomości** - 15-minutowe okno na edycję
- **Soft delete** - Usuwanie wiadomości (tylko dla wysyłającego)

### Organizacja Konwersacji
- **Lista rozmów** - Sortowana według ostatniej aktywności
- **Liczniki nieprzeczytanych** - Na poziomie rozmowy i globalnie
- **Automatyczne tworzenie** - Rozmowy tworzą się przy pierwszej wiadomości
- **Unikalność** - Jedna rozmowa między użytkownikami per rezerwacja

## 🏗️ Architektura Techniczna

### Modele Danych

#### Conversation Model
```php
// app/Models/Conversation.php
- owner_id_id, sitter_id_id (uporządkowane ID użytkowników)
- booking_id (opcjonalne powiązanie z rezerwacją)  
- subject (temat rozmowy)
- last_message_at (timestamp ostatniej wiadomości)
```

**Kluczowe Metody:**
- `getOtherUser(User $user)` - Zwraca drugiego uczestnika
- `hasUser(User $user)` - Sprawdza czy user uczestniczy
- `findOrCreateBetweenUsers()` - Znajdź/utwórz rozmowę
- `markAsReadForUser()` - Oznacz wszystkie jako przeczytane

#### Message Model  
```php
// app/Models/Message.php
- conversation_id (powiązanie z rozmową)
- sender_id (kto wysłał)
- content (treść wiadomości)
- type (text/image/file)
- attachment_path, attachment_name (załączniki)
- read_at (timestamp przeczytania)
- edited_at (timestamp edycji)
- is_deleted (soft delete)
```

**Kluczowe Metody:**
- `markAsRead()` - Oznacz jako przeczytane
- `markAsEdited()` - Oznacz jako edytowane  
- `softDelete()` - Usuń (soft delete)
- `hasAttachment()` - Sprawdź czy ma załącznik
- `getAttachmentSizeAttribute()` - Rozmiar pliku

### Kontroler API

#### MessageController
```php
// app/Http/Controllers/MessageController.php

GET    /conversations          # Lista rozmów użytkownika
GET    /conversations/{id}/messages  # Wiadomości w rozmowie
POST   /conversations/{id}/messages  # Wyślij wiadomość
POST   /conversations/start   # Rozpocznij nową rozmowę
PUT    /messages/{id}         # Edytuj wiadomość (15min)
DELETE /messages/{id}         # Usuń wiadomość
POST   /conversations/{id}/read  # Oznacz jako przeczytane
GET    /messages/unread-count # Liczba nieprzeczytanych
```

### Bezpieczeństwo i Autoryzacja

#### Zabezpieczenia
- **Autoryzacja** - Tylko uczestnicy rozmowy mają dostęp
- **Walidacja booking** - Sprawdzanie uprawnień do rezerwacji
- **Self-messaging** - Blokada wysyłania do siebie
- **File upload** - Ograniczenia rozmiaru i typu plików
- **Time limits** - 15min na edycję wiadomości

#### Rate Limiting
- Upload plików: 10MB max
- Długość wiadomości: 5000 znaków max
- Batch operations: Paginacja 20/50 elementów

## 💾 Struktura Bazy Danych

### Tabela `conversations`
```sql
- id (Primary Key)
- owner_id_id (Foreign Key -> users.id) 
- sitter_id_id (Foreign Key -> users.id)
- booking_id (Foreign Key -> bookings.id, nullable)
- subject (varchar, nullable)
- last_message_at (timestamp, nullable)
- created_at, updated_at

UNIQUE KEY (owner_id_id, sitter_id_id, booking_id)
INDEX (owner_id_id, sitter_id_id)
```

### Tabela `messages` 
```sql
- id (Primary Key)
- conversation_id (Foreign Key -> conversations.id)
- sender_id (Foreign Key -> users.id)
- content (text)
- type (enum: text, image, file)
- attachment_path (varchar, nullable)
- attachment_name (varchar, nullable)  
- read_at (timestamp, nullable)
- edited_at (timestamp, nullable)
- is_deleted (boolean, default false)
- created_at, updated_at

INDEX (conversation_id, created_at)
INDEX (sender_id, created_at)  
INDEX (read_at)
```

## 🔄 Integracje z Systemem

### User Model Extensions
```php
// app/Models/User.php
- getAllConversations() # Lista rozmów użytkownika
- getUnreadMessagesCount() # Liczba nieprzeczytanych wiadomości
- sentMessages() # Relacja do wysłanych wiadomości
```

### Automatic Updates
- **last_message_at** - Automatyczna aktualizacja przy nowej wiadomości
- **Conversation creation** - Auto-tworzenie przy pierwszej wiadomości
- **File cleanup** - TODO: Cleanup unused attachment files

## 📱 Funkcjonalności Użytkownika

### Lista Rozmów
- Sortowanie według ostatniej aktywności
- Podgląd ostatniej wiadomości
- Liczniki nieprzeczytanych wiadomości
- Informacje o powiązanej rezerwacji
- Avatar i nazwa rozmówcy

### Widok Rozmowy
- **Scroll do najnowszych** - Automatyczne przewijanie
- **Message bubbles** - Różne style dla własnych/obcych wiadomości  
- **Timestamps** - Czytelne daty i czasy
- **Read receipts** - Status przeczytania
- **Edit indicators** - Oznaczenie edytowanych wiadomości
- **Attachment previews** - Podgląd zdjęć, download plików

### Wysyłanie Wiadomości
- **Auto-resize textarea** - Rozszerzające się pole tekstowe
- **File upload** - Drag&drop lub click to upload
- **Send shortcuts** - Enter to send (Shift+Enter = nowa linia)
- **Typing indicators** - TODO: Real-time typing status
- **Emoji support** - Wsparcie dla emoji w wiadomościach

## 🎨 Frontend (Do Implementacji)

### Komponenty Vue.js

#### ConversationList.vue
```vue
// Lista rozmów użytkownika
- Infinite scroll dla dużych list
- Search/filter rozmów  
- Unread badges
- Context menu (archive, delete)
- Real-time updates
```

#### MessageChat.vue  
```vue
// Główny komponent rozmowy
- Message history z lazy loading
- Real-time message updates
- Scroll to bottom functionality
- Message status indicators
- File preview modals
```

#### MessageInput.vue
```vue 
// Input do wysyłania wiadomości
- Auto-expanding textarea
- File upload progress
- Emoji picker
- Send button states
- Draft saving (localStorage)
```

#### MessageBubble.vue
```vue
// Pojedyncza wiadomość
- Different styles for own/other messages
- Timestamp formatting  
- Edit/delete context menu
- Attachment rendering
- Read status display
```

### Ruty Frontend
```javascript
// resources/js/Pages/
- Messages.vue         # Główna strona wiadomości
- MessageThread.vue    # Konkretna rozmowa

// URL Routes  
/messages              # Lista rozmów
/messages/{id}         # Konkretna rozmowa
```

## 🔔 Integracja z Powiadomieniami

### Powiadomienia Email (Do Implementacji)
```php
// app/Notifications/NewMessageNotification.php
- Email notification dla nowych wiadomości
- Batch notifications (nie spam dla każdej wiadomości)
- Unsubscribe options w email settings
```

### Real-time Updates (Do Implementacji)
```javascript
// WebSockets lub Server-Sent Events
- New message notifications
- Typing indicators
- Online status
- Message read receipts
```

## ⚡ Performance i Optymalizacja

### Database Indexing
- Composite index na (conversation_id, created_at) dla messages
- Index na read_at dla szybkich zapytań o nieprzeczytane
- Index na (owner_id_id, sitter_id_id) dla conversations

### Caching Strategy
```php
// Redis caching dla aktywnych rozmów
- Cache user conversation lists
- Cache unread counts  
- Cache recent messages per conversation
- Invalidation na message create/update
```

### File Storage
- Storage w `storage/app/public/messages/`
- Unique filenames zapobiegają kolizjom
- TODO: CDN integration dla dużego ruchu
- TODO: Auto-cleanup starych plików

## 🧪 Testing Strategy

### Unit Tests
```php
// tests/Unit/Models/
ConversationTest.php   # Test model methods
MessageTest.php        # Test message operations
UserMessagingTest.php  # Test user integrations
```

### Feature Tests  
```php
// tests/Feature/
MessageControllerTest.php     # API endpoints
ConversationFlowTest.php      # Complete user flows
FileUploadTest.php           # Attachment handling
AuthorizationTest.php        # Security testing
```

### Frontend Testing
```javascript
// tests/js/
MessageComponents.test.js     # Vue component tests
MessageIntegration.test.js    # E2E conversation flows
```

## 🚀 Deployment Considerations

### Production Setup
- **Queue Workers** - Background processing dla file operations
- **File Storage** - S3/CloudFlare dla attachments w production  
- **Database** - Connection pooling dla concurrent users
- **Monitoring** - Logging message volume i performance metrics

### Scaling
- **Horizontal scaling** - Sharding conversations per user_id
- **Message archiving** - Move old messages to cold storage
- **CDN** - Serve static attachments via CDN
- **WebSocket servers** - Separate real-time infrastructure

---

## ✅ Status Implementacji

### ✅ UKOŃCZONE (Backend):
- [x] Database schema (conversations, messages tables)
- [x] Conversation model z business logic
- [x] Message model z attachment support
- [x] MessageController z pełnym API
- [x] User model integration
- [x] Authorization i security
- [x] File upload handling
- [x] Soft delete functionality
- [x] Read status tracking
- [x] Message editing (15min window)

### 🔄 DO IMPLEMENTACJI (Frontend):
- [ ] Vue.js components (ConversationList, MessageChat, MessageInput)
- [ ] API routes w web.php
- [ ] Strona Messages.vue
- [ ] Real-time updates (polling/WebSockets)
- [ ] File upload UI z progress
- [ ] Message notifications integration
- [ ] Responsive design dla mobile
- [ ] Emoji picker
- [ ] Message search functionality

### 🎯 PRZYSZŁE ROZSZERZENIA:
- [ ] Typing indicators
- [ ] Voice messages
- [ ] Video calls integration
- [ ] Message reactions (thumbs up, heart)
- [ ] Group conversations (multiple users)
- [ ] Message templates/quick replies
- [ ] Conversation archiving
- [ ] Advanced search w wiadomościach

**Backend systemu czatu jest w pełni funkcjonalny i gotowy do integracji z frontendem! 🎉**

Główne funkcje:
- ✅ Bezpieczna komunikacja 1-na-1
- ✅ Powiązanie z rezerwacjami
- ✅ Załączniki (zdjęcia/pliki)  
- ✅ Oznaczanie jako przeczytane
- ✅ Edycja i usuwanie wiadomości
- ✅ Kontrola uprawnień
- ✅ Scalable database design