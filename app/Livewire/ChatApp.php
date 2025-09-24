<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class ChatApp extends Component
{
    public $autoStartUserId = null;
    public $autoStartBookingId = null;
    public $fullWidth = false; // Właściwość kontrolująca pełną szerokość

    public function mount()
    {
        // Obsługa parametrów URL do rozpoczęcia konwersacji
        $userId = request('user');
        $bookingId = request('booking');

        logger()->info('ChatApp mount: Rozpoczęcie', [
            'requestUserId' => $userId,
            'requestBookingId' => $bookingId,
            'currentUserId' => Auth::id()
        ]);

        if ($userId && $bookingId) {
            $user = User::find($userId);
            $booking = Booking::find($bookingId);

            logger()->info('ChatApp mount: Znaleziono dane', [
                'user' => $user ? $user->name : 'null',
                'booking' => $booking ? $booking->id : 'null'
            ]);

            if ($user && $booking) {
                // Sprawdzamy czy użytkownik ma dostęp do tej rezerwacji
                $currentUserId = Auth::id();
                $hasAccess = ($booking->owner_id === $currentUserId || $booking->sitter_id === $currentUserId);
                $isOtherParty = (($booking->owner_id === $currentUserId && $booking->sitter_id === $user->id) ||
                                ($booking->sitter_id === $currentUserId && $booking->owner_id === $user->id));

                logger()->info('ChatApp mount: Sprawdzenie dostępu', [
                    'hasAccess' => $hasAccess,
                    'isOtherParty' => $isOtherParty,
                    'booking_owner_id' => $booking->owner_id,
                    'booking_sitter_id' => $booking->sitter_id
                ]);

                if ($hasAccess && $isOtherParty) {
                    $this->autoStartUserId = $user->id;
                    $this->autoStartBookingId = $booking->id;

                    logger()->info('ChatApp mount: Ustawiono autoStart', [
                        'autoStartUserId' => $this->autoStartUserId,
                        'autoStartBookingId' => $this->autoStartBookingId
                    ]);
                }
            }
        } elseif ($userId) {
            $user = User::find($userId);

            if ($user) {
                $this->autoStartUserId = $user->id;
                $this->autoStartBookingId = null;

                logger()->info('ChatApp mount: Ustawiono autoStart tylko userId', [
                    'autoStartUserId' => $this->autoStartUserId
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.chat-app');
    }
}
