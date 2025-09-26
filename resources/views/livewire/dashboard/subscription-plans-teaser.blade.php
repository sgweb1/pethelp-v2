<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Plany subskrypcji</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Rozszerz możliwości swojego konta
            </p>
        </div>

        @if($currentPlan && $currentPlan->slug !== 'basic')
            <button
                wire:click="toggleShowAll"
                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium"
            >
                {{ $showAll ? 'Pokaż tylko upgrade' : 'Pokaż wszystkie plany' }}
            </button>
        @endif
    </div>

    <!-- Current Plan Display -->
    @if($currentPlan)
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                </div>
                <div>
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                        Aktualny plan: {{ $currentPlan->name }}
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-300">
                        {{ $currentPlan->description }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Available Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($availablePlans as $plan)
            <div class="relative border border-gray-200 dark:border-gray-700 rounded-lg p-5
                     {{ $plan->slug === 'pro' ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'bg-white dark:bg-gray-800' }}">

                <!-- Recommended Badge -->
                @if($recommendedPlan && $plan->id === $recommendedPlan->id)
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                        <span class="px-3 py-1 text-xs font-medium text-white bg-blue-500 rounded-full">
                            Rekomendowany
                        </span>
                    </div>
                @endif

                <div class="text-center">
                    <!-- Plan Name -->
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        {{ $plan->name }}
                    </h4>

                    <!-- Price -->
                    <div class="mb-4">
                        @if($plan->price == 0)
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">Darmowy</span>
                        @else
                            <div class="flex items-baseline justify-center">
                                <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($plan->price, 0) }} PLN
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">/miesiąc</span>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ $plan->description }}
                    </p>

                    <!-- Features -->
                    @if($plan->features)
                        <ul class="text-sm text-left space-y-2 mb-6">
                            @foreach($plan->features as $feature)
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <!-- Action Button -->
                    @if($hasCurrentPlan($plan->slug))
                        <button disabled class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-lg text-sm font-medium cursor-not-allowed">
                            Aktualny plan
                        </button>
                    @else
                        <button
                            wire:click="selectPlan('{{ $plan->slug }}')"
                            class="w-full px-4 py-2
                                   {{ $plan->slug === 'pro' ?
                                      'bg-blue-600 hover:bg-blue-700 text-white' :
                                      'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white'
                                   }}
                                   rounded-lg text-sm font-medium transition-colors duration-200"
                        >
                            @if($currentPlan && $plan->price > $currentPlan->price)
                                Przejdź na {{ $plan->name }}
                            @else
                                Wybierz plan
                            @endif
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-8">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-lg font-medium mb-2">Brak dostępnych planów</p>
                    <p class="text-sm">
                        @if(!$showAll && $currentPlan)
                            Masz już najwyższy dostępny plan lub nie ma planów wyższych.
                        @else
                            Obecnie nie ma dostępnych planów subskrypcji.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Benefits Footer -->
    @if($availablePlans->isNotEmpty())
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-center text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Bezpieczne płatności • Możliwość anulowania w każdej chwili • 14 dni na zwrot
            </div>
        </div>
    @endif

    <!-- Become Sitter CTA -->
    @if($canBecomeSitter && (!$currentPlan || $currentPlan->slug === 'basic'))
        <div class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-purple-900 dark:text-purple-100">
                        Chcesz zostać opiekunem?
                    </h4>
                    <p class="text-xs text-purple-600 dark:text-purple-300 mt-1">
                        Wybierz plan Pro lub Premium aby oferować swoje usługi
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    @endif
</div>
