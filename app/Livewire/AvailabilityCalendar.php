<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AvailabilityCalendar extends Component
{
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

    protected $rules = [
        'date' => 'required|date|after_or_equal:today',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'is_available' => 'boolean',
        'notes' => 'nullable|string|max:500',
        'is_recurring' => 'boolean',
        'recurring_days' => 'array',
    ];

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
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

        // Check if there's existing availability for this date
        $existing = Availability::where('sitter_id', Auth::id())
            ->where('date', $date)
            ->first();

        if ($existing) {
            $this->editingAvailability = $existing->id;
            $this->start_time = $existing->start_time->format('H:i');
            $this->end_time = $existing->end_time->format('H:i');
            $this->is_available = $existing->is_available;
            $this->notes = $existing->notes;
        }
    }

    public function saveAvailability()
    {
        $this->validate();

        $data = [
            'sitter_id' => Auth::id(),
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_available' => $this->is_available,
            'notes' => $this->notes,
        ];

        if ($this->editingAvailability) {
            $availability = Availability::find($this->editingAvailability);
            $availability->update($data);
        } else {
            Availability::create($data);
        }

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
        $endDate = $startDate->copy()->addWeeks(8); // Create for next 8 weeks

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            if (in_array($current->dayOfWeek, $this->recurring_days)) {
                Availability::updateOrCreate(
                    [
                        'sitter_id' => Auth::id(),
                        'date' => $current->format('Y-m-d'),
                    ],
                    [
                        'start_time' => $this->start_time,
                        'end_time' => $this->end_time,
                        'is_available' => $this->is_available,
                        'notes' => $this->notes,
                    ]
                );
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

    private function resetForm()
    {
        $this->editingAvailability = null;
        $this->start_time = '09:00';
        $this->end_time = '17:00';
        $this->is_available = true;
        $this->notes = '';
        $this->is_recurring = false;
        $this->recurring_days = [];
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
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });
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

    public function render()
    {
        return view('livewire.availability-calendar');
    }
}
