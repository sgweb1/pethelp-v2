<!-- Advanced Pricing Structure Section -->
<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
        </svg>
        Zaawansowana struktura cenowa
    </h3>

    <!-- Basic Pricing Structure -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label for="price_structure" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Struktura cenowa
            </label>
            <select wire:model="price_structure"
                    id="price_structure"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                <option value="per_hour">Za godzinę</option>
                <option value="per_visit">Za wizytę</option>
                <option value="per_day">Za dzień</option>
                <option value="custom">Niestandardowa</option>
            </select>
            @error('price_structure') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Extended Pricing Options -->
        <div>
            <label for="price_per_visit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Cena za wizytę (zł)
            </label>
            <input type="number"
                   step="0.01"
                   min="0"
                   wire:model="price_per_visit"
                   id="price_per_visit"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                   placeholder="np. 50.00">
            @error('price_per_visit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="price_per_week" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Cena za tydzień (zł)
            </label>
            <input type="number"
                   step="0.01"
                   min="0"
                   wire:model="price_per_week"
                   id="price_per_week"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                   placeholder="np. 300.00">
            @error('price_per_week') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label for="price_per_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Cena za miesiąc (zł)
            </label>
            <input type="number"
                   step="0.01"
                   min="0"
                   wire:model="price_per_month"
                   id="price_per_month"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                   placeholder="np. 1000.00">
            @error('price_per_month') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- Surcharges Section -->
    <div class="border-t border-gray-200 dark:border-gray-600 pt-6 mb-6">
        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Dopłaty czasowe (%)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="weekend_surcharge_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Weekendy
                </label>
                <input type="number"
                       step="0.01"
                       min="0"
                       max="100"
                       wire:model="weekend_surcharge_percent"
                       id="weekend_surcharge_percent"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                       placeholder="np. 20">
                @error('weekend_surcharge_percent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="holiday_surcharge_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Święta
                </label>
                <input type="number"
                       step="0.01"
                       min="0"
                       max="100"
                       wire:model="holiday_surcharge_percent"
                       id="holiday_surcharge_percent"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                       placeholder="np. 30">
                @error('holiday_surcharge_percent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="early_morning_surcharge_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Wczesny ranek
                </label>
                <input type="number"
                       step="0.01"
                       min="0"
                       max="100"
                       wire:model="early_morning_surcharge_percent"
                       id="early_morning_surcharge_percent"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                       placeholder="np. 15">
                @error('early_morning_surcharge_percent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="late_evening_surcharge_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Późny wieczór
                </label>
                <input type="number"
                       step="0.01"
                       min="0"
                       max="100"
                       wire:model="late_evening_surcharge_percent"
                       id="late_evening_surcharge_percent"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                       placeholder="np. 25">
                @error('late_evening_surcharge_percent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <!-- Discounts Section -->
    <div class="border-t border-gray-200 dark:border-gray-600 pt-6 mb-6">
        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Rabaty i promocje</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Bulk Discount -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Rabat za większe zlecenia</h5>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="bulk_discount_threshold" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Próg kwotowy (zł)
                        </label>
                        <input type="number"
                               step="0.01"
                               min="0"
                               wire:model="bulk_discount_threshold"
                               id="bulk_discount_threshold"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                               placeholder="np. 500">
                        @error('bulk_discount_threshold') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="bulk_discount_percent" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Rabat (%)
                        </label>
                        <input type="number"
                               step="0.01"
                               min="0"
                               max="50"
                               wire:model="bulk_discount_percent"
                               id="bulk_discount_percent"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                               placeholder="np. 10">
                        @error('bulk_discount_percent') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Long-term Discount -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Rabat długoterminowy</h5>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="long_term_discount_days" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Min. dni
                        </label>
                        <input type="number"
                               min="1"
                               max="365"
                               wire:model="long_term_discount_days"
                               id="long_term_discount_days"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                               placeholder="np. 7">
                        @error('long_term_discount_days') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="long_term_discount_percent" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Rabat (%)
                        </label>
                        <input type="number"
                               step="0.01"
                               min="0"
                               max="50"
                               wire:model="long_term_discount_percent"
                               id="long_term_discount_percent"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                               placeholder="np. 15">
                        @error('long_term_discount_percent') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Special Offers -->
    <div class="border-t border-gray-200 dark:border-gray-600 pt-6 mb-6">
        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Specjalne oferty</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center">
                <input type="checkbox"
                       wire:model="free_consultation"
                       id="free_consultation"
                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                <label for="free_consultation" class="ml-2 block text-sm text-gray-900 dark:text-white">
                    Bezpłatna konsultacja
                </label>
            </div>

            <div class="flex items-center">
                <input type="checkbox"
                       wire:model="free_trial_visit"
                       id="free_trial_visit"
                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                <label for="free_trial_visit" class="ml-2 block text-sm text-gray-900 dark:text-white">
                    Bezpłatna wizyta próbna
                </label>
            </div>
        </div>
    </div>

    <!-- Payment and Cancellation Policy -->
    <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Zasady płatności i anulowania</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Metody płatności
                </label>
                <select wire:model="payment_method"
                        id="payment_method"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                    <option value="cash">Tylko gotówka</option>
                    <option value="transfer">Tylko przelew</option>
                    <option value="both">Gotówka i przelew</option>
                </select>
                @error('payment_method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="cancellation_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Wyprzedzenie anulowania (h)
                </label>
                <input type="number"
                       min="1"
                       max="168"
                       wire:model="cancellation_hours"
                       id="cancellation_hours"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                       placeholder="np. 24">
                @error('cancellation_hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="cancellation_fee_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Opłata za anulowanie (%)
                </label>
                <input type="number"
                       step="0.01"
                       min="0"
                       max="100"
                       wire:model="cancellation_fee_percent"
                       id="cancellation_fee_percent"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                       placeholder="np. 50">
                @error('cancellation_fee_percent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>