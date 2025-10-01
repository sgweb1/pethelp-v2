<?php

namespace App\Helpers;

/**
 * Helper do zarządzania strukturą katalogów zdjęć użytkowników.
 *
 * Organizuje zdjęcia w katalogi grupowane po 1000 użytkowników, gdzie każdy
 * użytkownik ma swój własny podkatalog na podstawie ID.
 *
 * Struktura katalogów:
 * - photos/users/0000-0999/123/profile/
 * - photos/users/0000-0999/123/home/
 * - photos/users/1000-1999/1234/profile/
 * - photos/users/1000-1999/1234/home/
 *
 * @package App\Helpers
 * @author Claude AI Assistant
 * @version 1.0.0
 */
class PhotoStorageHelper
{
    /**
     * Rozmiar grupy użytkowników w jednym katalogu.
     */
    const USERS_PER_DIRECTORY = 1000;

    /**
     * Generuje ścieżkę do katalogu zdjęć użytkownika.
     *
     * Używa modulo do określenia grupy katalogów i tworzy indywidualną
     * ścieżkę dla każdego użytkownika na podstawie jego ID.
     *
     * @param int $userId ID użytkownika
     * @param string $photoType Typ zdjęcia (profile, home, pet, etc.)
     * @return string Względna ścieżka do katalogu zdjęć
     *
     * @example
     * // Dla userId = 123, photoType = 'profile'
     * // Zwraca: "users/0000-0999/123/profile"
     *
     * // Dla userId = 1234, photoType = 'home'
     * // Zwraca: "users/1000-1999/1234/home"
     */
    public static function generateUserPhotoPath(int $userId, string $photoType): string
    {
        // Oblicz grupę katalogów używając modulo
        $groupStart = intval($userId / self::USERS_PER_DIRECTORY) * self::USERS_PER_DIRECTORY;
        $groupEnd = $groupStart + self::USERS_PER_DIRECTORY - 1;

        // Formatuj zakres grupy z zerami wiodącymi
        $groupRange = sprintf('%04d-%04d', $groupStart, $groupEnd);

        // Zwróć pełną ścieżkę
        return "users/{$groupRange}/{$userId}/{$photoType}";
    }

    /**
     * Generuje pełną ścieżkę do zapisu zdjęcia profilowego użytkownika.
     *
     * @param int $userId ID użytkownika
     * @param string $filename Nazwa pliku
     * @return string Pełna ścieżka do zapisu
     */
    public static function generateProfilePhotoPath(int $userId, string $filename): string
    {
        $basePath = self::generateUserPhotoPath($userId, 'profile');
        return "{$basePath}/{$filename}";
    }

    /**
     * Generuje pełną ścieżkę do zapisu zdjęcia domu użytkownika.
     *
     * @param int $userId ID użytkownika
     * @param string $filename Nazwa pliku
     * @return string Pełna ścieżka do zapisu
     */
    public static function generateHomePhotoPath(int $userId, string $filename): string
    {
        $basePath = self::generateUserPhotoPath($userId, 'home');
        return "{$basePath}/{$filename}";
    }

    /**
     * Generuje pełną ścieżkę do zapisu zdjęcia zwierzaka użytkownika.
     *
     * @param int $userId ID użytkownika
     * @param string $filename Nazwa pliku
     * @return string Pełna ścieżka do zapisu
     */
    public static function generatePetPhotoPath(int $userId, string $filename): string
    {
        $basePath = self::generateUserPhotoPath($userId, 'pets');
        return "{$basePath}/{$filename}";
    }

    /**
     * Zwraca informacje o grupie katalogów dla użytkownika.
     *
     * Przydatne do debugowania i logowania.
     *
     * @param int $userId ID użytkownika
     * @return array Informacje o grupie
     */
    public static function getUserGroupInfo(int $userId): array
    {
        $groupStart = intval($userId / self::USERS_PER_DIRECTORY) * self::USERS_PER_DIRECTORY;
        $groupEnd = $groupStart + self::USERS_PER_DIRECTORY - 1;
        $groupRange = sprintf('%04d-%04d', $groupStart, $groupEnd);

        return [
            'user_id' => $userId,
            'group_start' => $groupStart,
            'group_end' => $groupEnd,
            'group_range' => $groupRange,
            'users_per_directory' => self::USERS_PER_DIRECTORY,
            'base_path' => "users/{$groupRange}/{$userId}"
        ];
    }

    /**
     * Sprawdza czy katalog użytkownika powinien być utworzony.
     *
     * @param int $userId ID użytkownika
     * @param string $photoType Typ zdjęcia
     * @return bool True jeśli katalog nie istnieje
     */
    public static function shouldCreateDirectory(int $userId, string $photoType): bool
    {
        $path = self::generateUserPhotoPath($userId, $photoType);
        $fullPath = storage_path("app/public/{$path}");

        return !is_dir($fullPath);
    }

    /**
     * Tworzy katalog użytkownika jeśli nie istnieje.
     *
     * @param int $userId ID użytkownika
     * @param string $photoType Typ zdjęcia
     * @return bool True jeśli katalog został utworzony lub już istnieje
     */
    public static function ensureDirectoryExists(int $userId, string $photoType): bool
    {
        $path = self::generateUserPhotoPath($userId, $photoType);
        $fullPath = storage_path("app/public/{$path}");

        if (!is_dir($fullPath)) {
            return mkdir($fullPath, 0755, true);
        }

        return true;
    }

    /**
     * Czyści starsze zdjęcia użytkownika (opcjonalne).
     *
     * Może być używane do czyszczenia starych zdjęć przy upload nowych.
     *
     * @param int $userId ID użytkownika
     * @param string $photoType Typ zdjęcia
     * @param int $keepLatest Ile najnowszych plików zachować
     * @return int Liczba usuniętych plików
     */
    public static function cleanupOldPhotos(int $userId, string $photoType, int $keepLatest = 5): int
    {
        $path = self::generateUserPhotoPath($userId, $photoType);
        $fullPath = storage_path("app/public/{$path}");

        if (!is_dir($fullPath)) {
            return 0;
        }

        $files = glob($fullPath . '/*');

        \Log::info('📸 Cleanup - files before sort', [
            'count' => count($files),
            'files' => array_map('basename', $files)
        ]);

        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a); // Sortuj po dacie modyfikacji (najnowsze pierwsze)
        });

        \Log::info('📸 Cleanup - files after sort', [
            'keepLatest' => $keepLatest,
            'filesToKeep' => array_map('basename', array_slice($files, 0, $keepLatest)),
            'filesToDelete' => array_map('basename', array_slice($files, $keepLatest))
        ]);

        $deleted = 0;
        for ($i = $keepLatest; $i < count($files); $i++) {
            if (is_file($files[$i])) {
                \Log::info('📸 Deleting old photo', ['file' => basename($files[$i])]);
                unlink($files[$i]);
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Czyści stare tymczasowe pliki Livewire (starsze niż 24h).
     *
     * Livewire tworzy tymczasowe pliki podczas uploadu, które czasami
     * mogą zostać jeśli proces został przerwany.
     *
     * @param int $olderThanHours Usuń pliki starsze niż X godzin (domyślnie 24h)
     * @return int Liczba usuniętych plików
     */
    public static function cleanupLivewireTempFiles(int $olderThanHours = 24): int
    {
        $tempPath = storage_path('app/livewire-tmp');

        if (!is_dir($tempPath)) {
            return 0;
        }

        $deleted = 0;
        $cutoffTime = time() - ($olderThanHours * 3600);

        $files = glob($tempPath . '/*');
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }
}