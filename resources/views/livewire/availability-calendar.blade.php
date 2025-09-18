<div class="bg-white rounded-lg shadow-lg p-6">
    <!-- Calendar Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Kalendarz dostępności</h2>
        <div class="flex items-center space-x-4">
            <button
                wire:click="previousMonth"
                class="p-2 hover:bg-gray-100 rounded-full transition-colors"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h3 class="text-xl font-semibold text-gray-800 min-w-48 text-center">
                {{ $this->month_name }} {{ $this->currentYear }}
            </h3>
            <button
                wire:click="nextMonth"
                class="p-2 hover:bg-gray-100 rounded-full transition-colors"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-1 mb-4">
        <!-- Day Headers -->
        <div class="p-3 text-center text-sm font-medium text-gray-500">Pn</div>
        <div class="p-3 text-center text-sm font-medium text-gray-500">Wt</div>
        <div class="p-3 text-center text-sm font-medium text-gray-500">Śr</div>
        <div class="p-3 text-center text-sm font-medium text-gray-500">Cz</div>
        <div class="p-3 text-center text-sm font-medium text-gray-500">Pt</div>
        <div class="p-3 text-center text-sm font-medium text-gray-500">So</div>
        <div class="p-3 text-center text-sm font-medium text-gray-500">Nd</div>

        <!-- Empty cells for days before month start -->
        @php
            $firstDay = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, 1);
            $startDayOfWeek = $firstDay->dayOfWeek;
            $emptyDays = $startDayOfWeek === 0 ? 6 : $startDayOfWeek - 1;
        @endphp

        @for($i = 0; $i < $emptyDays; $i++)
            <div class="p-3"></div>
        @endfor

        <!-- Calendar Days -->
        @foreach($this->current_month_days as $day)
            @php
                $dateKey = $day->format('Y-m-d');
                $availability = $this->availability_for_month->get($dateKey);
                $isPast = $day->isPast();
                $isToday = $day->isToday();
            @endphp

            <div class="relative">
                <button
                    wire:click="selectDate('{{ $dateKey }}')"
                    @class([
                        'w-full p-3 text-sm rounded-lg transition-all duration-200 hover:bg-gray-50',
                        'bg-blue-100 text-blue-800 font-semibold' => $isToday,
                        'text-gray-400 cursor-not-allowed' => $isPast,
                        'hover:bg-blue-50' => !$isPast,
                        'bg-green-50 border border-green-200' => $availability && $availability->is_available,
                        'bg-red-50 border border-red-200' => $availability && !$availability->is_available,
                    ])
                    @if($isPast) disabled @endif
                >
                    <div class="flex flex-col items-center">
                        <span class="font-medium">{{ $day->day }}</span>
                        @if($availability)
                            <div class="mt-1 flex flex-col text-xs">
                                @if($availability->is_available)
                                    <span class="text-green-600 font-medium">
                                        {{ $availability->start_time->format('H:i') }}-{{ $availability->end_time->format('H:i') }}
                                    </span>
                                    <span class="text-green-500">Dostępny</span>
                                @else
                                    <span class="text-red-500 font-medium">Niedostępny</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </button>
            </div>
        @endforeach
    </div>

    <!-- Legend -->
    <div class="flex items-center justify-center space-x-6 text-sm text-gray-600 mb-6">
        <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-green-100 border border-green-200 rounded"></div>
            <span>Dostępny</span>
        </div>
        <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-red-100 border border-red-200 rounded"></div>
            <span>Niedostępny</span>
        </div>
        <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-blue-100 rounded"></div>
            <span>Dzisiaj</span>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="border-t pt-4">
        <h4 class="text-lg font-semibold text-gray-900 mb-3">Szybkie akcje</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button
                wire:click="selectDate('{{ now()->format('Y-m-d') }}')"
                class="p-3 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors text-left"
            >
                <div class="font-medium">Ustaw dzisiejszą dostępność</div>
                <div class="text-sm text-indigo-600">{{ now()->format('d.m.Y') }}</div>
            </button>
            <button
                wire:click="selectDate('{{ now()->addDay()->format('Y-m-d') }}')"
                class="p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-left"
            >
                <div class="font-medium">Ustaw jutrzejszą dostępność</div>
                <div class="text-sm text-green-600">{{ now()->addDay()->format('d.m.Y') }}</div>
            </button>
        </div>
    </div>

    <!-- Modal for editing availability -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-96 overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $editingAvailability ? 'Edytuj dostępność' : 'Dodaj dostępność' }}
                        </h3>
                        <button
                            wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveAvailability" class="space-y-4">
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                            <input
                                type="date"
                                wire:model="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                min="{{ now()->format('Y-m-d') }}"
                            >
                            @error('date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Available Toggle -->
                        <div class="flex items-center space-x-3">
                            <input
                                type="checkbox"
                                wire:model.live="is_available"
                                id="is_available"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <label for="is_available" class="text-sm font-medium text-gray-700">
                                Jestem dostępny w tym dniu
                            </label>
                        </div>

                        @if($is_available)
                            <!-- Time Range -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Od godziny</label>
                                    <input
                                        type="time"
                                        wire:model="start_time"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    >
                                    @error('start_time') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Do godziny</label>
                                    <input
                                        type="time"
                                        wire:model="end_time"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    >
                                    @error('end_time') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Recurring Options -->
                            <div>
                                <div class="flex items-center space-x-3 mb-3">
                                    <input
                                        type="checkbox"
                                        wire:model.live="is_recurring"
                                        id="is_recurring"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    >
                                    <label for="is_recurring" class="text-sm font-medium text-gray-700">
                                        Powtarzaj co tydzień
                                    </label>
                                </div>

                                @if($is_recurring)
                                    <div class="pl-6">
                                        <p class="text-sm text-gray-600 mb-2">Wybierz dni tygodnia:</p>
                                        <div class="grid grid-cols-4 gap-2">
                                            @php
                                                $days = [
                                                    1 => 'Pn', 2 => 'Wt', 3 => 'Śr', 4 => 'Cz',
                                                    5 => 'Pt', 6 => 'So', 0 => 'Nd'
                                                ];
                                            @endphp
                                            @foreach($days as $dayNum => $dayName)
                                                <label class="flex items-center space-x-1">
                                                    <input
                                                        type="checkbox"
                                                        wire:model="recurring_days"
                                                        value="{{ $dayNum }}"
                                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    >
                                                    <span class="text-xs">{{ $dayName }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notatki (opcjonalne)</label>
                            <textarea
                                wire:model="notes"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Dodatkowe informacje o dostępności..."
                            ></textarea>
                            @error('notes') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t">
                            @if($editingAvailability)
                                <button
                                    type="button"
                                    wire:click="deleteAvailability({{ $editingAvailability }})"
                                    wire:confirm="Czy na pewno chcesz usunąć tę dostępność?"
                                    class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                >
                                    Usuń
                                </button>
                            @else
                                <div></div>
                            @endif

                            <div class="flex space-x-3">
                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                >
                                    Anuluj
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                                >
                                    {{ $editingAvailability ? 'Zapisz zmiany' : 'Dodaj dostępność' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- JavaScript for notifications -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('availability-saved', () => {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                notification.textContent = '✅ Dostępność została zapisana!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            });

            Livewire.on('availability-deleted', () => {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                notification.textContent = '🗑️ Dostępność została usunięta!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            });
        });
    </script>
</div>
