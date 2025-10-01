<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model przechowujący draft'y wizard'ów użytkowników.
 *
 * Pozwala użytkownikom na zapisywanie postępu w wizard'ach
 * i kontynuowanie rejestracji w późniejszym czasie.
 *
 * @package App\Models
 */
class WizardDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wizard_type',
        'current_step',
        'wizard_data',
        'last_accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'wizard_data' => 'array',
            'last_accessed_at' => 'datetime',
        ];
    }

    /**
     * Relacja z użytkownikiem.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sprawdza czy draft jest świeży (ostatnio używany).
     */
    public function isRecent(): bool
    {
        return $this->last_accessed_at->isAfter(now()->subDays(7));
    }

    /**
     * Aktualizuje czas ostatniego dostępu.
     */
    public function touch($attribute = null): bool
    {
        $this->last_accessed_at = now();
        return parent::touch($attribute);
    }
}
