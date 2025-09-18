<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use App\Models\Pet;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

class BookingForm extends Component
{
    public Service $service;

    #[Validate('required')]
    public $pet_id = '';

    #[Validate('required|date|after:today')]
    public $start_date = '';

    #[Validate('required')]
    public $start_time = '';

    #[Validate('required|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('required')]
    public $end_time = '';

    public $special_instructions = '';

    public $estimated_price = 0;
    public $pricing_type = 'hour'; // hour or day

    public function mount(Service $service)
    {
        $this->service = $service;

        // Set default dates
        $this->start_date = now()->addDay()->format('Y-m-d');
        $this->end_date = now()->addDay()->format('Y-m-d');
        $this->start_time = '09:00';
        $this->end_time = '17:00';

        // Determine pricing type based on service
        if ($this->service->price_per_day && $this->service->price_per_hour) {
            $this->pricing_type = 'hour'; // default to hourly
        } elseif ($this->service->price_per_day) {
            $this->pricing_type = 'day';
        } else {
            $this->pricing_type = 'hour';
        }

        $this->calculatePrice();
    }

    public function updatedStartDate()
    {
        // Ensure end date is not before start date
        if ($this->end_date < $this->start_date) {
            $this->end_date = $this->start_date;
        }
        $this->calculatePrice();
    }

    public function updatedEndDate()
    {
        $this->calculatePrice();
    }

    public function updatedStartTime()
    {
        $this->calculatePrice();
    }

    public function updatedEndTime()
    {
        $this->calculatePrice();
    }

    public function updatedPricingType()
    {
        $this->calculatePrice();
    }

    public function calculatePrice()
    {
        if (!$this->start_date || !$this->end_date || !$this->start_time || !$this->end_time) {
            $this->estimated_price = 0;
            return;
        }

        $startDateTime = \Carbon\Carbon::parse($this->start_date . ' ' . $this->start_time);
        $endDateTime = \Carbon\Carbon::parse($this->end_date . ' ' . $this->end_time);

        if ($this->pricing_type === 'hour' && $this->service->price_per_hour) {
            $hours = $startDateTime->diffInHours($endDateTime);
            $this->estimated_price = $hours * $this->service->price_per_hour;
        } elseif ($this->pricing_type === 'day' && $this->service->price_per_day) {
            $days = $startDateTime->diffInDays($endDateTime) ?: 1; // minimum 1 day
            $this->estimated_price = $days * $this->service->price_per_day;
        }
    }

    public function submit()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Musisz być zalogowany, aby dokonać rezerwacji.');
            return redirect()->route('login');
        }

        $this->validate();

        // Check if user has pets
        if (Auth::user()->pets()->count() === 0) {
            session()->flash('error', 'Musisz najpierw dodać zwierzę do swojego profilu.');
            return;
        }

        // Validate pet belongs to user
        $pet = Pet::where('id', $this->pet_id)
                   ->where('owner_id', Auth::id())
                   ->first();

        if (!$pet) {
            session()->flash('error', 'Wybrane zwierzę nie należy do Ciebie.');
            return;
        }

        $startDateTime = \Carbon\Carbon::parse($this->start_date . ' ' . $this->start_time);
        $endDateTime = \Carbon\Carbon::parse($this->end_date . ' ' . $this->end_time);

        // Check for booking conflicts
        $conflict = Booking::where('sitter_id', $this->service->sitter_id)
                          ->whereIn('status', ['confirmed', 'in_progress'])
                          ->where(function($query) use ($startDateTime, $endDateTime) {
                              $query->whereBetween('start_date', [$startDateTime, $endDateTime])
                                    ->orWhereBetween('end_date', [$startDateTime, $endDateTime])
                                    ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                                        $q->where('start_date', '<=', $startDateTime)
                                          ->where('end_date', '>=', $endDateTime);
                                    });
                          })
                          ->exists();

        if ($conflict) {
            session()->flash('error', 'Opiekun jest już zajęty w wybranym terminie.');
            return;
        }

        // Create booking
        $booking = Booking::create([
            'owner_id' => Auth::id(),
            'sitter_id' => $this->service->sitter_id,
            'service_id' => $this->service->id,
            'pet_id' => $this->pet_id,
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
            'total_price' => $this->estimated_price,
            'special_instructions' => $this->special_instructions,
            'status' => 'pending',
        ]);

        // Wyślij powiadomienie opiekunowi
        $notificationService = app(\App\Services\NotificationService::class);
        $notificationService->notifyBookingCreated($booking);

        session()->flash('success', 'Rezerwacja została utworzona! Oczekuje na potwierdzenie przez opiekuna.');

        // Redirect to chat with booking context
        return redirect()->route('chat', [
            'user' => $this->service->sitter->id,
            'booking' => $booking->id
        ]);
    }

    public function createAndPay()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate();

        // Check if user has pets
        if (Auth::user()->pets()->count() === 0) {
            session()->flash('error', 'Musisz najpierw dodać zwierzę do swojego profilu.');
            return;
        }

        // Validate pet belongs to user
        $pet = Pet::where('id', $this->pet_id)
                   ->where('owner_id', Auth::id())
                   ->first();

        if (!$pet) {
            session()->flash('error', 'Wybrane zwierzę nie należy do Ciebie.');
            return;
        }

        $startDateTime = \Carbon\Carbon::parse($this->start_date . ' ' . $this->start_time);
        $endDateTime = \Carbon\Carbon::parse($this->end_date . ' ' . $this->end_time);

        // Check for booking conflicts
        $conflict = Booking::where('sitter_id', $this->service->sitter_id)
                          ->whereIn('status', ['confirmed', 'in_progress'])
                          ->where(function($query) use ($startDateTime, $endDateTime) {
                              $query->whereBetween('start_date', [$startDateTime, $endDateTime])
                                    ->orWhereBetween('end_date', [$startDateTime, $endDateTime])
                                    ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                                        $q->where('start_date', '<=', $startDateTime)
                                          ->where('end_date', '>=', $endDateTime);
                                    });
                          })
                          ->exists();

        if ($conflict) {
            session()->flash('error', 'Opiekun jest już zajęty w wybranym terminie.');
            return;
        }

        // Create booking
        $booking = Booking::create([
            'owner_id' => Auth::id(),
            'sitter_id' => $this->service->sitter_id,
            'service_id' => $this->service->id,
            'pet_id' => $this->pet_id,
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
            'total_price' => $this->estimated_price,
            'special_instructions' => $this->special_instructions,
            'status' => 'pending',
        ]);

        // Redirect to payment
        return redirect()->route('payment.process', $booking);
    }

    public function getUserPetsProperty()
    {
        if (!Auth::check()) {
            return collect();
        }

        return Auth::user()->pets()->active()->get();
    }

    public function render()
    {
        return view('livewire.booking-form');
    }
}
