@php
    $colorClasses = [
        'blue' => [
            'bg' => 'bg-blue-50 dark:bg-blue-900/20',
            'text' => 'text-blue-600 dark:text-blue-400',
            'icon' => 'bg-blue-100 dark:bg-blue-800'
        ],
        'green' => [
            'bg' => 'bg-green-50 dark:bg-green-900/20',
            'text' => 'text-green-600 dark:text-green-400',
            'icon' => 'bg-green-100 dark:bg-green-800'
        ],
        'purple' => [
            'bg' => 'bg-purple-50 dark:bg-purple-900/20',
            'text' => 'text-purple-600 dark:text-purple-400',
            'icon' => 'bg-purple-100 dark:bg-purple-800'
        ],
        'yellow' => [
            'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
            'text' => 'text-yellow-600 dark:text-yellow-400',
            'icon' => 'bg-yellow-100 dark:bg-yellow-800'
        ]
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($this->stats as $stat)
        @php
            $colors = $colorClasses[$stat['color']] ?? $colorClasses['blue'];
        @endphp
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="{{ $colors['icon'] }} rounded-lg p-3">
                            <span class="text-2xl">{{ $stat['icon'] }}</span>
                        </div>
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold {{ $colors['text'] }} truncate">
                                {{ $stat['value'] }}
                            </p>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $stat['title'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $stat['description'] }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Optional trend indicator -->
            <div class="{{ $colors['bg'] }} px-6 py-3">
                <div class="flex items-center text-sm {{ $colors['text'] }}">
                    <svg class="flex-shrink-0 h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Aktualne dane</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
