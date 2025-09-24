@php
    $colorClasses = [
        'primary' => 'bg-primary-600 hover:bg-primary-700 text-white',
        'blue' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'green' => 'bg-green-600 hover:bg-green-700 text-white',
        'purple' => 'bg-purple-600 hover:bg-purple-700 text-white',
        'indigo' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
        'yellow' => 'bg-yellow-600 hover:bg-yellow-700 text-white',
        'gray' => 'bg-gray-600 hover:bg-gray-700 text-white'
    ];
@endphp

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Szybkie akcje</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($this->actions as $action)
                @php
                    $colorClass = $colorClasses[$action['color']] ?? $colorClasses['gray'];
                    $isFeatured = isset($action['featured']) && $action['featured'];
                @endphp
                
                <a href="{{ route($action['route']) }}" 
                   class="relative group block {{ $isFeatured ? 'sm:col-span-2 lg:col-span-1' : '' }}">
                    <div class="p-4 rounded-lg border-2 border-transparent group-hover:border-gray-200 dark:group-hover:border-gray-600 transition-colors duration-200 {{ $isFeatured ? 'bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20' : 'bg-gray-50 dark:bg-gray-700' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="text-2xl">{{ $action['icon'] }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $action['title'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $action['description'] }}
                                    </p>
                                </div>
                            </div>
                            
                            @if(isset($action['badge']) && $action['badge'] > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ $action['badge'] }}
                                </span>
                            @endif
                        </div>
                        
                        @if($isFeatured)
                            <div class="mt-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $colorClass }} transition-colors duration-200">
                                    Rozpocznij teraz
                                    <svg class="ml-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        
        <!-- Quick stats row -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                <span>Ostatnia aktywność: {{ now()->diffForHumans() }}</span>
                <a href="{{ route('dashboard') }}" class="text-primary-600 hover:text-primary-500 dark:text-primary-400">
                    Odśwież →
                </a>
            </div>
        </div>
    </div>
</div>
