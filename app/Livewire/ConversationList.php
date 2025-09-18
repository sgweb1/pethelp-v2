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
        $user = User::find($userId);
        $booking = $bookingId ? Booking::find($bookingId) : null;

        if ($user && $user->id !== Auth::id()) {
            // Find or create conversation
            $conversation = Conversation::findOrCreateBetween(Auth::user(), $user, $booking);

            // Only select if not already selected to avoid loops
            if ($this->selectedConversationId !== $conversation->id) {
                $this->selectedConversationId = $conversation->id;
                $this->dispatch('conversationSelected', $conversation->id);
            }
        }
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
