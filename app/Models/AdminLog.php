<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model logów aktywności administratorów.
 *
 * Przechowuje kompletny audit trail wszystkich akcji wykonywanych przez
 * administratorów w panelu Filament. Umożliwia śledzenie zmian, eksport
 * danych i analizę aktywności.
 *
 * @property int $id
 * @property int $admin_id
 * @property string $action
 * @property string|null $model_type
 * @property int|null $model_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $description
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class AdminLog extends Model
{
    /**
     * Pola możliwe do masowego przypisania.
     *
     * @var array<string>
     */
    protected $fillable = [
        'admin_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
    ];

    /**
     * Konfiguracja rzutowania typów dla pól modelu.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relacja do administratora wykonującego akcję.
     *
     * @return BelongsTo<User, AdminLog>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Pobiera nazwę wyświetlaną dla akcji.
     *
     * @return string Polska nazwa akcji
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Utworzono',
            'updated' => 'Zaktualizowano',
            'deleted' => 'Usunięto',
            'viewed' => 'Przeglądano',
            'exported' => 'Wyeksportowano',
            'restored' => 'Przywrócono',
            'force_deleted' => 'Usunięto trwale',
            'attached' => 'Dołączono',
            'detached' => 'Odłączono',
            default => ucfirst($this->action),
        };
    }

    /**
     * Pobiera skróconą nazwę modelu bez namespace.
     *
     * @return string|null Nazwa klasy modelu
     */
    public function getModelNameAttribute(): ?string
    {
        if (! $this->model_type) {
            return null;
        }

        return class_basename($this->model_type);
    }

    /**
     * Loguje akcję administratora.
     *
     * Statyczna metoda pomocnicza do łatwego logowania akcji admina
     * z automatycznym pobieraniem IP i user agent z request.
     *
     * @param  int  $adminId  ID administratora
     * @param  string  $action  Typ akcji
     * @param  string|null  $modelType  Typ modelu
     * @param  int|null  $modelId  ID modelu
     * @param  array|null  $oldValues  Stare wartości
     * @param  array|null  $newValues  Nowe wartości
     * @param  string|null  $description  Opis akcji
     * @return self Utworzony log
     */
    public static function logAction(
        int $adminId,
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): self {
        return self::create([
            'admin_id' => $adminId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
