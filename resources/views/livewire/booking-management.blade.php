<div>

    <!-- Filters -->
    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 mb-8">
        <!-- Responsive layout: switch on top for mobile, inline for desktop -->
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-center gap-4">
            <!-- View Toggle - na górze w mobile -->
            <div class="flex justify-center xl:hidden">
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button wire:click="changeView('owner')"
                            class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $view === 'owner' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Moje rezerwacje
                    </button>
                    <button wire:click="changeView('sitter')"
                            class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $view === 'sitter' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Jako opiekun
                    </button>
                </div>
            </div>

            <!-- Desktop: filtry z switch w środku -->
            <div class="flex flex-wrap justify-center items-center gap-2">
                <!-- Pierwsze filtry -->
                <button wire:click="filterByStatus('')"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 {{ $statusFilter === '' ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
                    Wszystkie
                    <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded-full">{{ $this->booking_stats['total'] }}</span>
                </button>
                <button wire:click="filterByStatus('pending')"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 {{ $statusFilter === 'pending' ? 'bg-yellow-600 text-white' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' }}">
                    Oczekujące
                    <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded-full">{{ $this->booking_stats['pending'] }}</span>
                </button>
                <button wire:click="filterByStatus('confirmed')"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 {{ $statusFilter === 'confirmed' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                    Potwierdzone
                    <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded-full">{{ $this->booking_stats['confirmed'] }}</span>
                </button>

                <!-- View Toggle - w środku dla desktop -->
                <div class="hidden xl:flex bg-gray-100 rounded-lg p-1 mx-2">
                    <button wire:click="changeView('owner')"
                            class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $view === 'owner' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Moje rezerwacje
                    </button>
                    <button wire:click="changeView('sitter')"
                            class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $view === 'sitter' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Jako opiekun
                    </button>
                </div>

                <!-- Pozostałe filtry -->
                <button wire:click="filterByStatus('in_progress')"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 {{ $statusFilter === 'in_progress' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' }}">
                    W trakcie
                    <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded-full">{{ $this->booking_stats['in_progress'] }}</span>
                </button>
                <button wire:click="filterByStatus('completed')"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 {{ $statusFilter === 'completed' ? 'bg-green-600 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                    Zakończone
                    <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded-full">{{ $this->booking_stats['completed'] }}</span>
                </button>
                <button wire:click="filterByStatus('cancelled')"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 {{ $statusFilter === 'cancelled' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                    Anulowane
                    <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded-full">{{ $this->booking_stats['cancelled'] }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
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
                        <button wire:click="openCancelModal({{ $booking->id }})"
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
                        <button wire:click="openReviewModal({{ $booking->id }})"
                                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                            Napisz recenzję
                        </button>
                    @endif

                    <!-- Chat Button -->
                    @php
                        $otherUser = $view === 'owner' ? $booking->sitter : $booking->owner;
                    @endphp
                    <a href="{{ route('profile.chat.index') }}?user={{ $otherUser->id }}&booking={{ $booking->id }}"
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
                                            @foreach($selectedBooking->pet->special_needs_list as $index => $need)
                                                <span wire:key="special-need-{{ $selectedBooking->pet->id }}-{{ $index }}" class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">{{ $need }}</span>
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

                                @if($selectedBooking->status === 'cancelled')
                                    <div class="border-t pt-2 mt-2">
                                        <div class="flex justify-between">
                                            <span class="text-red-600">Data anulowania:</span>
                                            <span class="font-medium text-red-600">
                                                {{ $selectedBooking->cancelled_at ? $selectedBooking->cancelled_at->format('d.m.Y H:i') : 'Nieznana' }}
                                            </span>
                                        </div>
                                        @if($selectedBooking->cancellation_reason)
                                            <div class="mt-2">
                                                <span class="text-red-600">Powód anulowania:</span>
                                                <div class="bg-red-50 p-2 rounded mt-1 text-red-800 text-sm">
                                                    {{ $selectedBooking->cancellation_reason }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
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

    <!-- Cancel Modal -->
    @if($showCancelModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeCancelModal">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4" wire:click.stop>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Anuluj rezerwację</h2>
                        <button wire:click="closeCancelModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-6">
                        <p class="text-gray-600 mb-4">Czy na pewno chcesz anulować tę rezerwację?</p>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Powód anulowania (opcjonalnie):</label>
                            <textarea wire:model="cancelReason"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    rows="3"
                                    placeholder="Wprowadź powód anulowania..."></textarea>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button wire:click="closeCancelModal"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                            Anuluj
                        </button>
                        <button wire:click="confirmCancelBooking"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                            Potwierdź anulowanie
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Review Modal -->
    @if($showReviewModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeReviewModal">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 max-h-screen overflow-y-auto" wire:click.stop>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Napisz recenzję</h2>
                        <button wire:click="closeReviewModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Rating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ocena:</label>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button wire:click="$set('reviewRating', {{ $i }})"
                                            class="text-2xl {{ $reviewRating >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400">
                                        ⭐
                                    </button>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">({{ $reviewRating }}/5)</span>
                            </div>
                            @error('reviewRating')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Comment -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Komentarz:</label>
                            <textarea wire:model="reviewComment"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    rows="4"
                                    placeholder="Opisz swoje doświadczenia z tą usługą..."></textarea>
                            @error('reviewComment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end mt-6">
                        <button wire:click="closeReviewModal"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                            Anuluj
                        </button>
                        <button wire:click="submitReview"
                                class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">
                            Dodaj recenzję
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
