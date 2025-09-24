<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <!-- Calendar Header -->
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kalendarz tygodnia</h3>
            <div class="flex items-center space-x-2">
                <button wire:click="previousWeek"
                        class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button wire:click="goToToday"
                        class="px-3 py-1 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors duration-200">
                    Dziś
                </button>
                <button wire:click="nextWeek"
                        class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Week Range -->
        <div class="mb-4 text-center">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $currentWeek->translatedFormat('j F') }} - {{ $currentWeek->copy()->endOfWeek()->translatedFormat('j F Y') }}
            </span>
        </div>

        <!-- Week Calendar Grid -->
        <div class="grid grid-cols-7 gap-1 mb-4">
            @foreach($weekDays as $day)
                <div class="text-center">
                    <!-- Day header -->
                    <div class="py-2">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            {{ $day['dayShort'] }}
                        </div>
                        <div class="mt-1 {{ $day['isToday'] ? 'bg-primary-600 text-white' : 'text-gray-900 dark:text-white' }} w-8 h-8 mx-auto rounded-full flex items-center justify-center text-sm font-medium">
                            {{ $day['dayNumber'] }}
                        </div>
                    </div>

                    <!-- Events for this day -->
                    <div class="space-y-1 mt-2">
                        @if(count($day['events']) > 0)
                            @foreach(array_slice($day['events'], 0, 2) as $event)
                                <div class="text-xs p-1 bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-200 rounded border-l-2 border-primary-500">
                                    <div class="font-medium truncate">{{ $event['time'] }}</div>
                                    <div class="truncate">{{ $event['pet'] }}</div>
                                </div>
                            @endforeach
                            @if(count($day['events']) > 2)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    +{{ count($day['events']) - 2 }} więcej
                                </div>
                            @endif
                        @else
                            <div class="text-xs text-gray-400 dark:text-gray-500 py-2">
                                Wolny dzień
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Calendar Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                @php
                    $todayEvents = collect($weekDays)->firstWhere('isToday', true)['events'] ?? [];
                @endphp
                {{ count($todayEvents) }} wydarzeń dzisiaj
            </span>
            <a href="{{ route('availability.calendar') }}"
               class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">
                Zobacz pełny kalendarz →
            </a>
        </div>
    </div>
</div>
