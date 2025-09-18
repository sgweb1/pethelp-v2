<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

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
