<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

/**
 * Model reprezentujący zdjęcie w aplikacji PetHelp.
 *
 * Uniwersalny model do przechowywania zdjęć powiązanych z użytkownikami i zwierzętami.
 * Obsługuje albumy, tagi, publiczność zdjęć i zarządzanie plikami w storage.
 *
 * @package App\Models
 * @author Claude AI Assistant
 * @since 1.0.0
 *
 * @property int $id Unikalny identyfikator zdjęcia
 * @property int $user_id ID właściciela zdjęcia
 * @property int|null $pet_id ID powiązanego zwierzęcia (opcjonalne)
 * @property string|null $title Tytuł zdjęcia
 * @property string|null $description Opis zdjęcia
 * @property string $file_path Ścieżka do pliku w storage
 * @property string $file_name Nazwa pliku
 * @property string $mime_type Typ MIME pliku
 * @property int $file_size Rozmiar pliku w bajtach
 * @property int|null $width Szerokość obrazu w pikselach
 * @property int|null $height Wysokość obrazu w pikselach
 * @property string|null $album Nazwa albumu
 * @property bool $is_public Czy zdjęcie jest publiczne
 * @property bool $is_featured Czy zdjęcie jest wyróżnione
 * @property array|null $tags Tagi zdjęcia
 * @property int|null $sort_order Kolejność sortowania
 * @property \Carbon\Carbon $created_at Data przesłania zdjęcia
 * @property \Carbon\Carbon $updated_at Data ostatniej aktualizacji
 *
 * @property-read \App\Models\User $user Właściciel zdjęcia
 * @property-read \App\Models\Pet|null $pet Powiązane zwierzę (jeśli dotyczy)
 * @property-read string $url Publiczny URL do zdjęcia
 * @property-read string $thumbnail_url URL do miniaturki
 * @property-read string $file_size_human Sformatowany rozmiar pliku
 * @property-read string|null $dimensions Wymiary obrazu jako string
 *
 * @method static \App\Models\Photo create(array $attributes = []) Tworzy nowe zdjęcie
 * @method static \Illuminate\Database\Eloquent\Builder forUser(int $userId) Filtruje zdjęcia użytkownika
 * @method static \Illuminate\Database\Eloquent\Builder forPet(int $petId) Filtruje zdjęcia zwierzęcia
 * @method static \Illuminate\Database\Eloquent\Builder inAlbum(string $album) Filtruje po albumie
 * @method static \Illuminate\Database\Eloquent\Builder public() Filtruje publiczne zdjęcia
 * @method static \Illuminate\Database\Eloquent\Builder featured() Filtruje wyróżnione zdjęcia
 * @method static \Illuminate\Database\Eloquent\Builder ordered() Sortuje według kolejności
 */
class Photo extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_public' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected $fillable = [
        'user_id',
        'pet_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'width',
        'height',
        'album',
        'is_public',
        'is_featured',
        'tags',
        'sort_order',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    // Scopes
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForPet(Builder $query, int $petId): Builder
    {
        return $query->where('pet_id', $petId);
    }

    public function scopeInAlbum(Builder $query, string $album): Builder
    {
        return $query->where('album', $album);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getThumbnailUrlAttribute(): string
    {
        // For now, return the same URL. In future, could implement thumbnail generation
        return $this->url;
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDimensionsAttribute(): ?string
    {
        if ($this->width && $this->height) {
            return $this->width . 'x' . $this->height;
        }
        return null;
    }

    // Helper methods
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?? []);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        $this->update(['tags' => array_values($tags)]);
    }

    // Delete file when deleting model
    protected static function booted(): void
    {
        static::deleting(function (Photo $photo) {
            Storage::disk('public')->delete($photo->file_path);
        });
    }
}
