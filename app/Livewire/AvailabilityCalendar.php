<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Availability;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AvailabilityCalendar extends Component
{
    // Ograniczenia slotów na dzień
    const MAX_SLOTS_BASIC = 3;
    const MAX_SLOTS_PREMIUM = 6;

    public $currentMonth;
    public $currentYear;
    public $selectedDate = null;
    public $showModal = false;
    public $editingAvailability = null;

    // Form fields
    public $date = '';
    public $start_time = '09:00';
    public $end_time = '17:00';
    public $is_available = true;
    public $notes = '';
    public $is_recurring = false;
    public $recurring_days = [];
    public $recurring_end_date = null;
    public $recurring_weeks = 1;
    public $time_slot = 'custom';
    /**
     * Tablica ID wybranych usług dostępnych w slocie.
     *
     * @var array
     */
    public $selected_services = [];
    public $availability_slots = []; // Przechowuje wszystkie sloty dla danego dnia

    protected $rules = [
        'date' => 'required|date|after_or_equal:today',
        'start_time' => 'required',
        'end_time' => 'required',
        'is_available' => 'boolean',
        'notes' => 'nullable|string|max:500',
        'is_recurring' => 'boolean',
        'recurring_days' => 'array',
        'recurring_end_date' => 'nullable|date|after_or_equal:date',
        'recurring_weeks' => 'nullable|integer|min:1|max:52',
        'time_slot' => 'required|string',
        'selected_services' => 'array',
    ];

    protected $messages = [
        'date.after_or_equal' => 'Data musi być dzisiejsza lub przyszła.',
        'start_time.required' => 'Godzina rozpoczęcia jest wymagana.',
        'end_time.required' => 'Godzina zakończenia jest wymagana.',
        'end_time.after' => 'Godzina zakończenia musi być później niż rozpoczęcia.',
        'time_slot.required' => 'Slot czasowy jest wymagany.',
    ];

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    private function getMaxSlots(): int
    {
        return Auth::user()->getMaxAvailabilitySlots();
    }

    private function canAddSlot(): bool
    {
        $currentSlotsCount = count($this->availability_slots);
        return $currentSlotsCount < $this->getMaxSlots();
    }

    public function previousMonth()
    {
        if ($this->currentMonth == 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
    }

    public function nextMonth()
    {
        if ($this->currentMonth == 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->date = $date;
        $this->showModal = true;
        $this->resetForm();

        // Load existing availability slots for this date
        $this->refreshAvailabilitySlots();
    }

    public function saveAvailability()
    {
        $this->validate();

        // Custom walidacja dla przedziałów czasowych (obsługa slotów nocnych)
        if (!$this->validateTimeRange()) {
            return;
        }

        // Sprawdź czy nie ma już slotu z tym samym czasem rozpoczęcia
        $query = Availability::where('sitter_id', Auth::id())
            ->where('date', $this->date)
            ->where('start_time', $this->start_time);

        // Jeśli edytujemy, wykluczmy obecny slot z sprawdzenia
        if ($this->editingAvailability) {
            $query->where('id', '!=', $this->editingAvailability);
        }

        if ($query->exists()) {
            $this->addError('start_time', 'Masz już slot o tej godzinie rozpoczęcia. Wybierz inną godzinę.');
            return;
        }

        // Sprawdź nakładanie się czasów z innymi slotami (z obsługą slotów nocnych)
        if (!$this->checkTimeOverlap()) {
            return;
        }

        // Sprawdź limit slotów dla nowych slotów
        if (!$this->editingAvailability && !$this->canAddSlot()) {
            $maxSlots = $this->getMaxSlots();
            $user = Auth::user();

            if ($user->isPremium()) {
                session()->flash('error', 'Możesz mieć maksymalnie ' . $maxSlots . ' slotów na dzień.');
            } else {
                session()->flash('error', 'Osiągnąłeś limit ' . $maxSlots . ' slotów na dzień. Przejdź na wersję Premium dla ' . self::MAX_SLOTS_PREMIUM . ' slotów!');
            }
            return;
        }

        $data = [
            'sitter_id' => Auth::id(),
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_available' => $this->is_available,
            'time_slot' => $this->time_slot,
            'available_services' => $this->selected_services,
            'notes' => $this->notes,
            'is_recurring' => $this->is_recurring,
            'recurring_days' => $this->recurring_days,
            'recurring_end_date' => $this->recurring_end_date,
            'recurring_weeks' => $this->recurring_weeks,
        ];

        if ($this->editingAvailability) {
            $availability = Availability::find($this->editingAvailability);
            $availability->update($data);
        } else {
            Availability::create($data);
        }

        // Zawsze odśwież listę slotów po zapisie (edycji lub dodaniu)
        $this->refreshAvailabilitySlots();

        // Handle recurring availability
        if ($this->is_recurring && !empty($this->recurring_days)) {
            $this->createRecurringAvailability();
        }

        $this->closeModal();
        $this->dispatch('availability-saved');
    }

    public function createRecurringAvailability()
    {
        $startDate = Carbon::parse($this->date);

        // Determine end date - priority: recurring_end_date > recurring_weeks
        if ($this->recurring_end_date) {
            $endDate = Carbon::parse($this->recurring_end_date);
        } else {
            $weeks = $this->recurring_weeks ?: 1;
            $endDate = $startDate->copy()->addWeeks($weeks);
        }

        $current = $startDate->copy()->addDay(); // Start from next day to avoid duplicate
        while ($current->lte($endDate)) {
            if (in_array($current->dayOfWeek, $this->recurring_days)) {
                Availability::create([
                    'sitter_id' => Auth::id(),
                    'date' => $current->format('Y-m-d'),
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'is_available' => $this->is_available,
                            'time_slot' => $this->time_slot,
                    'available_services' => $this->selected_services,
                    'notes' => $this->notes,
                ]);
            }
            $current->addDay();
        }
    }

    public function deleteAvailability($availabilityId)
    {
        $availability = Availability::where('sitter_id', Auth::id())
            ->find($availabilityId);

        if ($availability) {
            $availability->delete();
            $this->dispatch('availability-deleted');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function editSlot($slotId)
    {
        $availability = Availability::find($slotId);
        if ($availability && $availability->sitter_id === Auth::id()) {
            $this->editingAvailability = $availability->id;
            $this->time_slot = $availability->time_slot;
            $this->start_time = $availability->start_time->format('H:i');
            $this->end_time = $availability->end_time->format('H:i');
            $this->is_available = $availability->is_available;
            $this->selected_services = $availability->available_services ?? [];
            $this->notes = $availability->notes;
            $this->is_recurring = $availability->is_recurring ?? false;
            $this->recurring_days = $availability->recurring_days ?? [];
            $this->recurring_end_date = $availability->recurring_end_date ? $availability->recurring_end_date->format('Y-m-d') : null;
            $this->recurring_weeks = $availability->recurring_weeks ?? 1;
        }
    }

    public function confirmDeleteSlot($slotId, $date, $time)
    {
        $this->dispatch('show-confirmation',
            'Usuń slot dostępności',
            'Czy na pewno chcesz usunąć slot z dnia ' . $date . ' o godzinie ' . $time . '? Ta operacja jest nieodwracalna.',
            'delete-slot-confirmed',
            [$slotId],
            'Usuń',
            'Anuluj',
            'bg-red-600 hover:bg-red-700'
        );
    }

    #[On('delete-slot-confirmed')]
    public function deleteSlot($slotId)
    {
        $availability = Availability::where('sitter_id', Auth::id())->find($slotId);
        if ($availability) {
            $availability->delete();
            $this->dispatch('availability-deleted');
            $this->dispatch('show-success-alert', [
                'message' => 'Slot dostępności został pomyślnie usunięty.'
            ]);
            // Refresh slots
            $this->refreshAvailabilitySlots();
        }
    }

    public function addNewSlot()
    {
        $this->resetForm();
        // Auto-set time based on existing slots
        $this->autoSetTimeForNewSlot();
    }


    /**
     * Zwraca etykietę typu usługi na podstawie konfiguracji usługi.
     *
     * @param \App\Models\Service $service Obiekt usługi
     * @return string Etykieta typu usługi
     */
    private function getServiceTypeLabel($service)
    {
        $types = [];
        if ($service->home_service) $types[] = 'U klienta';
        if ($service->sitter_home) $types[] = 'U opiekuna';

        return empty($types) ? 'Uniwersalna' : implode(', ', $types);
    }

    private function autoSetTimeForNewSlot()
    {
        $existingSlots = collect($this->availability_slots);

        if ($existingSlots->isEmpty()) {
            $this->time_slot = 'custom';
            $this->start_time = '09:00';
            $this->end_time = '17:00';
        } else {
            // Znajdź ostatni slot i zasugeruj następny przedział
            $lastSlot = $existingSlots->sortBy('start_time')->last();
            if ($lastSlot) {
                $lastEndTime = \Carbon\Carbon::parse($lastSlot['end_time']);
                $suggestedStart = $lastEndTime->format('H:i');
                $suggestedEnd = $lastEndTime->addHours(2)->format('H:i');

                $this->time_slot = 'custom';
                $this->start_time = $suggestedStart;
                $this->end_time = $suggestedEnd;
            } else {
                $this->time_slot = 'custom';
                $this->start_time = '09:00';
                $this->end_time = '17:00';
            }
        }
    }

    public function updatedTimeSlot($value)
    {
        // Auto-suggest times based on selected slot
        switch ($value) {
            case 'morning':
                $this->start_time = '08:00';
                $this->end_time = '12:00';
                break;
            case 'afternoon':
                $this->start_time = '12:00';
                $this->end_time = '18:00';
                break;
            case 'evening':
                $this->start_time = '18:00';
                $this->end_time = '22:00';
                break;
            case 'overnight':
                $this->start_time = '22:00';
                $this->end_time = '08:00';
                break;
            case 'all_day':
                $this->start_time = '08:00';
                $this->end_time = '20:00';
                break;
            case 'custom':
            default:
                // Keep current times or use defaults
                if (empty($this->start_time)) {
                    $this->start_time = '09:00';
                }
                if (empty($this->end_time)) {
                    $this->end_time = '17:00';
                }
                break;
        }
    }

    private function resetForm()
    {
        $this->editingAvailability = null;
        $this->start_time = '09:00';
        $this->end_time = '17:00';
        $this->is_available = true;
        $this->time_slot = 'custom';
        $this->selected_services = [];
        $this->notes = '';
        $this->is_recurring = false;
        $this->recurring_days = [];
        $this->recurring_end_date = null;
        $this->recurring_weeks = 1;
    }

    public function getCurrentMonthDaysProperty()
    {
        $firstDay = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

        $days = [];
        $current = $firstDay->copy();

        while ($current->lte($lastDay)) {
            $days[] = $current->copy();
            $current->addDay();
        }

        return $days;
    }

    public function getAvailabilityForMonthProperty()
    {
        return Availability::where('sitter_id', Auth::id())
            ->whereYear('date', $this->currentYear)
            ->whereMonth('date', $this->currentMonth)
            ->orderBy('date')
            ->orderBy('time_slot')
            ->orderBy('start_time')
            ->get();
    }

    public function getMonthNameProperty()
    {
        $months = [
            1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
            5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
            9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
        ];

        return $months[$this->currentMonth];
    }

    public function getUserServicesProperty()
    {
        return Service::where('sitter_id', Auth::id())
            ->where('is_active', true)
            ->with('category')
            ->get();
    }


    public function getTimeSlotOptionsProperty()
    {
        return [
            'custom' => 'Własny przedział czasowy',
            'morning' => 'Rano',
            'afternoon' => 'Popołudnie',
            'evening' => 'Wieczorem',
            'overnight' => 'Nocleg',
            'all_day' => 'Cały dzień',
        ];
    }


    private function validateTimeRange(): bool
    {
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);

        // Dla slotów nocnych (np. 22:00 - 08:00) - godzina końcowa może być wcześniejsza
        if ($this->time_slot === 'overnight') {
            // Slot nocny - end_time może być mniejszy od start_time (przechodzi na następny dzień)
            if ($startTime->hour >= 20 && $endTime->hour <= 10) {
                return true; // Poprawny slot nocny
            }
        }

        // Dla wszystkich innych slotów - normalna walidacja
        if ($endTime->lte($startTime)) {
            $this->addError('end_time', 'Godzina zakończenia musi być później niż rozpoczęcia (chyba że to slot nocny).');
            return false;
        }

        // Sprawdź minimalną długość slotu (15 minut)
        if ($this->time_slot !== 'overnight' && $startTime->diffInMinutes($endTime) < 15) {
            $this->addError('end_time', 'Slot musi trwać co najmniej 15 minut.');
            return false;
        }

        return true;
    }

    private function checkTimeOverlap(): bool
    {
        $existingSlots = Availability::where('sitter_id', Auth::id())
            ->where('date', $this->date)
            ->when($this->editingAvailability, function($query) {
                $query->where('id', '!=', $this->editingAvailability);
            })
            ->get(['start_time', 'end_time', 'time_slot']);

        $newStartTime = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
        $newEndTime = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);
        $isNewSlotOvernight = $this->time_slot === 'overnight';

        foreach ($existingSlots as $existingSlot) {
            $existingStartTime = \Carbon\Carbon::createFromFormat('H:i', $existingSlot->start_time->format('H:i'));
            $existingEndTime = \Carbon\Carbon::createFromFormat('H:i', $existingSlot->end_time->format('H:i'));
            $isExistingOvernight = $existingSlot->time_slot === 'overnight';

            // Sprawdź kolizje dla slotów nocnych i zwykłych
            if ($this->timesOverlap($newStartTime, $newEndTime, $isNewSlotOvernight,
                                   $existingStartTime, $existingEndTime, $isExistingOvernight)) {

                $this->addError('start_time', 'Ten przedział czasowy nakłada się z istniejącym slotem (' .
                    $existingSlot->start_time->format('H:i') . ' - ' .
                    $existingSlot->end_time->format('H:i') . '). Wybierz inny czas.');
                return false;
            }
        }

        return true;
    }

    private function timesOverlap($start1, $end1, $isOvernight1, $start2, $end2, $isOvernight2): bool
    {
        // Dla slotów nocnych, konwertujemy czas na minuty od północy z obsługą przejścia przez dzień
        $getMinutes = function($time, $isOvernight, $isEndTime = false) {
            $minutes = $time->hour * 60 + $time->minute;

            // Jeśli to slot nocny i godzina końcowa jest mniejsza od startu, dodaj 24h (następny dzień)
            if ($isOvernight && $isEndTime && $time->hour <= 10) {
                $minutes += 24 * 60;
            }

            return $minutes;
        };

        $start1Min = $getMinutes($start1, $isOvernight1);
        $end1Min = $getMinutes($end1, $isOvernight1, true);
        $start2Min = $getMinutes($start2, $isOvernight2);
        $end2Min = $getMinutes($end2, $isOvernight2, true);

        // Sprawdź nakładanie się przedziałów
        return !($end1Min <= $start2Min || $start1Min >= $end2Min);
    }

    private function refreshAvailabilitySlots()
    {
        $this->availability_slots = Availability::where('sitter_id', Auth::id())
            ->where('date', $this->date)
            ->with('service')
            ->orderBy('time_slot')
            ->orderBy('start_time')
            ->get()
            ->map(function($slot) {
                $slotArray = $slot->toArray();
                $slotArray['service_type_label'] = $slot->service_type_label;
                $slotArray['time_slot_label'] = $slot->time_slot_label;

                // Convert service data to display format with custom type labels
                if ($slot->available_services) {
                    $serviceData = is_array($slot->available_services) ? $slot->available_services : json_decode($slot->available_services, true);
                    $serviceInfo = [];

                    if ($serviceData) {
                        foreach ($serviceData as $key => $value) {
                            $serviceId = null;
                            $serviceType = null;

                            // Handle both old and new data structure
                            if (is_numeric($key) && is_numeric($value)) {
                                // Old structure: [1, 2, 3] - just service IDs
                                $serviceId = $value;
                                $serviceType = 'default';
                            } elseif (is_array($value) && isset($value['service_id'])) {
                                // New structure: ['service_id' => id, 'service_type' => 'type']
                                $serviceId = $value['service_id'];
                                $serviceType = $value['service_type'];
                            }

                            if ($serviceId) {
                                $service = \App\Models\Service::with('category')->find($serviceId);
                                if ($service) {
                                    $typeLabel = match($serviceType) {
                                        'home_service' => 'U klienta',
                                        'sitter_home' => 'U opiekuna',
                                        'universal' => 'Uniwersalna',
                                        default => $this->getServiceTypeLabel($service)
                                    };

                                    $serviceInfo[] = [
                                        'title' => $service->title,
                                        'type_label' => $typeLabel,
                                        'category' => $service->category?->name ?? 'Inna kategoria'
                                    ];
                                }
                            }
                        }
                    }
                    $slotArray['available_services'] = $serviceInfo;
                }

                return $slotArray;
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.availability-calendar');
    }
}
