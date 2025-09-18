<div class="max-w-6xl mx-auto p-6">
    <!-- Header -->
    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Powiadomienia</h1>
                @if($this->unreadCount > 0)
                    <p class="text-gray-600 mt-1">Masz {{ $this->unreadCount }} nieprzeczytanych powiadomień</p>
                @else
                    <p class="text-gray-600 mt-1">Wszystkie powiadomienia przeczytane</p>
                @endif
            </div>

            <div class="flex items-center space-x-3">
                @if($this->unreadCount > 0)
                    <button wire:click="markAllAsRead"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                        Oznacz wszystkie jako przeczytane
                    </button>
                @endif

                <button wire:click="toggleUnreadFilter"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $showUnreadOnly ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $showUnreadOnly ? 'Pokaż wszystkie' : 'Tylko nieprzeczytane' }}
                </button>
            </div>
        </div>

        <!-- Filters -->
        @if(count($this->notificationTypes) > 0)
            <div class="flex flex-wrap gap-2">
                <button wire:click="filterByType('')"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterType === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Wszystkie
                </button>
                @foreach($this->notificationTypes as $type)
                    <button wire:click="filterByType('{{ $type['type'] }}')"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterType === $type['type'] ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $type['label'] }} ({{ $type['count'] }})
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Notifications List -->
    <div class="space-y-4">
        @forelse($this->notifications as $notification)
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 {{ $notification->isUnread() ? 'border-l-4 border-indigo-600' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Icon -->
                        <div class="text-2xl">{{ $notification->icon }}</div>

                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                                @if($notification->is_important)
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Ważne</span>
                                @endif
                                @if($notification->isUnread())
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Nowe</span>
                                @endif
                            </div>

                            <p class="text-gray-700 mb-3">{{ $notification->message }}</p>

                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <div class="flex items-center space-x-4">
                                    <span>{{ $notification->type_label }}</span>
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                </div>

                                @if($notification->data && isset($notification->data['booking_id']))
                                    <a href="{{ route('bookings') }}" class="text-indigo-600 hover:text-indigo-800">
                                        Zobacz rezerwację →
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2 ml-4">
                        @if($notification->isUnread())
                            <button wire:click="markAsRead({{ $notification->id }})"
                                    class="text-gray-500 hover:text-indigo-600 transition-colors"
                                    title="Oznacz jako przeczytane">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                            </button>
                        @endif

                        <button wire:click="deleteNotification({{ $notification->id }})"
                                class="text-gray-500 hover:text-red-600 transition-colors"
                                title="Usuń powiadomienie"
                                onclick="return confirm('Czy na pewno chcesz usunąć to powiadomienie?')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Additional Data -->
                @if($notification->data)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            @if(isset($notification->data['service_title']))
                                <div>
                                    <span class="font-medium text-gray-600">Usługa:</span>
                                    <div>{{ $notification->data['service_title'] }}</div>
                                </div>
                            @endif

                            @if(isset($notification->data['start_date']))
                                <div>
                                    <span class="font-medium text-gray-600">Data:</span>
                                    <div>{{ $notification->data['start_date'] }}</div>
                                </div>
                            @endif

                            @if(isset($notification->data['amount']))
                                <div>
                                    <span class="font-medium text-gray-600">Kwota:</span>
                                    <div>{{ number_format($notification->data['amount'], 2) }} zł</div>
                                </div>
                            @endif

                            @if(isset($notification->data['pet_name']))
                                <div>
                                    <span class="font-medium text-gray-600">Zwierzę:</span>
                                    <div>{{ $notification->data['pet_name'] }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">🔔</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak powiadomień</h3>
                <p class="text-gray-600">
                    @if($showUnreadOnly)
                        Nie masz nieprzeczytanych powiadomień.
                    @else
                        Nie masz jeszcze żadnych powiadomień.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->notifications->hasPages())
        <div class="mt-8">
            {{ $this->notifications->links() }}
        </div>
    @endif
</div>
