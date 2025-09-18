<div class="max-w-7xl mx-auto p-6">
    <!-- Header -->
    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Zarządzanie rezerwacjami</h1>

            <!-- View Toggle -->
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button wire:click="changeView('owner')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $view === 'owner' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    Moje rezerwacje
                </button>
                <button wire:click="changeView('sitter')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $view === 'sitter' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    Jako opiekun
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-yellow-600">{{ $this->booking_stats['pending'] }}</div>
                <div class="text-sm text-yellow-800">Oczekujące</div>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-blue-600">{{ $this->booking_stats['confirmed'] }}</div>
                <div class="text-sm text-blue-800">Potwierdzone</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-green-600">{{ $this->booking_stats['completed'] }}</div>
                <div class="text-sm text-green-800">Zakończone</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-gray-600">{{ $this->booking_stats['total'] }}</div>
                <div class="text-sm text-gray-800">Wszystkie</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-wrap gap-2">
            <button wire:click="filterByStatus('')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Wszystkie
            </button>
            <button wire:click="filterByStatus('pending')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Oczekujące
            </button>
            <button wire:click="filterByStatus('confirmed')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === 'confirmed' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Potwierdzone
            </button>
            <button wire:click="filterByStatus('completed')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Zakończone
            </button>
            <button wire:click="filterByStatus('cancelled')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $statusFilter === 'cancelled' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Anulowane
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Bookings List -->
    <div class="space-y-4">
        @forelse($this->bookings as $booking)
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="text-2xl">{{ $booking->service->category->icon ?? '🐾' }}</div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $booking->service->title }}</h3>
                            <p class="text-gray-600">
                                @if($view === 'owner')
                                    Opiekun: {{ $booking->sitter->name }}
                                @else
                                    Właściciel: {{ $booking->owner->name }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-500">Zwierzę: {{ $booking->pet->name }} ({{ $booking->pet->type_label }})</p>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                            @elseif($booking->status === 'completed') bg-green-100 text-green-800
                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $booking->status_label }}
                        </span>
                        <div class="text-lg font-semibold text-gray-900 mt-1">{{ number_format($booking->total_price, 2) }} zł</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="text-sm text-gray-600">Okres:</div>
                        <div class="font-medium">{{ $booking->start_date->format('d.m.Y H:i') }} - {{ $booking->end_date->format('d.m.Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Czas trwania:</div>
                        <div class="font-medium">{{ $booking->duration_in_hours }} godzin</div>
                    </div>
                </div>

                @if($booking->special_instructions)
                    <div class="mb-4">
                        <div class="text-sm text-gray-600">Dodatkowe uwagi:</div>
                        <div class="text-sm bg-gray-50 p-3 rounded-lg">{{ $booking->special_instructions }}</div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                    <button wire:click="viewBooking({{ $booking->id }})"
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                        Zobacz szczegóły
                    </button>

                    @if($view === 'owner' && $booking->status === 'pending' && !$booking->payment)
                        <a href="{{ route('payment.process', $booking) }}"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                            💳 Zapłać
                        </a>
                    @endif

                    @if($view === 'sitter' && $booking->canBeConfirmed())
                        <button wire:click="confirmBooking({{ $booking->id }})"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                            Potwierdź
                        </button>
                    @endif

                    @if($booking->canBeCancelled())
                        <button wire:click="cancelBooking({{ $booking->id }})"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm">
                            Anuluj
                        </button>
                    @endif

                    @if($view === 'sitter' && $booking->canBeCompleted())
                        <button wire:click="completeBooking({{ $booking->id }})"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Zakończ
                        </button>
                    @endif

                    @if($booking->canBeReviewedBy(Auth::user()))
                        <a href="{{ route('review.create', $booking) }}"
                           class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                            Napisz recenzję
                        </a>
                    @endif

                    <!-- Chat Button -->
                    @php
                        $otherUser = $view === 'owner' ? $booking->sitter : $booking->owner;
                    @endphp
                    <a href="{{ route('chat') }}?user={{ $otherUser->id }}&booking={{ $booking->id }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                        💬 Czat
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">📅</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak rezerwacji</h3>
                <p class="text-gray-600">
                    @if($view === 'owner')
                        Nie masz jeszcze żadnych rezerwacji. <a href="{{ route('search') }}" class="text-indigo-600 underline">Znajdź opiekuna</a> dla swojego pupila!
                    @else
                        Nie masz jeszcze żadnych rezerwacji jako opiekun.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->bookings->hasPages())
        <div class="mt-8">
            {{ $this->bookings->links() }}
        </div>
    @endif

    <!-- Booking Details Modal -->
    @if($showModal && $selectedBooking)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeModal">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto" wire:click.stop>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Szczegóły rezerwacji</h2>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Service Info -->
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Usługa</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="text-2xl">{{ $selectedBooking->service->category->icon ?? '🐾' }}</span>
                                    <div>
                                        <div class="font-medium">{{ $selectedBooking->service->title }}</div>
                                        <div class="text-sm text-gray-600">{{ $selectedBooking->service->category->name }}</div>
                                    </div>
                                </div>
                                @if($selectedBooking->service->description)
                                    <p class="text-sm text-gray-700">{{ $selectedBooking->service->description }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Participants -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Właściciel</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="font-medium">{{ $selectedBooking->owner->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $selectedBooking->owner->email }}</div>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Opiekun</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="font-medium">{{ $selectedBooking->sitter->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $selectedBooking->sitter->email }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Pet Info -->
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Zwierzę</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="font-medium">{{ $selectedBooking->pet->name }}</div>
                                        <div class="text-sm text-gray-600">
                                            {{ $selectedBooking->pet->type_label }} • {{ $selectedBooking->pet->size_label }} • {{ $selectedBooking->pet->age_group }}
                                        </div>
                                        @if($selectedBooking->pet->breed)
                                            <div class="text-sm text-gray-600">Rasa: {{ $selectedBooking->pet->breed }}</div>
                                        @endif
                                    </div>
                                </div>
                                @if($selectedBooking->pet->description)
                                    <p class="text-sm text-gray-700 mb-2">{{ $selectedBooking->pet->description }}</p>
                                @endif
                                @if($selectedBooking->pet->hasSpecialNeeds())
                                    <div class="text-sm">
                                        <span class="font-medium text-orange-700">Specjalne potrzeby:</span>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($selectedBooking->pet->special_needs_list as $need)
                                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">{{ $need }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Szczegóły rezerwacji</h3>
                            <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                                <div class="flex justify-between">
                                    <span>Status:</span>
                                    <span class="font-medium">{{ $selectedBooking->status_label }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Data rozpoczęcia:</span>
                                    <span class="font-medium">{{ $selectedBooking->start_date->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Data zakończenia:</span>
                                    <span class="font-medium">{{ $selectedBooking->end_date->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Czas trwania:</span>
                                    <span class="font-medium">{{ $selectedBooking->duration_in_hours }} godzin</span>
                                </div>
                                <div class="flex justify-between text-lg font-semibold border-t pt-2">
                                    <span>Cena całkowita:</span>
                                    <span>{{ number_format($selectedBooking->total_price, 2) }} zł</span>
                                </div>
                            </div>
                        </div>

                        @if($selectedBooking->special_instructions)
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Dodatkowe uwagi</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    {{ $selectedBooking->special_instructions }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
