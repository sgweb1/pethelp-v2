<x-dashboard-layout title="Mój Profil" active-section="profile">
    <div class="space-y-6">
        <!-- Profile Header Banner -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold mb-1">
                            {{ auth()->user()->name }}
                        </h2>
                        <p class="text-blue-100">
                            {{ auth()->user()->email }}
                        </p>
                        <div class="flex items-center mt-2">
                            @if(auth()->user()->isOwner() && auth()->user()->isSitter())
                                <span class="bg-white/20 text-xs px-2 py-1 rounded-full mr-2">Właściciel & Opiekun</span>
                            @elseif(auth()->user()->isOwner())
                                <span class="bg-white/20 text-xs px-2 py-1 rounded-full mr-2">Właściciel zwierząt</span>
                            @elseif(auth()->user()->isSitter())
                                <span class="bg-white/20 text-xs px-2 py-1 rounded-full mr-2">Opiekun zwierząt</span>
                            @endif
                            @if(auth()->user()->email_verified_at)
                                <span class="bg-green-500/20 text-xs px-2 py-1 rounded-full flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Zweryfikowany
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <div class="text-4xl">👤</div>
                </div>
            </div>
        </div>

        <!-- Warning Alert if profile incomplete -->
        @if(!auth()->user()->profile || !auth()->user()->profile->is_complete)
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-orange-800">
                            Twój profil wymaga uzupełnienia. Wypełnij wszystkie wymagane informacje w zakładce
                            <span class="font-semibold">Dane personalne</span>.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div x-data="{
                activeTab: window.location.hash ? window.location.hash.substring(1) : 'personal-data',
                init() {
                    // Listen for hash changes
                    window.addEventListener('hashchange', () => {
                        this.activeTab = window.location.hash ? window.location.hash.substring(1) : 'personal-data';
                    });
                }
            }" class="w-full">
                <!-- Tab Headers -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button @click="activeTab = 'personal-data'; window.location.hash = 'personal-data'"
                                :class="activeTab === 'personal-data' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Dane personalne
                        </button>

                        {{-- @if(auth()->user()->isSitter())
                            <button @click="activeTab = 'payout-settings'; window.location.hash = 'payout-settings'"
                                    :class="activeTab === 'payout-settings' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                Ustawienia wypłat
                            </button>
                        @endif --}}

                        <button @click="activeTab = 'account-management'; window.location.hash = 'account-management'"
                                :class="activeTab === 'account-management' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Zarządzanie kontem
                        </button>

                        <button @click="activeTab = 'notifications'; window.location.hash = 'notifications'"
                                :class="activeTab === 'notifications' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Powiadomienia
                        </button>

                        {{-- @if(auth()->user()->isSitter())
                            <button @click="activeTab = 'earnings-report'; window.location.hash = 'earnings-report'"
                                    :class="activeTab === 'earnings-report' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                Raportowanie zarobków
                            </button>
                        @endif --}}
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Personal Data Tab -->
                    <div x-show="activeTab === 'personal-data'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="space-y-8">
                            <!-- Profile Information Section -->
                            <div>
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informacje osobiste</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Zaktualizuj swoje dane kontaktowe</p>
                                    </div>
                                </div>
                                <livewire:profile.update-profile-information-form />
                            </div>

                            <!-- Additional Profile Fields -->
                            <div>
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dodatkowe informacje</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Uzupełnij swój profil</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Numer telefonu
                                        </label>
                                        <input type="tel"
                                               value="{{ auth()->user()->profile->phone ?? '' }}"
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="+48 123 456 789">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Data urodzenia
                                        </label>
                                        <input type="date"
                                               value="{{ auth()->user()->profile->birth_date ?? '' }}"
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            O mnie
                                        </label>
                                        <textarea rows="4"
                                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                                  placeholder="Opowiedz coś o sobie...">{{ auth()->user()->profile->bio ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <!-- Payout Settings Tab -->
                    @if(auth()->user()->isSitter())
                        <div x-show="activeTab === 'payout-settings'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="space-y-6">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ustawienia wypłat</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Zarządzaj metodami płatności</p>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Numer konta bankowego</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                IBAN
                                            </label>
                                            <input type="text"
                                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="PL00 0000 0000 0000 0000 0000 0000">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Nazwa banku
                                            </label>
                                            <input type="text"
                                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="Nazwa banku">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif --}}

                    <!-- Account Management Tab -->
                    <div x-show="activeTab === 'account-management'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="space-y-8">
                            <!-- Password Change Section -->
                            <div>
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Zmiana hasła</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Zapewnij bezpieczeństwo konta silnym hasłem</p>
                                    </div>
                                </div>
                                <livewire:profile.update-password-form />
                            </div>

                            <!-- Account Statistics -->
                            <div>
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Statystyki konta</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Informacje o Twoim koncie</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Data rejestracji</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ auth()->user()->created_at->format('d.m.Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Ostatnie logowanie</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ auth()->user()->updated_at->format('d.m.Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    @if(auth()->user()->isOwner())
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Zwierzęta</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ auth()->user()->pets()->count() }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                    @if(auth()->user()->isSitter())
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Aktywne usługi</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ auth()->user()->services()->where('is_active', true)->count() }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Danger Zone -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-red-700 dark:text-red-400">Strefa niebezpieczna</h3>
                                        <p class="text-sm text-red-600 dark:text-red-500">Nieodwracalne akcje konta</p>
                                    </div>
                                </div>
                                <livewire:profile.delete-user-form />
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Tab -->
                    <div x-show="activeTab === 'notifications'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM21.121 15.536c-.617-.617-1.443-.955-2.316-.955-1.807 0-3.273 1.466-3.273 3.273 0 .873.338 1.699.955 2.316L21.121 15.536zm0 0L15 21.071 9 15.071l6.121-5.535 6 6z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ustawienia powiadomień</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Zarządzaj preferencjami powiadomień</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- Email Notifications -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-4">Powiadomienia email</h4>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nowe wiadomości</label>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Otrzymuj email gdy ktoś wyśle Ci wiadomość</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer" checked>
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nowe zlecenia</label>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Powiadom o nowych zleceniach w okolicy</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer" checked>
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Potwierdzenia płatności</label>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Email po otrzymaniu płatności</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer" checked>
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Push Notifications -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-4">Powiadomienia push</h4>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Wszystkie powiadomienia</label>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Ogólne ustawienie dla aplikacji mobilnej</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Marketing Notifications -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-4">Newsletter i promocje</h4>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Newsletter</label>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Otrzymuj cotygodniowy newsletter</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Promocje specjalne</label>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Informacje o promocjach i zniżkach</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <!-- Earnings Report Tab -->
                    @if(auth()->user()->isSitter())
                        <div x-show="activeTab === 'earnings-report'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="space-y-6">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Raportowanie zarobków</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Ustawienia dotyczące rozliczenia podatkowego</p>
                                    </div>
                                </div>

                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Informacja o podatkach</h4>
                                            <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                                Zarobki z usług pet-sitting mogą podlegać obowiązkowi podatkowemu.
                                                Skonsultuj się z księgowym lub urzędem skarbowym.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                NIP (jeśli posiadasz)
                                            </label>
                                            <input type="text"
                                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="0000000000">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                REGON (jeśli posiadasz)
                                            </label>
                                            <input type="text"
                                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="000000000">
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="font-medium text-gray-900 dark:text-white">Automatyczne raporty</h4>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Otrzymuj miesięczne zestawienia zarobków na email
                                        </p>
                                    </div>

                                    <div class="flex gap-3">
                                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                            Pobierz raport za bieżący rok
                                        </button>
                                        <button type="button" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-medium py-2 px-4 rounded-lg transition-colors">
                                            Historia raportów
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif --}}
                </div>

                <!-- Save Button -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 rounded-b-xl">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Zmiany są automatycznie zapisywane
                        </p>
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Zapisz wszystkie zmiany
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Sidebar -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3">
                <!-- Main content already above -->
            </div>

            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Szybkie akcje</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if(auth()->user()->isOwner())
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Zarządzaj pupilami</span>
                            </a>
                        @endif

                        @if(auth()->user()->isSitter())
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-green-700 dark:text-green-300">Moje usługi</span>
                            </a>
                        @endif

                        <a href="{{ route('dashboard') }}"
                           class="flex items-center p-3 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-purple-700 dark:text-purple-300">Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle profile update notifications
            window.addEventListener('profile-updated', event => {
                console.log('Profile updated:', event.detail);
            });
        });
    </script>
    @endpush
</x-dashboard-layout>