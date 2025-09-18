<div class="h-full flex flex-col bg-white">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Konwersacje</h2>
            <button wire:click="newConversation"
                    class="bg-indigo-600 text-white p-2 rounded-lg hover:bg-indigo-700 transition-colors"
                    title="Nowa konwersacja">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Conversation List -->
    <div class="flex-1 overflow-y-auto">
        @forelse($this->conversations as $conversation)
            @php
                $otherUser = $conversation->getOtherUser(Auth::user());
                $latestMessage = $conversation->latestMessage->first();
                $unreadCount = $conversation->getUnreadCount(Auth::user());
            @endphp

            <div wire:click="selectConversation({{ $conversation->id }})"
                 class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors {{ $selectedConversationId == $conversation->id ? 'bg-indigo-50 border-indigo-200' : '' }}">

                <div class="flex items-start space-x-3">
                    <!-- Avatar -->
                    <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ substr($otherUser->name, 0, 1) }}
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium text-gray-900 truncate">{{ $otherUser->name }}</h3>
                            <div class="flex items-center space-x-2">
                                @if($unreadCount > 0)
                                    <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                                @if($latestMessage)
                                    <span class="text-xs text-gray-500">
                                        {{ $latestMessage->created_at->format('H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($conversation->booking)
                            <p class="text-xs text-indigo-600 mb-1">
                                📅 {{ $conversation->booking->service->title }}
                            </p>
                        @endif

                        @if($latestMessage)
                            <p class="text-sm text-gray-600 truncate">
                                @if($latestMessage->sender_id === Auth::id())
                                    <span class="text-gray-500">Ty:</span>
                                @endif
                                {{ $latestMessage->message }}
                            </p>
                        @else
                            <p class="text-sm text-gray-400 italic">Brak wiadomości</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center">
                <div class="text-6xl mb-4">💬</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Brak konwersacji</h3>
                <p class="text-gray-600 mb-4">Nie masz jeszcze żadnych konwersacji.</p>
                <button wire:click="newConversation"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    Rozpocznij czat
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->conversations->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $this->conversations->links() }}
        </div>
    @endif
</div>
