<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model reprezentujący dostępność czasową opiekuna.
 *
 * Zarządza slotami czasowymi kiedy opiekun jest dostępny do świadczenia usług.
 * Obsługuje zarówno jednorazowe terminy jak i powtarzające się wzorce dostępności.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 *
 * @property int $id Unikalny identyfikator dostępności
 * @property int $sitter_id ID użytkownika/opiekuna
 * @property int|null $service_id ID konkretnej usługi (opcjonalne)
 * @property string|null $service_type Typ usługi (home_service, sitter_home, walking, etc.)
 * @property string|null $time_slot Slot czasowy (morning, afternoon, evening, etc.)
 * @property array|null $available_services Dostępne usługi
 * @property \Carbon\Carbon $date Data dostępności
 * @property \Carbon\Carbon $start_time Godzina rozpoczęcia
 * @property \Carbon\Carbon $end_time Godzina zakończenia
 * @property bool $is_available Czy slot jest dostępny
 * @property bool $is_recurring Czy to powtarzający się slot
 * @property array|null $recurring_days Dni tygodnia dla powtarzania
 * @property \Carbon\Carbon|null $recurring_end_date Data zakończenia powtarzania
 * @property int|null $recurring_weeks Liczba tygodni powtarzania
 * @property string|null $notes Dodatkowe notatki
 * @property \Carbon\Carbon $created_at Data utworzenia
 * @property \Carbon\Carbon $updated_at Data ostatniej aktualizacji
 * @property-read \App\Models\User $sitter Użytkownik/opiekun
 * @property-read \App\Models\Service|null $service Powiązana usługa
 * @property-read string $time_range Przedział czasowy (np. "09:00 - 17:00")
 * @property-read string $time_slot_label Polska nazwa slotu czasowego
 * @property-read string $service_type_label Polska nazwa typu usługi
 *
 * @method static \App\Models\Availability create(array $attributes = []) Tworzy nową dostępność
 * @method static \Illuminate\Database\Eloquent\Builder available() Filtruje dostępne sloty
 * @method static \Illuminate\Database\Eloquent\Builder forDate($date) Filtruje dla daty
 * @method static \Illuminate\Database\Eloquent\Builder forSitter($sitterId) Filtruje dla opiekuna
 * @method static \Illuminate\Database\Eloquent\Builder forTimeSlot($timeSlot) Filtruje dla slotu czasowego
 * @method static \Illuminate\Database\Eloquent\Builder forServiceType($serviceType) Filtruje dla typu usługi
 */
class Availability extends Model
{
    use HasFactory;

    protected $table = 'availability';

    protected function casts(): array
    {
        return [
            'available_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'is_available' => 'boolean',
            'is_recurring' => 'boolean',
            'available_services' => 'array',
            'recurring_days' => 'array',
            'recurring_end_date' => 'date',
            'vacation_end_date' => 'date',
        ];
    }

    protected $fillable = [
        'sitter_id',
        'service_id',
        'service_type',
        'time_slot',
        'available_services',
        'available_date',
        'start_time',
        'end_time',
        'is_available',
        'is_recurring',
        'recurring_days',
        'recurring_end_date',
        'recurring_weeks',
        'notes',
        'vacation_end_date',
    ];

    // Map 'date' to 'available_date' column
    public function getDateAttribute()
    {
        return $this->available_date ? Carbon::parse($this->available_date) : null;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['available_date'] = $value;
    }

    /**
     * Relacja do użytkownika/opiekuna.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Availability>
     */
    public function sitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sitter_id');
    }

    /**
     * Relacja do usługi (opcjonalna).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Service, \App\Models\Availability>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('available_date', $date);
    }

    public function scopeForSitter($query, $sitterId)
    {
        return $query->where('sitter_id', $sitterId);
    }

    public function getTimeRangeAttribute(): string
    {
        return $this->start_time->format('H:i').' - '.$this->end_time->format('H:i');
    }

    public function scopeForTimeSlot($query, $timeSlot)
    {
        return $query->where('time_slot', $timeSlot);
    }

    public function scopeForServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function getTimeSlotLabelAttribute(): string
    {
        return match ($this->time_slot) {
            'morning' => 'Rano',
            'afternoon' => 'Popołudnie',
            'evening' => 'Wieczorem',
            'overnight' => 'Nocleg',
            'all_day' => 'Cały dzień',
            'vacation' => 'Urlop',
            'custom' => 'Własny czas',
            default => 'Nieokreślony'
        };
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return match ($this->service_type) {
            'home_service' => 'U klienta',
            'sitter_home' => 'U opiekuna',
            'walking' => 'Spacer',
            'overnight' => 'Opieka nocna',
            'transport' => 'Transport',
            default => 'Wszystkie usługi'
        };
    }
}
