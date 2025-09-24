<div>
    @if(!auth()->user()->isSitter())
        <!-- Become Pet Sitter Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg p-4 text-white mb-4">
                <div class="flex items-center">
                    <div class="text-3xl mr-3">🐕‍🦺</div>
                    <div>
                        <h3 class="text-lg font-semibold">Zostań Pet Sitterem!</h3>
                        <p class="text-green-100 text-sm">Zarabiaj opiekując się zwierzętami</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3 mb-4">
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Elastyczne godziny pracy</span>
                </div>
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Dodatkowy dochód</span>
                </div>
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Praca z pupilami</span>
                </div>
            </div>

            <button wire:click="openModal"
                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
                Rozpocznij aktywację
            </button>
        </div>
    @endif

    <!-- Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <!-- Overlay -->
                <div wire:click="closeModal" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

                <!-- Modal Content -->
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Zostań Pet Sitterem
                            </h3>
                            <button wire:click="closeModal"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Progress Steps -->
                        <div class="mt-4 flex items-center justify-between">
                            @for($i = 1; $i <= 4; $i++)
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full
                                                {{ $step >= $i ? 'bg-green-600 text-white' : 'bg-gray-300 text-gray-500' }}">
                                        {{ $i }}
                                    </div>
                                    @if($i < 4)
                                        <div class="w-16 h-1 mx-2 {{ $step > $i ? 'bg-green-600' : 'bg-gray-300' }}"></div>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6">
                        <!-- Step 1: Basic Info -->
                        @if($step === 1)
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        Krok 1: Podstawowe informacje
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                                        Opowiedz nam o swoim doświadczeniu ze zwierzętami
                                    </p>
                                </div>

                                <!-- Experience -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Opisz swoje doświadczenie z zwierzętami *
                                    </label>
                                    <textarea wire:model="experience"
                                              rows="4"
                                              class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
                                              placeholder="Opowiedz o swoim doświadczeniu w opiece nad zwierzętami, dlaczego chcesz zostać pet sitterem..."></textarea>
                                    @error('experience')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Pet Types -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Z jakimi zwierzętami masz doświadczenie? *
                                    </label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach(['dog' => 'Psy', 'cat' => 'Koty', 'rabbit' => 'Króliki', 'bird' => 'Ptaki', 'fish' => 'Ryby', 'other' => 'Inne'] as $value => $label)
                                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700
                                                        {{ in_array($value, $pets_experience) ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                                <input type="checkbox"
                                                       wire:model="pets_experience"
                                                       value="{{ $value }}"
                                                       class="mr-2 text-green-600 focus:ring-green-500">
                                                <span class="text-sm">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('pets_experience')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Service Radius -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        W jakim promieniu świadczysz usługi? (km) *
                                    </label>
                                    <input type="number"
                                           wire:model="service_radius"
                                           min="1"
                                           max="50"
                                           class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                    @error('service_radius')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Step 2: Services -->
                        @if($step === 2)
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        Krok 2: Wybierz usługi
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                                        Jakie usługi chcesz świadczyć?
                                    </p>
                                </div>

                                <div class="space-y-3">
                                    @foreach([
                                        'walking' => ['Spacery z psem', 'Regularne spacery z psami w okolicy', '🚶‍♂️'],
                                        'home_care' => ['Opieka w domu właściciela', 'Opieka nad zwierzęciem w domu klienta', '🏠'],
                                        'sitter_home' => ['Opieka u opiekuna', 'Opieka nad zwierzęciem w swoim domu', '🏡'],
                                        'feeding' => ['Karmienie', 'Wizyty w celu nakarmienia zwierzęcia', '🍽️'],
                                        'grooming' => ['Pielęgnacja', 'Strzyżenie, kąpanie, pielęgnacja', '✂️'],
                                    ] as $value => $info)
                                        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200
                                                    {{ in_array($value, $selected_services) ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                            <input type="checkbox"
                                                   wire:model="selected_services"
                                                   value="{{ $value }}"
                                                   class="mt-1 mr-3 text-green-600 focus:ring-green-500">
                                            <div class="flex-1">
                                                <div class="flex items-center">
                                                    <span class="text-2xl mr-2">{{ $info[2] }}</span>
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $info[0] }}</span>
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $info[1] }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selected_services')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- Step 3: Availability -->
                        @if($step === 3)
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        Krok 3: Dostępność
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                                        Kiedy możesz świadczyć usługi?
                                    </p>
                                </div>

                                <!-- Emergency Availability -->
                                <div>
                                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <input type="checkbox"
                                               wire:model="emergency_available"
                                               class="mr-3 text-green-600 focus:ring-green-500">
                                        <div>
                                            <span class="font-medium text-gray-900 dark:text-white">Dostępność w nagłych przypadkach</span>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                Mogę podjąć się opieki w nagłych sytuacjach
                                            </p>
                                        </div>
                                    </label>
                                </div>

                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <p class="text-sm text-blue-800 dark:text-blue-300">
                                        <strong>Wskazówka:</strong> Szczegółowy kalendarz dostępności będziesz mógł ustawić po aktywacji konta.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Step 4: Summary -->
                        @if($step === 4)
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        Krok 4: Podsumowanie
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                                        Sprawdź swoje dane przed aktywacją konta
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">Twoje doświadczenie:</h5>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $experience }}</p>
                                    </div>

                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">Wybrane usługi:</h5>
                                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                            @foreach($selected_services as $service)
                                                <li>• {{ ucfirst(str_replace('_', ' ', $service)) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <h5 class="font-medium text-green-800 dark:text-green-300 mb-2">Co dalej?</h5>
                                        <ul class="text-sm text-green-700 dark:text-green-400 space-y-1">
                                            <li>• Twoje konto Pet Sittera zostanie aktywowane</li>
                                            <li>• Będziesz mógł dodać szczegółowe opisy usług</li>
                                            <li>• Ustawisz własne ceny i dostępność</li>
                                            <li>• Twój profil pojawi się w wyszukiwarce</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <button wire:click="previousStep"
                                    @if($step === 1) disabled @endif
                                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Wstecz
                                </span>
                            </button>

                            @if($step < 4)
                                <button wire:click="nextStep"
                                        class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <span class="flex items-center">
                                        Dalej
                                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                </button>
                            @else
                                <button wire:click="activateSitterAccount"
                                        class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Aktywuj konto Pet Sittera
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>