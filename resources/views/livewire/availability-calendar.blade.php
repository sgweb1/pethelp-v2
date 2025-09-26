<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
    <!-- Compact Calendar Header -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-2">
            <button
                wire:click="previousMonth"
                class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors"
            >
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h3 class="text-base font-semibold text-gray-800 dark:text-white min-w-28 text-center">
                {{ $this->month_name }} {{ $this->currentYear }}
            </h3>
            <button
                wire:click="nextMonth"
                class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors"
            >
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        <!-- Compact Legend -->
        <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-400">
            <div class="flex items-center space-x-1">
                <div class="w-2 h-2 bg-green-100 border border-green-200 rounded"></div>
                <span class="hidden sm:inline">Dostępny</span>
            </div>
            <div class="flex items-center space-x-1">
                <div class="w-2 h-2 bg-red-100 border border-red-200 rounded"></div>
                <span class="hidden sm:inline">Niedostępny</span>
            </div>
            <div class="flex items-center space-x-1">
                <div class="w-2 h-2 bg-yellow-100 border border-yellow-200 rounded"></div>
                <span class="hidden sm:inline">Mieszane</span>
            </div>
            <div class="flex items-center space-x-1">
                <div class="w-2 h-2 bg-blue-100 rounded"></div>
                <span class="hidden sm:inline">Dzisiaj</span>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-1 mb-3">
        <!-- Day Headers -->
        <div class="p-2 text-center text-xs font-medium text-gray-500">Pn</div>
        <div class="p-2 text-center text-xs font-medium text-gray-500">Wt</div>
        <div class="p-2 text-center text-xs font-medium text-gray-500">Śr</div>
        <div class="p-2 text-center text-xs font-medium text-gray-500">Cz</div>
        <div class="p-2 text-center text-xs font-medium text-gray-500">Pt</div>
        <div class="p-2 text-center text-xs font-medium text-gray-500">So</div>
        <div class="p-2 text-center text-xs font-medium text-gray-500">Nd</div>

        <!-- Empty cells for days before month start -->
        @php
            $firstDay = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, 1);
            $startDayOfWeek = $firstDay->dayOfWeek;
            $emptyDays = $startDayOfWeek === 0 ? 6 : $startDayOfWeek - 1;
        @endphp

        @for($i = 0; $i < $emptyDays; $i++)
            <div class="p-2"></div>
        @endfor

        <!-- Calendar Days -->
        @foreach($this->current_month_days as $day)
            @if($day)
                @php
                    $dateKey = $day->format('Y-m-d');
                    $dayAvailabilities = $this->availability_for_month->filter(function($availability) use ($dateKey) {
                        return $availability->available_date && $availability->available_date->format('Y-m-d') === $dateKey;
                    });
                    $isPast = $day->isPast();
                    $isToday = $day->isToday();
                    $hasAvailability = $dayAvailabilities->isNotEmpty();
                    $allAvailable = $dayAvailabilities->where('is_available', true)->isNotEmpty();
                    $hasUnavailable = $dayAvailabilities->where('is_available', false)->isNotEmpty();
                @endphp

            <div class="relative">
                <button
                    wire:click="selectDate('{{ $dateKey }}')"
                    @class([
                        'w-full p-2 text-xs rounded transition-all duration-200 hover:bg-gray-50',
                        'bg-blue-100 text-blue-800 font-semibold' => $isToday,
                        'text-gray-400 cursor-not-allowed' => $isPast,
                        'hover:bg-blue-50' => !$isPast,
                        'bg-green-50 border border-green-200' => $hasAvailability && $allAvailable && !$hasUnavailable,
                        'bg-red-50 border border-red-200' => $hasAvailability && $hasUnavailable && !$allAvailable,
                        'bg-yellow-50 border border-yellow-200' => $hasAvailability && $allAvailable && $hasUnavailable,
                    ])
                    @if($isPast) disabled @endif
                >
                    <div class="flex flex-col items-center min-h-[40px]">
                        <span class="font-medium text-sm">{{ $day->day }}</span>
                        @if($hasAvailability)
                            <div class="mt-0.5 text-xs leading-tight space-y-0.5">
                                @foreach($dayAvailabilities->take(3) as $availability)
                                    @if($availability->is_available)
                                        <div class="text-green-600 font-medium">
                                            {{ $availability->time_slot_label }}
                                            @if($availability->service_type ?? false)
                                                <div class="text-xs text-green-500 mt-0.5">{{ $availability->service_type_label }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-red-500 font-medium">
                                            ✕ {{ $availability->time_slot_label }}
                                            @if($availability->service_type ?? false)
                                                <div class="text-xs text-red-400 mt-0.5">{{ $availability->service_type_label }}</div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                                @if($dayAvailabilities->count() > 3)
                                    <div class="text-gray-500 font-medium">+{{ $dayAvailabilities->count() - 3 }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </button>
            </div>
            @endif
        @endforeach
    </div>

    <!-- Quick Actions & Info -->
    <div class="border-t pt-3 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <button
                wire:click="selectDate('{{ now()->format('Y-m-d') }}')"
                class="p-3 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors text-left"
            >
                <div class="font-medium text-sm">Ustaw dzisiejszą dostępność</div>
                <div class="text-xs text-indigo-600">{{ now()->format('d.m.Y') }}</div>
            </button>
            <button
                wire:click="selectDate('{{ now()->addDay()->format('Y-m-d') }}')"
                class="p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-left"
            >
                <div class="font-medium text-sm">Ustaw jutrzejszą dostępność</div>
                <div class="text-xs text-green-600">{{ now()->addDay()->format('d.m.Y') }}</div>
            </button>
            <button
                wire:click="openVacationModal"
                class="p-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors text-left"
            >
                <div class="font-medium text-sm">🏖️ Tryb urlopowy</div>
                <div class="text-xs text-orange-600">Ustaw długotrwałą niedostępność</div>
            </button>
        </div>

        <div class="p-3 bg-blue-50 rounded-lg">
            <h5 class="text-sm font-medium text-blue-900 mb-1">💡 Wskazówki</h5>
            <ul class="text-xs text-blue-800 space-y-1">
                <li>• Możesz ustawiać wiele slotów w ciągu dnia (np. rano + wieczorem)</li>
                <li>• Każdy slot może mieć różne typy usług i dowolne godziny</li>
                <li>• Wybierz szablon slotu lub ustaw własne godziny</li>
                <li>• Sloty można edytować i usuwać niezależnie</li>
            </ul>
        </div>
    </div>

    <!-- Vacation Mode Modal -->
    @if($showVacationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            🏖️ Tryb urlopowy
                        </h3>
                        <button
                            wire:click="closeVacationModal"
                            class="text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveVacation" class="space-y-4">
                        <!-- Date Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Data rozpoczęcia urlopu
                                </label>
                                <input
                                    type="date"
                                    wire:model="vacationFromDate"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    required
                                >
                                @error('vacationFromDate') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Data zakończenia urlopu
                                </label>
                                <input
                                    type="date"
                                    wire:model="vacationToDate"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    required
                                >
                                @error('vacationToDate') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Time Settings -->
                        <div>
                            <div class="flex items-center space-x-3 mb-3">
                                <input
                                    type="checkbox"
                                    wire:model.live="vacationAllDay"
                                    id="vacationAllDay"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                                >
                                <label for="vacationAllDay" class="text-sm font-medium text-gray-700">
                                    Cały dzień (24h)
                                </label>
                            </div>

                            @if(!$vacationAllDay)
                                <div class="grid grid-cols-2 gap-4 pl-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Od godziny
                                        </label>
                                        <input
                                            type="time"
                                            wire:model="vacationStartTime"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                        >
                                        @error('vacationStartTime') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Do godziny
                                        </label>
                                        <input
                                            type="time"
                                            wire:model="vacationEndTime"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                        >
                                        @error('vacationEndTime') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Notatki (opcjonalne)
                            </label>
                            <textarea
                                wire:model="vacationNotes"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="Np. urlop świąteczny, wyjazd służbowy..."
                            ></textarea>
                            @error('vacationNotes') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Info Box -->
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-orange-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-orange-700">
                                    <p class="font-medium mb-1">Tryb urlopowy:</p>
                                    <ul class="text-xs space-y-1">
                                        <li>• Nadpisuje wszystkie istniejące terminy w wybranym okresie</li>
                                        <li>• W wynikach wyszukiwania pojawi się komunikat o niedostępności</li>
                                        <li>• Można anulować w dowolnym momencie</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button
                                type="button"
                                wire:click="closeVacationModal"
                                class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                            >
                                Anuluj
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors"
                            >
                                🏖️ Aktywuj tryb urlopowy
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal for managing availability -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Header (fixed) -->
                <div class="p-6 pb-4 border-b border-gray-200 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dostępność - {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
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
                </div>

                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto pr-4">
                    <div class="pl-6 pr-2 py-6">

                    <!-- Existing Slots -->
                    @if(!empty($availability_slots))
                        <div class="mb-6">
                            <h4 class="text-xs font-medium text-gray-900 mb-3">Istniejące sloty czasowe</h4>
                            <div class="space-y-2">
                                @foreach($availability_slots as $slot)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-medium">
                                                    {{ $slot['time_slot_label'] ?? 'Cały dzień' }}
                                                </span>
                                                <span class="text-xs text-gray-600">
                                                    {{ \Carbon\Carbon::parse($slot['start_time'])->format('H:i') }}-{{ \Carbon\Carbon::parse($slot['end_time'])->format('H:i') }}
                                                </span>
                                                @if(isset($slot['service_type']) && $slot['service_type'])
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                                        {{ $slot['service_type_label'] }}
                                                    </span>
                                                @endif
                                                @if(!$slot['is_available'])
                                                    @if($slot['time_slot'] === 'vacation')
                                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded">🏖️ Urlop</span>
                                                    @else
                                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">Niedostępny</span>
                                                    @endif
                                                @endif
                                            </div>
                                            @if($slot['available_services'] && count($slot['available_services']) > 0)
                                                <div class="mt-1">
                                                    <span class="text-xs text-gray-500">Dostępne usługi:</span>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($slot['available_services'] as $service)
                                                            <div class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">
                                                                <span class="font-medium">{{ is_array($service) ? $service['title'] : $service }}</span>
                                                                @if(is_array($service) && !empty($service['type_label']))
                                                                    <span class="px-1 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">
                                                                        ({{ $service['type_label'] }})
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            @if($slot['notes'])
                                                <div class="mt-1">
                                                    <span class="text-xs text-gray-500">Notatki: {{ $slot['notes'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <button
                                                wire:click="editSlot({{ $slot['id'] }})"
                                                class="text-blue-600 hover:text-blue-700 text-xs"
                                            >
                                                Edytuj
                                            </button>
                                            <button
                                                wire:click="confirmDeleteSlot({{ $slot['id'] }}, '{{ $slot['available_date'] }}', '{{ $slot['start_time'] }}-{{ $slot['end_time'] }}')"
                                                class="text-red-600 hover:text-red-700 text-xs"
                                            >
                                                Usuń
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add/Edit Slot Form -->
                    <div class="border-t pt-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-medium text-gray-900">
                                {{ $editingAvailability ? 'Edytuj slot' : 'Dodaj nowy slot' }}
                            </h4>
                            @php
                                $maxSlots = auth()->user()->getMaxAvailabilitySlots();
                                $currentCount = count($availability_slots);
                                $isPremium = auth()->user()->isPremium();
                            @endphp
                            <div class="text-xs text-gray-500">
                                <span class="font-medium">{{ $currentCount }}/{{ $maxSlots }}</span> slotów
                                @if(!$isPremium && $currentCount >= 2)
                                    <span class="ml-1 px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs">
                                        🎯 Premium: {{ 6 }} slotów
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Flash Messages -->
                        @if (session()->has('error'))
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                                {{ session('error') }}
                                @if(!$isPremium && str_contains(session('error'), 'Premium'))
                                    <div class="mt-2">
                                        <a href="#" class="text-red-800 underline font-medium hover:text-red-900">
                                            Dowiedz się więcej o Premium →
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <form wire:submit.prevent="saveAvailability" class="space-y-4">
                            <!-- Time Slot Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Rodzaj slotu
                                    <span class="text-xs text-gray-500 ml-1">(wybierz szablon lub ustaw własny)</span>
                                </label>
                                <select wire:model.live="time_slot" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    @foreach($this->time_slot_options as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('time_slot') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Time Range -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Od godziny
                                        @if($time_slot !== 'custom')
                                            <span class="text-xs text-gray-500">(można zmienić)</span>
                                        @endif
                                    </label>
                                    <input
                                        type="time"
                                        wire:model="start_time"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        step="900"
                                    >
                                    @error('start_time') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Do godziny
                                        @if($time_slot !== 'custom')
                                            <span class="text-xs text-gray-500">(można zmienić)</span>
                                        @endif
                                    </label>
                                    <input
                                        type="time"
                                        wire:model="end_time"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        step="900"
                                    >
                                    @error('end_time') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
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
                                    Jestem dostępny w tym slocie
                                </label>
                            </div>

                            @if($is_available)
                                {{-- Service Selection --}}
                                @if($this->user_services->isNotEmpty())
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Dostępne usługi w tym slocie</label>
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach($this->user_services as $service)
                                                <label class="flex items-center space-x-2 p-2 border rounded hover:bg-gray-50">
                                                    <input
                                                        type="checkbox"
                                                        wire:model="selected_services"
                                                        value="{{ $service->id }}"
                                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    >
                                                    <div class="flex items-center space-x-2 flex-1">
                                                        <span class="text-sm">{{ $service->title }}</span>

                                                        {{-- Service Type Labels --}}
                                                        @if($service->home_service && $service->sitter_home)
                                                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 text-xs rounded">
                                                                🏡 U klienta
                                                            </span>
                                                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 text-xs rounded">
                                                                🏠 U opiekuna
                                                            </span>
                                                        @elseif($service->home_service)
                                                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 text-xs rounded">
                                                                🏡 U klienta
                                                            </span>
                                                        @elseif($service->sitter_home)
                                                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 text-xs rounded">
                                                                🏠 U opiekuna
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300 text-xs rounded">
                                                                🌍 Uniwersalna
                                                            </span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Pozostaw puste dla wszystkich usług</p>
                                    </div>
                                @endif
                            @endif

                            <!-- Recurring Options -->
                            @if(!$editingAvailability)
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
                                        <div class="pl-6 space-y-4">
                                            <div>
                                                <p class="text-sm text-gray-600 mb-2">Wybierz dni tygodnia:</p>
                                                <div class="grid grid-cols-2 gap-2">
                                                    @php
                                                        $days = [
                                                            1 => 'Poniedziałek', 2 => 'Wtorek', 3 => 'Środa', 4 => 'Czwartek',
                                                            5 => 'Piątek', 6 => 'Sobota', 0 => 'Niedziela'
                                                        ];
                                                    @endphp
                                                    @foreach($days as $dayNum => $dayName)
                                                        <label class="flex items-center space-x-2">
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

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Powtarzaj przez (tygodni)
                                                        @php
                                                            $maxWeeks = $this->getRecurringMaxWeeks();
                                                            $subscription = auth()->user()->currentSubscriptionPlan();
                                                        @endphp
                                                        @if($maxWeeks < 52)
                                                            <span class="text-xs text-gray-500 ml-1">
                                                                (max {{ $maxWeeks }} tyg. - {{ $subscription->name ?? 'Basic' }})
                                                            </span>
                                                        @endif
                                                    </label>
                                                    <select wire:model="recurring_weeks" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                                        <option value="1">1 tydzień</option>
                                                        <option value="2">2 tygodnie</option>
                                                        <option value="4" {{ $maxWeeks >= 4 ? '' : 'disabled' }}>4 tygodnie (miesiąc)</option>
                                                        @if($maxWeeks >= 8)
                                                            <option value="8">8 tygodni (2 miesiące)</option>
                                                        @else
                                                            <option value="8" disabled>8 tygodni (2 miesiące) - wymagany Premium</option>
                                                        @endif
                                                        @if($maxWeeks >= 12)
                                                            <option value="12">12 tygodni (3 miesiące)</option>
                                                        @else
                                                            <option value="12" disabled>12 tygodni (3 miesiące) - wymagany Premium</option>
                                                        @endif
                                                        @if($maxWeeks >= 24)
                                                            <option value="24">24 tygodnie (6 miesięcy)</option>
                                                        @else
                                                            <option value="24" disabled>24 tygodnie (6 miesięcy) - wymagany Pro</option>
                                                        @endif
                                                        @if($maxWeeks >= 52)
                                                            <option value="52">52 tygodnie (rok)</option>
                                                        @else
                                                            <option value="52" disabled>52 tygodnie (rok) - wymagany Pro</option>
                                                        @endif
                                                    </select>
                                                    @error('recurring_weeks') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Data końcowa (opcjonalne)
                                                        @php
                                                            $maxDate = now()->addWeeks($maxWeeks)->format('Y-m-d');
                                                        @endphp
                                                        @if($maxWeeks < 52)
                                                            <span class="text-xs text-gray-500 ml-1">
                                                                (max do {{ now()->addWeeks($maxWeeks)->format('d.m.Y') }})
                                                            </span>
                                                        @endif
                                                    </label>
                                                    <input
                                                        type="date"
                                                        wire:model="recurring_end_date"
                                                        min="{{ $date }}"
                                                        max="{{ $maxDate }}"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                                    >
                                                    @error('recurring_end_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <div class="text-xs text-gray-500 bg-blue-50 p-2 rounded">
                                                    💡 Jeśli podasz datę końcową, ma ona priorytet nad liczbą tygodni.
                                                </div>
                                                @if($maxWeeks < 52)
                                                    <div class="text-xs text-orange-700 bg-orange-50 p-2 rounded border border-orange-200">
                                                        ⚡ Plan {{ $subscription->name ?? 'Basic' }}: maksymalnie {{ $maxWeeks }} tygodni powtarzania.
                                                        @if($maxWeeks < 8)
                                                            Przejdź na Premium dla 8 tygodni lub Pro dla roku!
                                                        @elseif($maxWeeks < 52)
                                                            Przejdź na Pro dla roku powtarzania!
                                                        @endif
                                                    </div>
                                                @endif
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
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Dodatkowe informacje o dostępności..."
                                ></textarea>
                                @error('notes') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-4 border-t">
                                <div class="flex space-x-3">
                                    @if(!$editingAvailability)
                                        <button
                                            type="button"
                                            wire:click="addNewSlot"
                                            class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                        >
                                            Resetuj formularz
                                        </button>
                                    @endif
                                </div>

                                <div class="flex space-x-3">
                                    <button
                                        type="button"
                                        wire:click="closeModal"
                                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                    >
                                        Zamknij
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                                    >
                                        {{ $editingAvailability ? 'Zapisz zmiany' : 'Dodaj slot' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div> <!-- End scrollable content -->
                </div> <!-- End flex-1 overflow-y-auto -->
            </div> <!-- End bg-white rounded-xl -->
        </div> <!-- End fixed inset-0 -->
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
