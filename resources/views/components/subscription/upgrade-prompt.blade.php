@props([
    'feature' => null,
    'title' => 'Aktualizuj do Premium',
    'description' => 'Ta funkcja jest dostępna tylko w planach premium.',
    'showPlans' => true
])

<div {{ $attributes->merge(['class' => 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6']) }}>
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <x-icon name="heroicon-s-star" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
        </div>
        <div class="ml-4 flex-1">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $title }}</h3>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $description }}</p>

            @if($feature)
                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        Wymagana funkcja: {{ $feature }}
                    </span>
                </div>
            @endif

            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('subscription.plans') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <x-icon name="heroicon-s-arrow-up" class="w-4 h-4 mr-2" />
                    Zobacz plany
                </a>

                @auth
                    @if(auth()->user()->hasActiveSubscription())
                        <a href="{{ route('subscription.dashboard') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                            <x-icon name="heroicon-s-cog" class="w-4 h-4 mr-2" />
                            Zarządzaj subskrypcją
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    @if($showPlans)
        <div class="mt-6 pt-6 border-t border-blue-200 dark:border-blue-700">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Popularne plany:</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <h5 class="font-medium text-gray-900 dark:text-white">Pro</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">49 PLN<span class="text-sm text-gray-500">/mies</span></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Nielimitowane ogłoszenia</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-500 ring-1 ring-blue-500">
                    <h5 class="font-medium text-gray-900 dark:text-white">Premium</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">99 PLN<span class="text-sm text-gray-500">/mies</span></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">AI matching + analytics</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <h5 class="font-medium text-gray-900 dark:text-white">Business</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">199 PLN<span class="text-sm text-gray-500">/mies</span></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">API + integracje</p>
                </div>
            </div>
        </div>
    @endif
</div>