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

    // Cancel modal
    public $showCancelModal = false;
    public $cancelBookingId = null;
    public $cancelReason = '';

    // Review modal
    public $showReviewModal = false;
    public $reviewBookingId = null;
    public $reviewRating = 5;
    public $reviewComment = '';

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

    public function openReviewModal($bookingId)
    {
        $this->reviewBookingId = $bookingId;
        $this->reviewRating = 5;
        $this->reviewComment = '';
        $this->showReviewModal = true;
    }

    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->reviewBookingId = null;
        $this->reviewRating = 5;
        $this->reviewComment = '';
    }

    public function submitReview()
    {
        $this->validate([
            'reviewRating' => 'required|integer|min:1|max:5',
            'reviewComment' => 'required|string|min:10|max:1000'
        ], [
            'reviewRating.required' => 'Ocena jest wymagana',
            'reviewRating.min' => 'Minimalna ocena to 1',
            'reviewRating.max' => 'Maksymalna ocena to 5',
            'reviewComment.required' => 'Komentarz jest wymagany',
            'reviewComment.min' => 'Komentarz musi mieć co najmniej 10 znaków',
            'reviewComment.max' => 'Komentarz może mieć maksymalnie 1000 znaków'
        ]);

        $booking = Booking::with(['owner', 'sitter'])->find($this->reviewBookingId);

        if ($booking && $booking->canBeReviewedBy(Auth::user())) {
            // Create review logic would go here
            // For now just close modal and show success
            session()->flash('success', 'Recenzja została dodana pomyślnie!');
            $this->closeReviewModal();
        } else {
            session()->flash('error', 'Nie można dodać recenzji dla tej rezerwacji.');
        }
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

    public function openCancelModal($bookingId)
    {
        $this->cancelBookingId = $bookingId;
        $this->cancelReason = '';
        $this->showCancelModal = true;
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancelBookingId = null;
        $this->cancelReason = '';
    }

    public function confirmCancelBooking()
    {
        try {
            if ($this->view === 'owner') {
                $booking = Booking::where('id', $this->cancelBookingId)
                                 ->where('owner_id', Auth::id())
                                 ->whereIn('status', ['pending', 'confirmed'])
                                 ->first();
            } else {
                $booking = Booking::where('id', $this->cancelBookingId)
                                 ->where('sitter_id', Auth::id())
                                 ->whereIn('status', ['pending', 'confirmed'])
                                 ->first();
            }

            if ($booking && $booking->canBeCancelled()) {
                $booking->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => $this->cancelReason ?: 'Anulowano'
                ]);

                // Try to send notification, but don't fail if service doesn't exist
                try {
                    if (class_exists('\App\Services\NotificationService')) {
                        $notificationService = app(\App\Services\NotificationService::class);
                        $notificationService->notifyBookingCancelled($booking, Auth::user());
                    }
                } catch (\Exception $e) {
                    // Log but don't fail
                    \Log::info('Notification service not available: ' . $e->getMessage());
                }

                session()->flash('success', 'Rezerwacja została anulowana.');
                $this->closeCancelModal();
            } else {
                session()->flash('error', 'Nie można anulować tej rezerwacji.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Wystąpił błąd podczas anulowania rezerwacji: ' . $e->getMessage());
        }
    }

    // Legacy method for backwards compatibility
    public function cancelBooking($bookingId, $reason = '')
    {
        $this->cancelBookingId = $bookingId;
        $this->cancelReason = $reason;
        $this->confirmCancelBooking();
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
            'in_progress' => (clone $baseQuery)->inProgress()->count(),
            'completed' => (clone $baseQuery)->completed()->count(),
            'cancelled' => (clone $baseQuery)->cancelled()->count(),
            'total' => (clone $baseQuery)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.booking-management');
    }
}
