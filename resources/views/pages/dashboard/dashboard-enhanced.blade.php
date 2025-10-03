@php
    $breadcrumbs = [
        [
            'title' => 'Panel',
            'icon' => '🏠'
        ]
    ];
@endphp

<x-dashboard-layout :breadcrumbs="$breadcrumbs">
    @section('title', 'Dashboard - PetHelp')

    {{-- Gradient Header with Welcome --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 rounded-3xl shadow-2xl -mx-4 sm:-mx-6 lg:-mx-8 mb-8">
        <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-transparent via-white/5 to-white/10"></div>

        {{-- Decorative Pattern --}}
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="dashboardpattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="2" fill="white" opacity="0.3"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#dashboardpattern)"/>
            </svg>
        </div>

        <div class="relative z-10 p-8">
            {{-- Welcome Badge --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center bg-white/20 backdrop-blur-md rounded-full px-5 py-2 mb-4 border border-white/30">
                    <span class="text-white font-semibold text-sm">🏆 Panel użytkownika</span>
                </div>

                <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4 leading-tight">
                    Witaj, <span class="text-emerald-100">{{ auth()->user()->profile?->first_name ?? auth()->user()->name }}</span>! 👋
                </h1>

                <p class="text-lg text-white/90 max-w-2xl mx-auto mb-8">
                    @if(auth()->user()->isOwner() && auth()->user()->isSitter())
                        Zarządzaj swoimi pupilami i świadcz usługi opieki
                    @elseif(auth()->user()->isOwner())
                        Znajdź najlepszą opiekę dla swoich pupili
                    @elseif(auth()->user()->isSitter())
                        Świadcz usługi opieki nad zwierzętami
                    @else
                        Uzupełnij swój profil, aby zacząć korzystać z platformy
                    @endif
                </p>

                {{-- Hero Stats with gradient badges --}}
                @if(auth()->user()->isOwner() || auth()->user()->isSitter())
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 max-w-4xl mx-auto">
                        @if(auth()->user()->isOwner())
                            <div class="bg-white/20 backdrop-blur-md rounded-2xl p-5 text-center border border-white/30 hover:bg-white/25 transition-all">
                                <div class="text-3xl font-bold text-white mb-1">{{ auth()->user()->pets()->count() }}</div>
                                <div class="text-white/80 text-sm font-medium">Moje zwierzęta</div>
                            </div>
                        @endif

                        @if(auth()->user()->isSitter())
                            <div class="bg-white/20 backdrop-blur-md rounded-2xl p-5 text-center border border-white/30 hover:bg-white/25 transition-all">
                                <div class="text-3xl font-bold text-white mb-1">{{ auth()->user()->services()->count() }}</div>
                                <div class="text-white/80 text-sm font-medium">Usługi</div>
                            </div>
                        @endif

                        <div class="bg-white/20 backdrop-blur-md rounded-2xl p-5 text-center border border-white/30 hover:bg-white/25 transition-all">
                            <div class="text-3xl font-bold text-white mb-1">{{ auth()->user()->ownerBookings()->count() + auth()->user()->sitterBookings()->count() }}</div>
                            <div class="text-white/80 text-sm font-medium">Rezerwacje</div>
                        </div>

                        <div class="bg-white/20 backdrop-blur-md rounded-2xl p-5 text-center border border-white/30 hover:bg-white/25 transition-all">
                            <div class="text-3xl font-bold text-white mb-1">{{ auth()->user()->reviewsReceived()->count() }}</div>
                            <div class="text-white/80 text-sm font-medium">Opinie</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Actions Grid with gradient icons --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- My Pets Action --}}
        <a href="{{ route('profile.pets.index') }}" class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-2xl shadow-lg p-6 text-center cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center text-3xl shadow-lg group-hover:scale-110 transition-transform">
                🐕‍🦺
            </div>
            <h3 class="text-gray-900 font-bold mb-2">Moje zwierzęta</h3>
            <p class="text-gray-600 text-sm">Zarządzaj pupilami</p>
        </a>

        {{-- Gallery Action --}}
        <a href="{{ route('profile.gallery.index') }}" class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-2xl shadow-lg p-6 text-center cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-2xl flex items-center justify-center text-3xl shadow-lg group-hover:scale-110 transition-transform">
                📸
            </div>
            <h3 class="text-gray-900 font-bold mb-2">Galeria</h3>
            <p class="text-gray-600 text-sm">Zdjęcia pupili</p>
        </a>

        {{-- Services/Search Action --}}
        @if(auth()->user()->isSitter())
            <a href="{{ route('profile.services.index') }}" class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-2xl shadow-lg p-6 text-center cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center text-3xl shadow-lg group-hover:scale-110 transition-transform">
                    🔧
                </div>
                <h3 class="text-gray-900 font-bold mb-2">Usługi</h3>
                <p class="text-gray-600 text-sm">Zarządzaj ofertą</p>
            </a>
        @else
            <a href="/search" class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-2xl shadow-lg p-6 text-center cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center text-3xl shadow-lg group-hover:scale-110 transition-transform">
                    🔍
                </div>
                <h3 class="text-gray-900 font-bold mb-2">Szukaj</h3>
                <p class="text-gray-600 text-sm">Znajdź opiekuna</p>
            </a>
        @endif

        {{-- Events Action --}}
        <a href="{{ route('events.index') }}" class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-2xl shadow-lg p-6 text-center cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-pink-400 to-rose-500 rounded-2xl flex items-center justify-center text-3xl shadow-lg group-hover:scale-110 transition-transform">
                🎉
            </div>
            <h3 class="text-gray-900 font-bold mb-2">Wydarzenia</h3>
            <p class="text-gray-600 text-sm">Spotkania i eventy</p>
        </a>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - 2/3 width --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Quick Actions Card --}}
            <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-xl border border-white/20 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        Szybkie akcje
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-6">Wybierz jedną z dostępnych opcji:</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{ route('profile.pets.create') }}"
                           class="flex items-center justify-center px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Dodaj zwierzę
                        </a>

                        @if(auth()->user()->isSitter())
                            <a href="{{ route('profile.services.create') }}"
                               class="flex items-center justify-center px-6 py-4 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                </svg>
                                Dodaj usługę
                            </a>
                        @else
                            <a href="/search"
                               class="flex items-center justify-center px-6 py-4 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Znajdź opiekuna
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stats Overview Card --}}
            <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-xl border border-white/20 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        Przegląd aktywności
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @if(auth()->user()->isOwner())
                        <div class="flex items-center justify-between p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-100">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700 font-medium">Moje zwierzęta</span>
                            </div>
                            <span class="text-2xl font-bold text-gray-900">{{ auth()->user()->pets()->count() }}</span>
                        </div>
                    @endif

                    @if(auth()->user()->isSitter())
                        <div class="flex items-center justify-between p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-100">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700 font-medium">Moje usługi</span>
                            </div>
                            <span class="text-2xl font-bold text-gray-900">{{ auth()->user()->services()->count() }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between p-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border border-purple-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Wszystkie rezerwacje</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ auth()->user()->ownerBookings()->count() + auth()->user()->sitterBookings()->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl border border-amber-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Otrzymane opinie</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ auth()->user()->reviewsReceived()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - 1/3 width --}}
        <div class="space-y-6">
            {{-- Profile Summary Card --}}
            <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-xl border border-white/20 overflow-hidden">
                <div class="bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        Twój profil
                    </h3>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg">{{ auth()->user()->name }}</h4>
                            <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-3 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-3 py-1 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 rounded-full text-xs font-semibold">
                                @if(auth()->user()->isOwner() && auth()->user()->isSitter())
                                    Właściciel & Opiekun
                                @elseif(auth()->user()->isOwner())
                                    Właściciel zwierząt
                                @elseif(auth()->user()->isSitter())
                                    Opiekun zwierząt
                                @else
                                    Nowy użytkownik
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Członek od:</span>
                            <span class="text-gray-900 font-semibold">{{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edytuj profil
                        </a>
                    </div>
                </div>
            </div>

            {{-- Become Pet Sitter (for non-sitters) --}}
            @if(!auth()->user()->isSitter())
                <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-xl border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-6">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            Zostań opiekunem
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Dołącz do naszej społeczności opiekunów i zarabiaj opiekując się zwierzętami.
                        </p>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-5 border border-amber-100">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-amber-700 mb-1">40-80 zł/godz</div>
                                <div class="text-xs text-amber-600 font-medium">Średnie zarobki</div>
                            </div>
                        </div>

                        <a href="/register?type=sitter"
                           class="flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                            </svg>
                            Dołącz jako opiekun
                        </a>
                    </div>
                </div>
            @endif

            {{-- Quick Links Card --}}
            <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-xl border border-white/20 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        Szybkie linki
                    </h3>
                </div>
                <div class="p-6 space-y-2">
                    <a href="{{ route('profile.pets.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 rounded-xl transition-all group">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <span class="font-medium">Moje zwierzęta</span>
                    </a>

                    <a href="{{ route('profile.gallery.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 rounded-xl transition-all group">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="font-medium">Galeria zdjęć</span>
                    </a>

                    @if(auth()->user()->isSitter())
                        <a href="{{ route('profile.services.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 rounded-xl transition-all group">
                            <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                </svg>
                            </div>
                            <span class="font-medium">Oferta</span>
                        </a>
                    @else
                        <a href="/search" class="flex items-center p-3 text-gray-700 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 rounded-xl transition-all group">
                            <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <span class="font-medium">Znajdź opiekuna</span>
                        </a>
                    @endif

                    <a href="{{ route('events.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 rounded-xl transition-all group">
                        <div class="w-8 h-8 bg-gradient-to-br from-pink-100 to-rose-100 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="font-medium">Wydarzenia</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Welcome Message for New Users --}}
    @if(!auth()->user()->profile?->bio || (!auth()->user()->isOwner() && !auth()->user()->isSitter()))
        <div class="mt-8">
            <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-xl border border-white/20 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        Witaj w PetHelp!
                    </h3>
                </div>
                <div class="p-6 space-y-5">
                    <p class="text-gray-700 text-lg">
                        Uzupełnij swój profil i zacznij korzystać z pełni możliwości platformy.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-start space-x-3 p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-100">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Dodaj informacje o sobie</p>
                                <p class="text-xs text-gray-600 mt-1">Uzupełnij podstawowe dane</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-100">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Zarejestruj zwierzęta</p>
                                <p class="text-xs text-gray-600 mt-1">Dodaj swoich pupili</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 p-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border border-purple-100">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Znajdź opiekunów</p>
                                <p class="text-xs text-gray-600 mt-1">Rozpocznij poszukiwania</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center justify-center w-full px-6 py-4 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Uzupełnij profil teraz
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Become Pet Sitter Section - Livewire Component --}}
    <div class="mt-8">
        @livewire('dashboard.become-sitter')
    </div>

    @push('scripts')
    <script>
        // Dashboard interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh dashboard data every 5 minutes
            setInterval(function() {
                Livewire.dispatch('refreshDashboard');
            }, 300000);

            // Handle dashboard notifications
            window.addEventListener('dashboard-notification', event => {
                console.log('Dashboard notification:', event.detail);
            });
        });
    </script>

    <style>
        /* Enhanced animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Hover effects */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-4px);
        }

        /* Dark mode enhancements */
        @media (prefers-color-scheme: dark) {
            .bg-white\/95 {
                background-color: rgba(31, 41, 55, 0.95);
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .transition-all,
            .transition-transform,
            .animate-float {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
    @endpush
</x-dashboard-layout>
