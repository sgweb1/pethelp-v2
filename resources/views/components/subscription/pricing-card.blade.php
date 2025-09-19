@props([
    'plan',
    'isPopular' => false,
    'currentPlan' => false
])

<div class="relative bg-white dark:bg-gray-800 rounded-lg border {{ $isPopular ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700' }} p-6 shadow-lg">
    @if($isPopular)
        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
            <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                Najpopularniejszy
            </span>
        </div>
    @endif

    @if($currentPlan)
        <div class="absolute -top-3 right-4">
            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                Aktualny plan
            </span>
        </div>
    @endif

    <div class="text-center">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $plan->description }}</p>

        <div class="mt-4">
            <span class="text-4xl font-bold text-gray-900 dark:text-white">{{ $plan->formatted_price }}</span>
            <span class="text-gray-600 dark:text-gray-400">/{{ $plan->billing_period === 'yearly' ? 'rok' : 'miesiąc' }}</span>
        </div>

        @if($plan->billing_period === 'yearly' && $plan->price > 0)
            <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                Oszczędzasz {{ number_format(($plan->price / 12) * 12 * 0.17, 2, ',', ' ') }} PLN rocznie
            </p>
        @endif
    </div>

    <div class="mt-6">
        <ul class="space-y-3">
            @if($plan->max_listings)
                <li class="flex items-center">
                    <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                    <span class="text-gray-700 dark:text-gray-300">{{ $plan->max_listings }} ogłoszeń</span>
                </li>
            @else
                <li class="flex items-center">
                    <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                    <span class="text-gray-700 dark:text-gray-300">Nielimitowane ogłoszenia</span>
                </li>
            @endif

            @foreach($plan->feature_list as $feature => $label)
                <li class="flex items-center">
                    <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                    <span class="text-gray-700 dark:text-gray-300">{{ $label }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mt-8">
        @if($currentPlan)
            <button disabled class="w-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 py-3 px-4 rounded-lg font-medium">
                Aktualny plan
            </button>
        @elseif($plan->price == 0)
            <form method="POST" action="{{ route('subscription.subscribe', $plan) }}" class="w-full">
                @csrf
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                    Wybierz plan podstawowy
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('subscription.subscribe', $plan) }}" class="w-full">
                @csrf
                <button type="submit" class="w-full {{ $isPopular ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-900 hover:bg-gray-800' }} text-white py-3 px-4 rounded-lg font-medium transition-colors">
                    Wybierz {{ $plan->name }}
                </button>
            </form>
        @endif
    </div>

    @if($plan->price > 0)
        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3">
            Wszystkie ceny zawierają VAT
        </p>
    @endif
</div>