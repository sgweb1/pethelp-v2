@php
    $data = $this->completionData;
    $steps = $data['steps'];
    $percentage = $data['percentage'];
    $remaining = $data['remaining'];
@endphp

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ukończ swój profil</h3>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $data['completed'] }}/{{ $data['total'] }}
            </span>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Postęp ukończenia
                </span>
                <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
                    {{ $percentage }}%
                </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-2 rounded-full transition-all duration-300" 
                     style="width: {{ $percentage }}%"></div>
            </div>
        </div>

        @if($percentage < 100)
            <!-- Motivation Message -->
            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-primary-50 dark:from-blue-900/20 dark:to-primary-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100">
                            @if($percentage < 30)
                                Rozpocznij swoją przygodę z PetHelp!
                            @elseif($percentage < 70)
                                Jesteś na dobrej drodze!
                            @else
                                Prawie gotowe!
                            @endif
                        </h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                            @if($remaining === 1)
                                Pozostał tylko {{ $remaining }} krok do ukończenia profilu.
                            @else
                                Pozostały {{ $remaining }} kroki do ukończenia profilu.
                            @endif
                            Kompletny profil zwiększa zaufanie i przyciąga więcej użytkowników.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Completion Steps -->
        <div class="space-y-3">
            @foreach($steps as $step)
                @php
                    $priorityColors = [
                        'high' => $step['completed'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400',
                        'medium' => $step['completed'] ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'
                    ];
                    $priorityColor = $priorityColors[$step['priority']] ?? 'text-gray-600 dark:text-gray-400';
                @endphp
                
                <div class="flex items-center justify-between p-3 rounded-lg {{ $step['completed'] ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600' }} transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            @if($step['completed'])
                                <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-6 h-6 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm">{{ $step['icon'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium {{ $step['completed'] ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-white' }}">
                                {{ $step['title'] }}
                            </p>
                            <p class="text-xs {{ $step['completed'] ? 'text-green-700 dark:text-green-300' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $step['description'] }}
                            </p>
                        </div>
                    </div>
                    
                    @if(!$step['completed'])
                        <a href="{{ route($step['route']) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-600 text-white hover:bg-primary-700 transition-colors duration-200">
                            @if($step['priority'] === 'high')
                                Priorytet
                            @else
                                Uzupełnij
                            @endif
                            <svg class="ml-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            ✓ Gotowe
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

        @if($percentage === 100)
            <!-- Completion Celebration -->
            <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-2xl">🎉</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-green-900 dark:text-green-100">
                            Gratulacje! Profil jest kompletny
                        </h4>
                        <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                            Twój profil jest w pełni ukończony. Teraz możesz w pełni korzystać z wszystkich funkcji platformy.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
