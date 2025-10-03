@php
    $breadcrumbs = [
        [
            'title' => 'Panel',
            'url' => route('profile.dashboard'),
            'icon' => '🏠'
        ],
        [
            'title' => 'Mój Profil',
            'icon' => '👤'
        ]
    ];

    // Sprawdzamy status pet sittera
    $isSitter = auth()->user()->isSitter();
    $sitterActivated = auth()->user()->profile?->sitter_activated_at !== null;
    $wizardCompleted = $isSitter && $sitterActivated;
@endphp

<x-dashboard-layout title="Mój Profil" active-section="profile" :breadcrumbs="$breadcrumbs">
    <div class="space-y-8">
        {{-- Gradient Header Card --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 rounded-3xl shadow-2xl">
            <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-transparent via-white/5 to-white/10"></div>

            <div class="relative p-8">
                <div class="flex items-center justify-between flex-wrap gap-6">
                    <div class="flex items-center gap-6">
                        {{-- Avatar with gradient border --}}
                        <div class="relative">
                            <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border-2 border-white/30">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            @if(auth()->user()->email_verified_at)
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div>
                            <h1 class="text-3xl font-bold text-white mb-2">
                                {{ auth()->user()->name }}
                            </h1>
                            <p class="text-white/90 text-lg mb-3">
                                {{ auth()->user()->email }}
                            </p>
                            <div class="flex items-center gap-2 flex-wrap">
                                @if(auth()->user()->isOwner() && auth()->user()->isSitter())
                                    <span class="bg-white/25 backdrop-blur-md text-white text-xs px-3 py-1.5 rounded-full border border-white/20 font-medium">
                                        👤 Właściciel & Opiekun
                                    </span>
                                @elseif(auth()->user()->isOwner())
                                    <span class="bg-white/25 backdrop-blur-md text-white text-xs px-3 py-1.5 rounded-full border border-white/20 font-medium">
                                        👤 Właściciel zwierząt
                                    </span>
                                @elseif(auth()->user()->isSitter())
                                    <span class="bg-white/25 backdrop-blur-md text-white text-xs px-3 py-1.5 rounded-full border border-white/20 font-medium">
                                        🐾 Opiekun zwierząt
                                    </span>
                                @endif
                                @if(auth()->user()->email_verified_at)
                                    <span class="bg-white/25 backdrop-blur-md text-white text-xs px-3 py-1.5 rounded-full border border-white/20 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Zweryfikowany
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Decorative icon --}}
                    <div class="hidden sm:block text-7xl opacity-30">
                        👤
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert dla użytkowników niebędących pet sitterami - NOWA SEKCJA --}}
        @if(!$isSitter)
            <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500 rounded-3xl shadow-2xl">
                <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-transparent via-white/5 to-white/10"></div>

                {{-- Decorative Pattern --}}
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <pattern id="becomesitterpattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                <circle cx="10" cy="10" r="1.5" fill="white" opacity="0.4"/>
                            </pattern>
                        </defs>
                        <rect width="100" height="100" fill="url(#becomesitterpattern)"/>
                    </svg>
                </div>

                <div class="relative p-8">
                    <div class="flex flex-col lg:flex-row items-center gap-8">
                        {{-- Ikona --}}
                        <div class="flex-shrink-0">
                            <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-3xl flex items-center justify-center border-2 border-white/30 shadow-2xl">
                                <span class="text-5xl">🐾</span>
                            </div>
                        </div>

                        {{-- Treść --}}
                        <div class="flex-1 text-center lg:text-left">
                            <h2 class="text-3xl font-bold text-white mb-3">
                                Zostań Pet Sitterem i zarabiaj!
                            </h2>
                            <p class="text-white/90 text-lg mb-6 leading-relaxed">
                                Dołącz do naszej społeczności profesjonalnych opiekunów. Pomagaj właścicielom zwierząt i zarabiaj w elastycznych godzinach, robiąc to co kochasz.
                            </p>

                            {{-- Korzyści w siatce --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                                <div class="bg-white/15 backdrop-blur-md rounded-xl p-4 border border-white/20">
                                    <div class="text-2xl font-bold text-white mb-1">40-80 zł/godz</div>
                                    <div class="text-white/80 text-sm">Elastyczne zarobki</div>
                                </div>
                                <div class="bg-white/15 backdrop-blur-md rounded-xl p-4 border border-white/20">
                                    <div class="text-2xl font-bold text-white mb-1">Własny grafik</div>
                                    <div class="text-white/80 text-sm">Ty decydujesz kiedy</div>
                                </div>
                                <div class="bg-white/15 backdrop-blur-md rounded-xl p-4 border border-white/20">
                                    <div class="text-2xl font-bold text-white mb-1">10 min</div>
                                    <div class="text-white/80 text-sm">Prosty proces</div>
                                </div>
                            </div>

                            {{-- Przycisk CTA --}}
                            <a href="{{ route('profile.become-sitter') }}"
                               class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-bold text-lg rounded-2xl shadow-2xl hover:shadow-3xl hover:scale-105 transition-all duration-300">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                Rozpocznij kreator (10 min)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Alert dla pet sitterów którzy nie ukończyli wizarda - NOWA SEKCJA --}}
        @if($isSitter && !$wizardCompleted)
            <div class="bg-gradient-to-r from-blue-50 via-cyan-50 to-teal-50 border-2 border-blue-300 rounded-3xl p-6 shadow-xl">
                <div class="flex items-start gap-5">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-blue-900 mb-2">Dokończ konfigurację profilu opiekuna</h3>
                        <p class="text-blue-800 mb-4 leading-relaxed">
                            Twój profil opiekuna wymaga uzupełnienia. Przejdź przez prosty kreator, aby aktywować swoje konto i zacząć przyjmować zlecenia.
                        </p>

                        {{-- Postęp --}}
                        <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 mb-4 border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-blue-900">Status profilu</span>
                                <span class="text-sm font-bold text-blue-600">Nieukończony</span>
                            </div>
                            <div class="w-full bg-blue-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-3 rounded-full transition-all duration-500" style="width: 30%"></div>
                            </div>
                            <p class="text-xs text-blue-700 mt-2">Wypełnij kreator, aby odblokować pełne funkcje</p>
                        </div>

                        <a href="{{ route('profile.become-sitter') }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Dokończ konfigurację profilu
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Warning Alert if profile incomplete --}}
        @if(!auth()->user()->profile || !auth()->user()->profile->is_complete)
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-orange-200 rounded-2xl p-5 shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-amber-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-orange-900 font-semibold mb-1">Profil wymaga uzupełnienia</h3>
                        <p class="text-sm text-orange-800">
                            Wypełnij wszystkie wymagane informacje w zakładce <span class="font-semibold">Dane personalne</span>, aby w pełni korzystać z platformy.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Content with Tabs and Sidebar --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Main Content Area --}}
            <div class="lg:col-span-3">
                <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl border border-white/20">
                    <div x-data="{
                        activeTab: window.location.hash ? window.location.hash.substring(1) : 'personal-data',
                        init() {
                            window.addEventListener('hashchange', () => {
                                this.activeTab = window.location.hash ? window.location.hash.substring(1) : 'personal-data';
                            });
                        }
                    }" class="w-full">
                        {{-- Tab Headers with gradient underline --}}
                        <div class="border-b border-gray-200">
                            <nav class="flex space-x-8 px-8 overflow-x-auto" aria-label="Tabs">
                                <button @click="activeTab = 'personal-data'; window.location.hash = 'personal-data'"
                                        class="whitespace-nowrap py-5 px-1 font-medium text-sm transition-all relative group"
                                        :class="activeTab === 'personal-data' ? 'text-emerald-600' : 'text-gray-500 hover:text-gray-700'">
                                    Dane personalne
                                    <div x-show="activeTab === 'personal-data'"
                                         class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                                </button>

                                <button @click="activeTab = 'account-management'; window.location.hash = 'account-management'"
                                        class="whitespace-nowrap py-5 px-1 font-medium text-sm transition-all relative group"
                                        :class="activeTab === 'account-management' ? 'text-emerald-600' : 'text-gray-500 hover:text-gray-700'">
                                    Zarządzanie kontem
                                    <div x-show="activeTab === 'account-management'"
                                         class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                                </button>

                                <button @click="activeTab = 'notifications'; window.location.hash = 'notifications'"
                                        class="whitespace-nowrap py-5 px-1 font-medium text-sm transition-all relative group"
                                        :class="activeTab === 'notifications' ? 'text-emerald-600' : 'text-gray-500 hover:text-gray-700'">
                                    Powiadomienia
                                    <div x-show="activeTab === 'notifications'"
                                         class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                                </button>
                            </nav>
                        </div>

                        {{-- Tab Content --}}
                        <div class="p-8">
                            {{-- Personal Data Tab --}}
                            <div x-show="activeTab === 'personal-data'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="space-y-8">
                                    {{-- Profile Information Section --}}
                                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-6 border border-gray-100">
                                        <div class="flex items-center mb-6">
                                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">Informacje osobiste</h3>
                                                <p class="text-sm text-gray-600">Zaktualizuj swoje dane kontaktowe</p>
                                            </div>
                                        </div>
                                        <livewire:profile.update-profile-information-form />
                                    </div>

                                    {{-- Additional Profile Fields --}}
                                    <div class="bg-gradient-to-br from-white to-purple-50/30 rounded-2xl p-6 border border-purple-100">
                                        <div class="flex items-center mb-6">
                                            <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">Dodatkowe informacje</h3>
                                                <p class="text-sm text-gray-600">Uzupełnij swój profil</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Numer telefonu
                                                </label>
                                                <input type="tel"
                                                       value="{{ auth()->user()->profile->phone ?? '' }}"
                                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all"
                                                       placeholder="+48 123 456 789">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Data urodzenia
                                                </label>
                                                <input type="date"
                                                       value="{{ auth()->user()->profile->birth_date ?? '' }}"
                                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    O mnie
                                                </label>
                                                <textarea rows="4"
                                                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all"
                                                          placeholder="Opowiedz coś o sobie...">{{ auth()->user()->profile->bio ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Account Management Tab --}}
                            <div x-show="activeTab === 'account-management'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="space-y-8">
                                    {{-- Password Change Section --}}
                                    <div class="bg-gradient-to-br from-white to-green-50/30 rounded-2xl p-6 border border-green-100">
                                        <div class="flex items-center mb-6">
                                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">Zmiana hasła</h3>
                                                <p class="text-sm text-gray-600">Zapewnij bezpieczeństwo konta silnym hasłem</p>
                                            </div>
                                        </div>
                                        <livewire:profile.update-password-form />
                                    </div>

                                    {{-- Account Statistics --}}
                                    <div class="bg-gradient-to-br from-white to-blue-50/30 rounded-2xl p-6 border border-blue-100">
                                        <div class="flex items-center mb-6">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">Statystyki konta</h3>
                                                <p class="text-sm text-gray-600">Informacje o Twoim koncie</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-5 border border-gray-100 hover:shadow-lg transition-all">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm text-gray-600 font-medium">Data rejestracji</span>
                                                    <span class="text-sm font-bold text-gray-900">
                                                        {{ auth()->user()->created_at->format('d.m.Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-5 border border-gray-100 hover:shadow-lg transition-all">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm text-gray-600 font-medium">Ostatnie logowanie</span>
                                                    <span class="text-sm font-bold text-gray-900">
                                                        {{ auth()->user()->updated_at->format('d.m.Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if(auth()->user()->isOwner())
                                                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-5 border border-gray-100 hover:shadow-lg transition-all">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm text-gray-600 font-medium">Zwierzęta</span>
                                                        <span class="text-sm font-bold text-gray-900">
                                                            {{ auth()->user()->pets()->count() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(auth()->user()->isSitter())
                                                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-5 border border-gray-100 hover:shadow-lg transition-all">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm text-gray-600 font-medium">Aktywne usługi</span>
                                                        <span class="text-sm font-bold text-gray-900">
                                                            {{ auth()->user()->services()->where('is_active', true)->count() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Danger Zone --}}
                                    <div class="border-t-2 border-gray-200 pt-8">
                                        <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl p-6 border-2 border-red-200">
                                            <div class="flex items-center mb-6">
                                                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-500 rounded-xl flex items-center justify-center mr-4">
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-xl font-bold text-red-700">Strefa niebezpieczna</h3>
                                                    <p class="text-sm text-red-600">Nieodwracalne akcje konta</p>
                                                </div>
                                            </div>
                                            <livewire:profile.delete-user-form />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Notifications Tab --}}
                            <div x-show="activeTab === 'notifications'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="space-y-6">
                                    <div class="flex items-center mb-6">
                                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center mr-4">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">Ustawienia powiadomień</h3>
                                            <p class="text-sm text-gray-600">Zarządzaj preferencjami powiadomień</p>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        {{-- Email Notifications --}}
                                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-6 border border-gray-100">
                                            <h4 class="font-bold text-gray-900 mb-5">Powiadomienia email</h4>
                                            <div class="space-y-5">
                                                <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 hover:border-emerald-200 transition-all">
                                                    <div>
                                                        <label class="text-sm font-semibold text-gray-700">Nowe wiadomości</label>
                                                        <p class="text-xs text-gray-500 mt-1">Otrzymuj email gdy ktoś wyśle Ci wiadomość</p>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="sr-only peer" checked>
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-500"></div>
                                                    </label>
                                                </div>

                                                <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 hover:border-emerald-200 transition-all">
                                                    <div>
                                                        <label class="text-sm font-semibold text-gray-700">Nowe zlecenia</label>
                                                        <p class="text-xs text-gray-500 mt-1">Powiadom o nowych zleceniach w okolicy</p>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="sr-only peer" checked>
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-500"></div>
                                                    </label>
                                                </div>

                                                <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 hover:border-emerald-200 transition-all">
                                                    <div>
                                                        <label class="text-sm font-semibold text-gray-700">Potwierdzenia płatności</label>
                                                        <p class="text-xs text-gray-500 mt-1">Email po otrzymaniu płatności</p>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="sr-only peer" checked>
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-500"></div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Push Notifications --}}
                                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-6 border border-gray-100">
                                            <h4 class="font-bold text-gray-900 mb-5">Powiadomienia push</h4>
                                            <div class="space-y-5">
                                                <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 hover:border-emerald-200 transition-all">
                                                    <div>
                                                        <label class="text-sm font-semibold text-gray-700">Wszystkie powiadomienia</label>
                                                        <p class="text-xs text-gray-500 mt-1">Ogólne ustawienie dla aplikacji mobilnej</p>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="sr-only peer">
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-500"></div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Marketing Notifications --}}
                                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-6 border border-gray-100">
                                            <h4 class="font-bold text-gray-900 mb-5">Newsletter i promocje</h4>
                                            <div class="space-y-5">
                                                <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 hover:border-emerald-200 transition-all">
                                                    <div>
                                                        <label class="text-sm font-semibold text-gray-700">Newsletter</label>
                                                        <p class="text-xs text-gray-500 mt-1">Otrzymuj cotygodniowy newsletter</p>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="sr-only peer">
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-500"></div>
                                                    </label>
                                                </div>

                                                <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 hover:border-emerald-200 transition-all">
                                                    <div>
                                                        <label class="text-sm font-semibold text-gray-700">Promocje specjalne</label>
                                                        <p class="text-xs text-gray-500 mt-1">Informacje o promocjach i zniżkach</p>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="sr-only peer">
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-500"></div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Save Button Footer --}}
                        <div class="px-8 py-5 bg-gradient-to-r from-gray-50 to-emerald-50/30 border-t border-gray-200 rounded-b-3xl">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-600">
                                    <svg class="w-4 h-4 inline mr-1 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Zmiany są automatycznie zapisywane
                                </p>
                                <button type="button"
                                        class="px-6 py-3 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105"
                                        style="background: linear-gradient(135deg, #10b981, #06b6d4);">
                                    Zapisz wszystkie zmiany
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Sidebar --}}
            <div class="space-y-6">
                {{-- Quick Actions Card --}}
                <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-xl border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path>
                            </svg>
                            Szybkie akcje
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if(auth()->user()->isOwner())
                            <a href="{{ route('profile.dashboard') }}"
                               class="group flex items-center p-4 bg-gradient-to-br from-blue-50 to-cyan-50 hover:from-blue-100 hover:to-cyan-100 rounded-xl transition-all border border-blue-100 hover:border-blue-200 hover:shadow-lg">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-blue-700 group-hover:text-blue-800">Zarządzaj pupilami</span>
                            </a>
                        @endif

                        @if(auth()->user()->isSitter())
                            <a href="{{ route('profile.dashboard') }}"
                               class="group flex items-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 rounded-xl transition-all border border-green-100 hover:border-green-200 hover:shadow-lg">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-green-700 group-hover:text-green-800">Moje usługi</span>
                            </a>
                        @endif

                        <a href="{{ route('profile.dashboard') }}"
                           class="group flex items-center p-4 bg-gradient-to-br from-purple-50 to-indigo-50 hover:from-purple-100 hover:to-indigo-100 rounded-xl transition-all border border-purple-100 hover:border-purple-200 hover:shadow-lg">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-purple-700 group-hover:text-purple-800">Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obsługa powiadomień o aktualizacji profilu
            window.addEventListener('profile-updated', event => {
                console.log('Profile updated:', event.detail);
            });
        });
    </script>
    @endpush
</x-dashboard-layout>
