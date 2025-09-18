# Dokumentacja - System Zarządzania Zdjęciami dla Opiekunów

## Przegląd
System umożliwia opiekunom zwierząt (sitters) zarządzanie zdjęciem profilowym oraz galerią zdjęć (do 8 zdjęć).

## Struktura Plików

### 1. Backend - Laravel

#### `app/Http/Controllers/PhotoController.php`
Główny kontroler obsługujący operacje na zdjęciach:

**Metody:**
- `uploadProfilePhoto()` - Upload zdjęcia profilowego
- `removeProfilePhoto()` - Usunięcie zdjęcia profilowego  
- `uploadGalleryPhoto()` - Dodanie zdjęcia do galerii
- `deleteGalleryPhoto()` - Usunięcie zdjęcia z galerii
- `reorderGalleryPhotos()` - Zmiana kolejności zdjęć w galerii

**Kluczowa poprawka:**
```php
// Przed poprawką (nie działało):
$profile->update(['profile_photo' => $photoUrl]);

// Po poprawce (działa):
$profile->profile_photo = $photoUrl;
$profile->save();
```

#### Routing (`routes/web.php`)
```php
// Photo management
Route::post('sitter/photos/profile', [PhotoController::class, 'uploadProfilePhoto']);
Route::delete('sitter/photos/profile', [PhotoController::class, 'removeProfilePhoto']);
Route::post('sitter/photos/gallery', [PhotoController::class, 'uploadGalleryPhoto']);
Route::delete('sitter/photos/gallery', [PhotoController::class, 'deleteGalleryPhoto']);
Route::put('sitter/photos/gallery/reorder', [PhotoController::class, 'reorderGalleryPhotos']);
```

#### Baza Danych
W tabeli `sitter_profiles`:
- `profile_photo` (VARCHAR) - URL zdjęcia profilowego
- `gallery_photos` (TEXT) - JSON array z URL-ami zdjęć galerii

### 2. Frontend - Vue.js

#### `resources/js/Components/Sitter/PhotoManager.vue`
Komponent zarządzania zdjęciami:

**Funkcjonalności:**
- Upload zdjęcia profilowego (do 5MB, JPEG/PNG)
- Upload zdjęć galerii (max 8 zdjęć)
- Usuwanie zdjęć
- Zmiana kolejności zdjęć w galerii
- Wskaźniki postępu uploadu
- Komunikaty sukcesu/błędów

**Emitowane eventy:**
```typescript
const emit = defineEmits<{
    'photo-updated': [];
}>();
```

#### `resources/js/Components/Dashboard/SitterDashboard.vue`
Integracja z PhotoManager:

```vue
<PhotoManager 
    :user-initials="user?.name?.split(' ').map(n => n[0]).join('') || 'S'"
    :initial-profile-photo="profilePhoto"
    :initial-gallery-photos="galleryPhotos"
    @photo-updated="handlePhotoUpdated"
/>
```

## Rozwiązane Problemy

### 1. Problem Persistencji Zdjęć
**Objaw:** Zdjęcia znikały po odświeżeniu strony
**Przyczyna:** Metoda `update()` Eloquent nie zapisywała danych do bazy
**Rozwiązanie:** Zmiana na bezpośrednie przypisanie + `save()`

### 2. Błędy JSON Decode
**Objaw:** `json_decode(): Argument #1 ($json) must be of type string, array given`
**Rozwiązanie:** Sprawdzanie typu przed dekodowaniem JSON

### 3. Komunikacja Komponentów
**Objaw:** Zmiany w PhotoManager nie odświeżały dashboardu
**Rozwiązanie:** System eventów `@photo-updated`

## Konfiguracja Środowiska

### Storage
```bash
php artisan storage:link
```

### Struktura Katalogów
Zdjęcia są organizowane według ID użytkownika:
- Struktura: `sitter-photos/{type}/{ceil(user_id/1000)}/{user_id}/`
- Katalog nadrzędny: ID 1-1000 → folder 1, ID 1001-2000 → folder 2, etc.

**Przykłady:**
- User ID 35: `storage/app/public/sitter-photos/profiles/1/35/`
- User ID 1234: `storage/app/public/sitter-photos/profiles/2/1234/`  
- User ID 1999: `storage/app/public/sitter-photos/gallery/2/1999/`
- User ID 2001: `storage/app/public/sitter-photos/gallery/3/2001/`

### Uprawnienia
- Automatyczne tworzenie folderów według struktury ID użytkownika
- Organizacja w podkatalogi co 1000 użytkowników dla lepszej wydajności

## Walidacja

### Upload Plików
```php
$request->validate([
    'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
]);
```

### Galeria
- Maksymalnie 8 zdjęć
- Walidacja URL-i przy zmianie kolejności

## API Responses

### Sukces Upload Zdjęcia Profilowego
```json
{
    "message": "Profile photo updated successfully",
    "photo_url": "/storage/sitter-photos/profiles/xyz.jpg"
}
```

### Sukces Upload Zdjęcia Galerii
```json
{
    "message": "Gallery photo added successfully",
    "photo_url": "/storage/sitter-photos/gallery/abc.jpg",
    "gallery_photos": ["url1", "url2", "..."]
}
```

## Testowanie

### Test Persistencji
1. Upload zdjęcia profilowego
2. Odświeżenie strony
3. Sprawdzenie czy zdjęcie się utrzymało

### Test Galerii
1. Upload wielu zdjęć
2. Zmiana kolejności
3. Usunięcie zdjęcia
4. Sprawdzenie persistencji po odświeżeniu

## Logi Debugowania

Włączone szczegółowe logowanie w `PhotoController.php`:
```php
\Log::info('PhotoController: Upload attempt', [
    'user_id' => $user->id,
    'profile_id' => $profile->id,
    'update_result' => $updateResult
]);
```

## Status Implementacji
✅ **Zakończone:**
- Upload zdjęcia profilowego
- Upload zdjęć galerii  
- Usuwanie zdjęć
- Zmiana kolejności
- Persistencja po odświeżeniu strony
- Walidacja i error handling
- Responsywny interface

Wszystkie funkcjonalności zostały przetestowane i działają poprawnie.