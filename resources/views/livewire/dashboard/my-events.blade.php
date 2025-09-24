<!-- My Events Dashboard Component -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Moje eventy</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @php
                            try {
                                $eventsCount = $this->events->count();
                                echo $eventsCount . ' ' . ($eventsCount === 1 ? 'event' : 'eventów');
                            } catch (Exception $e) {
                                echo '0 eventów';
                            }
                        @endphp
                    </p>
                </div>
            </div>
            <a href="{{ route('events.index') }}"
               class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                Zobacz wszystkie
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        @php
            try {
                $events = $this->events;
                $eventsCount = $events->count();
            } catch (Exception $e) {
                $events = collect();
                $eventsCount = 0;
            }
        @endphp

        @if($eventsCount > 0)
                <div class="space-y-4">
                    @foreach($this->events as $event)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center flex-1">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-lg">📅</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $event->title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $event->event_date->format('d.m.Y H:i') }}
                                    </p>
                                    @if($event->location)
                                        <p class="text-xs text-purple-600 dark:text-purple-400">
                                            {{ $event->location }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                                    Event
                                </span>
                                <a href="{{ route('events.edit', $event) }}"
                                   class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400"
                                   title="Edytuj">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Add New Event Button -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <a href="{{ route('events.create') }}"
                       class="flex items-center justify-center w-full p-3 text-sm font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dodaj nowy event
                    </a>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Brak eventów</h4>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Nie masz jeszcze żadnych zaplanowanych eventów. Dodaj swój pierwszy event.
                    </p>
                    <a href="{{ route('events.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dodaj pierwszy event
                    </a>
                </div>
            @endif
    </div>
</div>
