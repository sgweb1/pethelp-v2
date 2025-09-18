<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class ChatApp extends Component
{
    public function mount()
    {
        // Handle URL parameters for starting conversation
        $userId = request('user');
        $bookingId = request('booking');

        if ($userId && $bookingId) {
            $user = User::find($userId);
            $booking = Booking::find($bookingId);

            if ($user && $booking) {
                // Verify user has access to this booking
                if ($booking->owner_id === Auth::id() || $booking->sitter_id === Auth::id()) {
                    $this->dispatch('startConversationWith', [
                        'userId' => $user->id,
                        'bookingId' => $booking->id
                    ]);
                }
            }
        } elseif ($userId) {
            $user = User::find($userId);

            if ($user) {
                $this->dispatch('startConversationWith', [
                    'userId' => $user->id,
                    'bookingId' => null
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.chat-app');
    }
}
