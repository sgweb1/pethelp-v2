<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Wybierz plan idealny dla Ciebie
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    Rozpocznij z planem bezpłatnym lub wybierz premium, aby odblokować wszystkie funkcje platformy PetHelp
                </p>
            </div>

            @auth
                @if($currentSubscription)
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-icon name="heroicon-s-information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" />
                            <div>
                                <p class="text-blue-800 dark:text-blue-200 font-medium">
                                    Aktualnie korzystasz z planu: <strong>{{ $currentSubscription->subscriptionPlan->name }}</strong>
                                </p>
                                <p class="text-blue-600 dark:text-blue-400 text-sm">
                                    Plan wygasa: {{ $currentSubscription->ends_at->format('d.m.Y') }}
                                    ({{ $currentSubscription->days_remaining }} dni pozostało)
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Pricing Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                @foreach($plans as $plan)
                    <x-subscription.pricing-card
                        :plan="$plan"
                        :is-popular="$plan->is_popular"
                        :current-plan="$currentSubscription && $currentSubscription->subscriptionPlan->id === $plan->id"
                    />
                @endforeach
            </div>

            <!-- Features Comparison -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Porównanie funkcji</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Funkcja
                                </th>
                                @foreach($plans as $plan)
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ $plan->name }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            <!-- Max Listings -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                    Liczba ogłoszeń
                                </td>
                                @foreach($plans as $plan)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                        {{ $plan->max_listings ?: 'Bez limitów' }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Features -->
                            @php
                                $allFeatures = [
                                    'basic_search' => 'Podstawowe wyszukiwanie',
                                    'advanced_search' => 'Zaawansowane wyszukiwanie',
                                    'messaging' => 'System wiadomości',
                                    'reviews' => 'Opinie i oceny',
                                    'analytics' => 'Analityka i statystyki',
                                    'verified_badge' => 'Badge "Zweryfikowany"',
                                    'ai_matching' => 'AI-powered matching',
                                    'promoted_listings' => 'Promowane ogłoszenia',
                                    'priority_support' => 'Priorytetowe wsparcie',
                                    'api_access' => 'Dostęp do API',
                                ];
                            @endphp

                            @foreach($allFeatures as $feature => $label)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                        {{ $label }}
                                    </td>
                                    @foreach($plans as $plan)
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($plan->hasFeature($feature))
                                                <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mx-auto" />
                                            @else
                                                <x-icon name="heroicon-s-x-mark" class="w-5 h-5 text-gray-300 mx-auto" />
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mt-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-8">
                    Często zadawane pytania
                </h2>
                <div class="max-w-3xl mx-auto">
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                Czy mogę zmienić plan w każdej chwili?
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Tak, możesz w każdej chwili zaktualizować lub zmienić swój plan. Zmiany wchodzą w życie natychmiast, a rozliczenia są proporcjonalne.
                            </p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                Czy istnieje darmowy okres próbny?
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Plan podstawowy jest darmowy na zawsze. Możesz rozpocząć z nim i w każdej chwili przejść na plan premium.
                            </p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                Jak anulować subskrypcję?
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Możesz anulować subskrypcję w każdej chwili z poziomu panelu zarządzania kontem. Zachowasz dostęp do funkcji premium do końca opłaconego okresu.
                            </p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                Czy ceny zawierają VAT?
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Tak, wszystkie podane ceny zawierają polski VAT w wysokości 23%.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
