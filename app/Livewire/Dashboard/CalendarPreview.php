<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Carbon\Carbon;

class CalendarPreview extends Component
{
    public $currentWeek;
    public $weekDays = [];

    public function mount()
    {
        $this->currentWeek = Carbon::now()->startOfWeek();
        $this->generateWeekDays();
    }

    public function generateWeekDays()
    {
        $this->weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $this->currentWeek->copy()->addDays($i);
            $this->weekDays[] = [
                'date' => $date,
                'dayName' => $date->translatedFormat('l'),
                'dayShort' => $date->translatedFormat('D'),
                'dayNumber' => $date->day,
                'isToday' => $date->isToday(),
                'events' => $this->getEventsForDate($date)
            ];
        }
    }

    public function getEventsForDate($date)
    {
        $user = auth()->user();

        // Get bookings for this date
        $bookings = $user->sitterBookings()
            ->whereDate('start_date', $date)
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->with(['service', 'owner', 'pet'])
            ->orderBy('start_date')
            ->get();

        return $bookings->map(function($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->service->title,
                'time' => $booking->start_date->format('H:i') . ' - ' . $booking->end_date->format('H:i'),
                'client' => $booking->owner->name,
                'pet' => $booking->pet->name,
                'status' => $booking->status,
                'type' => 'booking',
                'icon' => '📅'
            ];
        })->toArray();
    }

    public function nextWeek()
    {
        $this->currentWeek = $this->currentWeek->copy()->addWeek();
        $this->generateWeekDays();
    }

    public function previousWeek()
    {
        $this->currentWeek = $this->currentWeek->copy()->subWeek();
        $this->generateWeekDays();
    }

    public function goToToday()
    {
        $this->currentWeek = Carbon::now()->startOfWeek();
        $this->generateWeekDays();
    }

    public function render()
    {
        return view('livewire.dashboard.calendar-preview');
    }
}