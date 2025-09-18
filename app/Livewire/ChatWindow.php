<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

class ChatWindow extends Component
{
    public ?Conversation $conversation = null;
    public ?User $recipient = null;
    public ?Booking $booking = null;

    #[Validate('required|string|max:1000')]
    public $newMessage = '';

    #[On('conversationSelected')]
    public function loadConversation($conversationId)
    {
        $this->conversation = Conversation::with(['userOne', 'userTwo', 'booking'])->find($conversationId);

        if ($this->conversation) {
            $this->recipient = $this->conversation->getOtherUser(Auth::user());
            $this->booking = $this->conversation->booking;

            // Mark messages as read
            $this->conversation->markAsRead(Auth::user());
        }
    }

    #[On('newConversation')]
    public function startNewConversation()
    {
        $this->conversation = null;
        $this->recipient = null;
        $this->booking = null;
    }

    #[On('startConversationWith')]
    public function handleStartConversation($userId, $bookingId = null)
    {
        $user = User::find($userId);
        $booking = $bookingId ? Booking::find($bookingId) : null;

        if ($user && $user->id !== Auth::id()) {
            // Only start if we don't already have this conversation
            $existingConversation = $this->conversation;
            $newConversation = Conversation::findOrCreateBetween(Auth::user(), $user, $booking);

            if (!$existingConversation || $existingConversation->id !== $newConversation->id) {
                $this->startConversationWith($user, $booking);
            }
        }
    }

    public function startConversationWith(User $user, ?Booking $booking = null)
    {
        $this->conversation = Conversation::findOrCreateBetween(Auth::user(), $user, $booking);
        $this->recipient = $user;
        $this->booking = $booking;

        $this->conversation->markAsRead(Auth::user());
    }

    public function sendMessage()
    {
        if (!$this->conversation || !$this->recipient) {
            session()->flash('error', 'Wybierz konwersację lub osobę do napisania.');
            return;
        }

        $this->validate();

        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => Auth::id(),
            'message' => $this->newMessage,
        ]);

        // Send notification
        $notificationService = app(\App\Services\NotificationService::class);
        $notificationService->notifyMessageReceived($message);

        $this->newMessage = '';

        // Refresh the conversation to get the latest messages
        $this->conversation->refresh();

        $this->dispatch('messageSent');
    }

    public function getMessagesProperty()
    {
        if (!$this->conversation) {
            return collect();
        }

        return $this->conversation->messages()
                                 ->with('sender')
                                 ->orderBy('created_at', 'asc')
                                 ->get();
    }

    public function render()
    {
        return view('livewire.chat-window');
    }
}
