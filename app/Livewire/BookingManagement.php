<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class BookingManagement extends Component
{
    use WithPagination;

    public $view = 'owner'; // 'owner' or 'sitter'
    public $statusFilter = '';
    public $selectedBooking = null;
    public $showModal = false;

    public function mount($view = 'owner')
    {
        $this->view = $view;
    }

    public function changeView($view)
    {
        $this->view = $view;
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function viewBooking($bookingId)
    {
        $this->selectedBooking = Booking::with(['owner', 'sitter', 'service', 'pet', 'payment'])
                                         ->find($bookingId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedBooking = null;
    }

    public function confirmBooking($bookingId)
    {
        $booking = Booking::where('id', $bookingId)
                         ->where('sitter_id', Auth::id())
                         ->where('status', 'pending')
                         ->first();

        if ($booking) {
            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now()
            ]);

            // Wyślij powiadomienie
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyBookingConfirmed($booking);

            session()->flash('success', 'Rezerwacja została potwierdzona!');
        }
    }

    public function cancelBooking($bookingId, $reason = '')
    {
        if ($this->view === 'owner') {
            $booking = Booking::where('id', $bookingId)
                             ->where('owner_id', Auth::id())
                             ->whereIn('status', ['pending', 'confirmed'])
                             ->first();
        } else {
            $booking = Booking::where('id', $bookingId)
                             ->where('sitter_id', Auth::id())
                             ->whereIn('status', ['pending', 'confirmed'])
                             ->first();
        }

        if ($booking && $booking->canBeCancelled()) {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason ?: 'Anulowano'
            ]);

            // Wyślij powiadomienie
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyBookingCancelled($booking, Auth::user());

            session()->flash('success', 'Rezerwacja została anulowana.');
        }
    }

    public function completeBooking($bookingId)
    {
        $booking = Booking::where('id', $bookingId)
                         ->where('sitter_id', Auth::id())
                         ->where('status', 'in_progress')
                         ->first();

        if ($booking && $booking->canBeCompleted()) {
            $booking->update(['status' => 'completed']);

            // Wyślij powiadomienie
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyBookingCompleted($booking);

            session()->flash('success', 'Rezerwacja została zakończona!');
        }
    }

    public function getBookingsProperty()
    {
        $query = Booking::with(['owner', 'sitter', 'service.category', 'pet']);

        if ($this->view === 'owner') {
            $query->forOwner(Auth::id());
        } else {
            $query->forSitter(Auth::id());
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getBookingStatsProperty()
    {
        $baseQuery = $this->view === 'owner'
            ? Booking::forOwner(Auth::id())
            : Booking::forSitter(Auth::id());

        return [
            'pending' => (clone $baseQuery)->pending()->count(),
            'confirmed' => (clone $baseQuery)->confirmed()->count(),
            'completed' => (clone $baseQuery)->completed()->count(),
            'total' => (clone $baseQuery)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.booking-management');
    }
}
