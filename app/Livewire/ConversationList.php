<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ConversationList extends Component
{
    use WithPagination;

    public $selectedConversationId = null;

    public function selectConversation($conversationId)
    {
        $this->selectedConversationId = $conversationId;
        $this->dispatch('conversationSelected', $conversationId);
    }

    public function newConversation()
    {
        $this->selectedConversationId = null;
        $this->dispatch('newConversation');
    }

    #[On('startConversationWith')]
    public function handleStartConversation($userId, $bookingId = null)
    {
        // Debugowanie - logujemy otrzymane parametry
        logger()->info('ConversationList: Otrzymano startConversationWith', [
            'userId' => $userId,
            'bookingId' => $bookingId,
            'currentUserId' => Auth::id()
        ]);

        $user = User::find($userId);
        $booking = $bookingId ? Booking::find($bookingId) : null;

        if ($user && $user->id !== Auth::id()) {
            // Znajdź lub utwórz konwersację
            $conversation = Conversation::findOrCreateBetween(Auth::user(), $user, $booking);

            logger()->info('ConversationList: Utworzono/znaleziono konwersację', [
                'conversationId' => $conversation->id
            ]);

            // Zawsze wybieramy konwersację (nawet jeśli już istnieje)
            $this->selectedConversationId = $conversation->id;
            $this->dispatch('conversationSelected', $conversation->id);
        } else {
            logger()->warning('ConversationList: Nie udało się rozpocząć konwersacji', [
                'userFound' => $user ? true : false,
                'isSameUser' => $user ? ($user->id === Auth::id()) : false
            ]);
        }
    }

    #[On('conversationCreatedAndSelected')]
    public function handleConversationCreatedAndSelected($conversationId)
    {
        // Update our selection to match what ChatWindow loaded
        $this->selectedConversationId = $conversationId;
    }

    public function getConversationsProperty()
    {
        return Conversation::with(['userOne', 'userTwo', 'booking', 'latestMessage'])
                          ->forUser(Auth::id())
                          ->recent()
                          ->paginate(15);
    }

    public function render()
    {
        return view('livewire.conversation-list');
    }
}
