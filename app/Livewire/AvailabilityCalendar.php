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

    // Vacation mode
    public $showVacationModal = false;
    public $vacationFromDate = '';
    public $vacationToDate = '';
    public $vacationNotes = '';
    public $vacationFromTime = '00:00';
    public $vacationToTime = '23:59';
    public $vacationAllDay = true;
    public $vacationStartTime = '00:00';
    public $vacationEndTime = '23:59';

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
    public $recurring_max_weeks = 4; // Basic: 4 weeks, Premium: 8 weeks, Pro: 52 weeks
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
            ->where('available_date', $this->date)
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
            'available_date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_available' => $this->is_available,
            'time_slot' => $this->time_slot,
            'available_services' => $this->selected_services,
            'notes' => $this->notes,
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
        $maxWeeks = $this->getRecurringMaxWeeks();

        // Determine end date - priority: recurring_end_date > recurring_weeks
        if ($this->recurring_end_date) {
            $endDate = Carbon::parse($this->recurring_end_date);

            // Validate that end date doesn't exceed subscription limits
            $maxEndDate = $startDate->copy()->addWeeks($maxWeeks);
            if ($endDate->gt($maxEndDate)) {
                session()->flash('error', 'Data końcowa przekracza limity Twojego planu subskrypcji.');
                return;
            }
        } else {
            $weeks = min($this->recurring_weeks ?: 1, $maxWeeks);
            $endDate = $startDate->copy()->addWeeks($weeks);
        }

        $current = $startDate->copy()->addDay(); // Start from next day to avoid duplicate
        while ($current->lte($endDate)) {
            if (in_array($current->dayOfWeek, $this->recurring_days)) {
                Availability::create([
                    'sitter_id' => Auth::id(),
                    'available_date' => $current->format('Y-m-d'),
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
            ->whereYear('available_date', $this->currentYear)
            ->whereMonth('available_date', $this->currentMonth)
            ->orderBy('available_date')
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
            ->where('available_date', $this->date)
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
            ->where('available_date', $this->date)
            ->with('service')
            ->orderBy('time_slot')
            ->orderBy('start_time')
            ->get()
            ->map(function($slot) {
                $slotArray = $slot->toArray();
                $slotArray['service_type_label'] = $slot->service_type_label;
                $slotArray['time_slot_label'] = $slot->time_slot_label;

                // Upewnij się, że service_type zawsze istnieje
                if (!isset($slotArray['service_type'])) {
                    $slotArray['service_type'] = null;
                }

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

    // Vacation mode methods
    public function openVacationModal()
    {
        $this->showVacationModal = true;
        $this->vacationFromDate = now()->format('Y-m-d');
        $this->vacationToDate = now()->addWeek()->format('Y-m-d');
        $this->vacationFromTime = '00:00';
        $this->vacationToTime = '23:59';
        $this->vacationAllDay = true;
        $this->vacationStartTime = '00:00';
        $this->vacationEndTime = '23:59';
        $this->vacationNotes = '';
    }

    public function closeVacationModal()
    {
        $this->showVacationModal = false;
        $this->vacationFromDate = '';
        $this->vacationToDate = '';
        $this->vacationFromTime = '00:00';
        $this->vacationToTime = '23:59';
        $this->vacationAllDay = true;
        $this->vacationStartTime = '00:00';
        $this->vacationEndTime = '23:59';
        $this->vacationNotes = '';
    }

    public function saveVacation()
    {
        $validationRules = [
            'vacationFromDate' => 'required|date|after_or_equal:today',
            'vacationToDate' => 'required|date|after_or_equal:vacationFromDate',
            'vacationNotes' => 'nullable|string|max:500'
        ];

        $validationMessages = [
            'vacationFromDate.required' => 'Data rozpoczęcia urlopu jest wymagana.',
            'vacationFromDate.after_or_equal' => 'Urlop nie może rozpoczynać się w przeszłości.',
            'vacationToDate.required' => 'Data zakończenia urlopu jest wymagana.',
            'vacationToDate.after_or_equal' => 'Data zakończenia musi być równa lub późniejsza niż rozpoczęcia.',
            'vacationNotes.max' => 'Notatka może mieć maksymalnie 500 znaków.'
        ];

        // Dodaj walidację czasu tylko jeśli nie jest "cały dzień"
        if (!$this->vacationAllDay) {
            $validationRules['vacationStartTime'] = 'required';
            $validationRules['vacationEndTime'] = 'required';
            $validationMessages['vacationStartTime.required'] = 'Godzina rozpoczęcia jest wymagana.';
            $validationMessages['vacationEndTime.required'] = 'Godzina zakończenia jest wymagana.';
        }

        $this->validate($validationRules, $validationMessages);

        // Określ godziny na podstawie ustawienia "cały dzień"
        $startTime = $this->vacationAllDay ? '00:00' : $this->vacationStartTime;
        $endTime = $this->vacationAllDay ? '23:59' : $this->vacationEndTime;

        // Utwórz wpisy urlopowe dla wszystkich dni w przedziale
        $currentDate = \Carbon\Carbon::parse($this->vacationFromDate);
        $endDate = \Carbon\Carbon::parse($this->vacationToDate);

        $createdCount = 0;
        $updatedCount = 0;

        while ($currentDate->lte($endDate)) {
            $availability = Availability::where('sitter_id', Auth::id())
                ->where('available_date', $currentDate->format('Y-m-d'))
                ->first();

            if ($availability) {
                // Aktualizuj istniejący wpis
                $availability->update([
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_available' => false,
                    'time_slot' => 'vacation',
                    'available_services' => [],
                    'notes' => 'URLOP: ' . $this->vacationNotes,
                    'vacation_end_date' => $this->vacationToDate,
                ]);
                $updatedCount++;
            } else {
                // Utwórz nowy wpis
                Availability::create([
                    'sitter_id' => Auth::id(),
                    'available_date' => $currentDate->format('Y-m-d'),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_available' => false,
                    'time_slot' => 'vacation',
                    'available_services' => [],
                    'notes' => 'URLOP: ' . $this->vacationNotes,
                    'vacation_end_date' => $this->vacationToDate,
                ]);
                $createdCount++;
            }

            $currentDate->addDay();
        }

        $totalDays = $createdCount + $updatedCount;
        $message = "Urlop został zapisany pomyślnie! ";
        $message .= "Przetworzono {$totalDays} dni ";
        if ($createdCount > 0 && $updatedCount > 0) {
            $message .= "({$createdCount} nowych, {$updatedCount} aktualizacji).";
        } elseif ($createdCount > 0) {
            $message .= "({$createdCount} nowych dni).";
        } else {
            $message .= "({$updatedCount} aktualizacji istniejących dni).";
        }

        session()->flash('success', $message);
        $this->closeVacationModal();
        $this->dispatch('availability-updated');
    }

    public function getRecurringMaxWeeks()
    {
        $user = Auth::user();

        // Pro account - 1 year
        if ($user->hasActiveSubscription('pro')) {
            return 52;
        }

        // Premium account - 2 months
        if ($user->hasActiveSubscription('premium')) {
            return 8;
        }

        // Basic account - 1 month
        return 4;
    }

    public function isOnVacation()
    {
        $now = now();

        return Availability::where('sitter_id', Auth::id())
            ->where('time_slot', 'vacation')
            ->where('available_date', '<=', $now->format('Y-m-d'))
            ->where(function($query) use ($now) {
                $query->whereNull('vacation_end_date')
                    ->orWhere('vacation_end_date', '>=', $now->format('Y-m-d'));
            })
            ->exists();
    }

    public function getCurrentVacation()
    {
        $now = now();

        return Availability::where('sitter_id', Auth::id())
            ->where('time_slot', 'vacation')
            ->where('available_date', '<=', $now->format('Y-m-d'))
            ->where(function($query) use ($now) {
                $query->whereNull('vacation_end_date')
                    ->orWhere('vacation_end_date', '>=', $now->format('Y-m-d'));
            })
            ->first();
    }

    public function render()
    {
        $this->recurring_max_weeks = $this->getRecurringMaxWeeks();

        return view('livewire.availability-calendar');
    }
}
