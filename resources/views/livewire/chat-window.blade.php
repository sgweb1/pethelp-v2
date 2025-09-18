<div class="h-full flex flex-col bg-white">
    @if($conversation && $recipient)
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center space-x-3">
                <!-- Recipient Avatar -->
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ substr($recipient->name, 0, 1) }}
                </div>

                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900">{{ $recipient->name }}</h3>
                    @if($booking)
                        <p class="text-sm text-indigo-600">
                            📅 {{ $booking->service->title }} - {{ $booking->start_date->format('d.m.Y') }}
                        </p>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    @if($booking)
                        <a href="{{ route('bookings') }}"
                           class="text-gray-500 hover:text-indigo-600 transition-colors"
                           title="Zobacz rezerwację">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messagesContainer">
            @forelse($this->messages as $message)
                <div class="flex {{ $message->isFromUser(Auth::user()) ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md">
                        @if(!$message->isFromUser(Auth::user()))
                            <!-- Other user message -->
                            <div class="flex items-start space-x-2">
                                <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr($message->sender->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="bg-gray-100 text-gray-800 rounded-lg px-4 py-2">
                                        {{ $message->message }}
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $message->time_ago }}</p>
                                </div>
                            </div>
                        @else
                            <!-- Current user message -->
                            <div class="flex items-start space-x-2 justify-end">
                                <div>
                                    <div class="bg-indigo-600 text-white rounded-lg px-4 py-2">
                                        {{ $message->message }}
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 text-right">
                                        {{ $message->time_ago }}
                                        @if($message->is_read)
                                            <span class="text-indigo-600">✓✓</span>
                                        @else
                                            <span class="text-gray-400">✓</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="text-6xl mb-4">💬</div>
                    <p class="text-gray-500">Brak wiadomości. Napisz coś!</p>
                </div>
            @endforelse
        </div>

        <!-- Message Input -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit="sendMessage" class="flex space-x-3">
                <div class="flex-1">
                    <textarea
                        wire:model="newMessage"
                        placeholder="Napisz wiadomość..."
                        rows="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                        style="min-height: 40px; max-height: 120px;"
                        onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); @this.call('sendMessage'); }"></textarea>
                    @error('newMessage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        wire:loading.attr="disabled"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 flex-shrink-0">
                    <span wire:loading.remove>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </span>
                    <span wire:loading>
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    @else
        <!-- No conversation selected -->
        <div class="h-full flex items-center justify-center">
            <div class="text-center">
                <div class="text-6xl mb-4">💬</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Wybierz konwersację</h3>
                <p class="text-gray-600">Kliknij na konwersację z listy, aby rozpocząć czat.</p>
            </div>
        </div>
    @endif
</div>

<script>
    // Auto-scroll to bottom when new messages arrive
    document.addEventListener('livewire:init', () => {
        let isScrolling = false;

        Livewire.hook('message.processed', (message, component) => {
            if (component.fingerprint.name === 'chat-window' && !isScrolling) {
                isScrolling = true;
                setTimeout(() => {
                    const container = document.getElementById('messagesContainer');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                    isScrolling = false;
                }, 100);
            }
        });
    });
</script>
