@props([
    'plan',
    'isPopular' => false,
    'currentPlan' => false,
    'availablePlans' => collect()
])

<div class="relative bg-white dark:bg-gray-800 rounded-lg border {{ $isPopular ? 'border-blue-500 ring-2 ring-blue-500' : ($plan->billing_period === 'yearly' ? 'border-green-300 dark:border-green-600 ring-1 ring-green-300 dark:ring-green-600' : 'border-gray-200 dark:border-gray-700') }} p-6 shadow-lg">
    @if($isPopular)
        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
            <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                Najpopularniejszy
            </span>
        </div>
    @elseif($plan->billing_period === 'yearly' && $plan->price > 0 && isset($plan->monthlyVariant))
        @php
            $monthlyPlan = $plan->monthlyVariant;
            if ($monthlyPlan) {
                $yearlyPriceIfMonthly = $monthlyPlan->price * 12;
                $savings = $yearlyPriceIfMonthly - $plan->price;
                $discountPercent = round(($savings / $yearlyPriceIfMonthly) * 100);
            } else {
                $discountPercent = 0;
            }
        @endphp
        @if($discountPercent > 0)
            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                    -{{ $discountPercent }}% RABATU
                </span>
            </div>
        @endif
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
            @if(isset($plan->pricing) && $plan->pricing['has_proration'])
                {{-- Cena z proration - pokaż oszczędności --}}
                <div class="space-y-2">
                    <div class="text-sm text-gray-500 dark:text-gray-400 line-through">
                        {{ number_format($plan->pricing['original_price'], 2, ',', ' ') }} PLN
                    </div>
                    <div class="flex items-baseline justify-center space-x-2">
                        <span class="text-4xl font-bold text-green-600 dark:text-green-400">{{ number_format($plan->pricing['final_price'], 2, ',', ' ') }} PLN</span>
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                            (-{{ number_format($plan->pricing['savings'], 2, ',', ' ') }} PLN)
                        </span>
                    </div>
                    <div class="text-xs text-blue-600 dark:text-blue-400">
                        Odliczenie z obecnego planu: {{ number_format($plan->pricing['credit_amount'], 2, ',', ' ') }} PLN
                    </div>
                </div>
            @else
                {{-- Standardowa cena --}}
                <span class="text-4xl font-bold text-gray-900 dark:text-white">{{ $plan->formatted_price }}</span>
            @endif
            <span class="text-gray-600 dark:text-gray-400">/{{ $plan->billing_period === 'yearly' ? 'rok' : 'miesiąc' }}</span>
        </div>

        @if($plan->billing_period === 'yearly' && $plan->price > 0 && isset($plan->monthlyVariant))
            @php
                $monthlyPlan = $plan->monthlyVariant;
                if ($monthlyPlan) {
                    $yearlyPriceIfMonthly = $monthlyPlan->price * 12;
                    $savings = $yearlyPriceIfMonthly - $plan->price;
                    $discountPercent = round(($savings / $yearlyPriceIfMonthly) * 100);
                } else {
                    $savings = 0;
                    $discountPercent = 0;
                }
            @endphp
            @if($savings > 0)
                <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                    Oszczędzasz {{ number_format($savings, 2, ',', ' ') }} PLN rocznie ({{ $discountPercent }}% rabatu)
                </p>
            @endif
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
            <a href="{{ route('subscription.checkout', $plan) }}" class="block w-full {{ $isPopular ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-900 hover:bg-gray-800' }} text-white py-3 px-4 rounded-lg font-medium transition-colors text-center">
                Wybierz {{ $plan->name }}
            </a>
        @endif

        {{-- Informacja o oszczędnościach dla planów miesięcznych --}}
        @if($plan->billing_period === 'monthly' && $plan->price > 0 && isset($plan->yearlyVariant))
            @php
                $yearlyPlan = $plan->yearlyVariant;
                if ($yearlyPlan) {
                    $yearlyPriceIfMonthly = $plan->price * 12;
                    $savings = $yearlyPriceIfMonthly - $yearlyPlan->price;
                    $discountPercent = round(($savings / $yearlyPriceIfMonthly) * 100);
                } else {
                    $savings = 0;
                    $discountPercent = 0;
                }
            @endphp
            @if($savings > 0)
                <div class="mt-3 text-center">
                    <button
                        wire:click="setBillingPeriod('yearly')"
                        class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 transition-colors"
                    >
                        💰 Oszczędź {{ number_format($savings, 0, ',', ' ') }} PLN rocznie ({{ $discountPercent }}% rabatu)
                    </button>
                </div>
            @endif
        @endif
    </div>

    @if($plan->price > 0)
        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3">
            Wszystkie ceny zawierają VAT
        </p>
    @endif
</div>