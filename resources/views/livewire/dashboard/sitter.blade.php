<?php

use function Livewire\Volt\{state, computed};

// State variables
state(['isOnline' => true, 'isAvailableToday' => true]);

// Computed properties
$stats = computed(function() {
    $user = auth()->user();
    return [
        'services' => $user->services()->count(),
        'active_services' => $user->services()->where('is_active', true)->count(),
        'bookings' => $user->sitterBookings()->count(),
        'active_bookings' => $user->sitterBookings()->whereIn('status', ['pending', 'confirmed', 'in_progress'])->count(),
        'today_bookings' => $user->sitterBookings()->whereDate('start_date', today())->count(),
        'reviews' => $user->reviewsReceived()->count(),
        'profile_views' => 0, // TODO: implement profile views tracking
        'completion_rate' => 95, // TODO: calculate actual completion rate
    ];
});

$averageRating = computed(function() {
    $reviews = auth()->user()->reviewsReceived();
    return $reviews->count() > 0 ? $reviews->avg('rating') : 0;
});

$todayEarnings = computed(function() {
    // TODO: implement earnings calculation
    return 0;
});

$thisMonthEarnings = computed(function() {
    // TODO: implement monthly earnings calculation
    return 0;
});

$recentBookings = computed(function() {
    return auth()->user()->sitterBookings()
        ->with(['service.category', 'owner', 'pet'])
        ->latest()
        ->take(3)
        ->get();
});

$todayBookings = computed(function() {
    return auth()->user()->sitterBookings()
        ->with(['service', 'owner', 'pet'])
        ->whereDate('start_date', today())
        ->whereIn('status', ['confirmed', 'in_progress'])
        ->orderBy('start_date')
        ->get();
});

$upcomingBookings = computed(function() {
    return auth()->user()->sitterBookings()
        ->with(['service', 'owner', 'pet'])
        ->where('start_date', '>', now())
        ->whereIn('status', ['confirmed'])
        ->orderBy('start_date')
        ->take(5)
        ->get();
});

// Actions
$toggleOnlineStatus = function() {
    $this->isOnline = !$this->isOnline;
    // TODO: Update database status
    session()->flash('message', $this->isOnline ? 'Status zmieniony na: Online' : 'Status zmieniony na: Offline');
};

$toggleAvailability = function() {
    $this->isAvailableToday = !$this->isAvailableToday;
    // TODO: Update database availability
    session()->flash('message', $this->isAvailableToday ? 'Dostępność: Dostępny dziś' : 'Dostępność: Niedostępny dziś');
};

?>

<!-- Hero-style Background -->
<div class="relative -m-4 sm:-m-6 lg:-m-8 mb-8 bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
                <pattern id="sitterpattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="10" cy="10" r="2" fill="white" opacity="0.3"/>
                </pattern>
            </defs>
            <rect width="100" height="100" fill="url(#sitterpattern)"/>
        </svg>
    </div>

    <div class="relative z-10 p-4 sm:p-6 lg:p-8 space-y-6">
        <!-- Welcome Header -->
        <div class="text-center py-8">
            <div class="inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 mb-4">
                <span class="text-white/90 text-sm font-medium">🐾 Panel Pet Sitter</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                Witaj, <span class="text-yellow-300">{{ auth()->user()->profile?->first_name ?? auth()->user()->name }}</span>! 👋
            </h1>

            <p class="text-lg text-white/90 max-w-2xl mx-auto mb-8">
                Zarządzaj swoimi usługami opieki nad zwierzętami i monitoruj rezerwacje
            </p>
        </div>

        <!-- Status Banner -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
            <div class="flex flex-wrap items-center gap-3 mb-4 sm:mb-0">
                <button wire:click="toggleOnlineStatus"
                        class="inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 transition-all duration-300 hover:bg-white/20">
                    <span class="text-lg mr-2">{{ $isOnline ? '🟢' : '🔴' }}</span>
                    <span class="text-white font-medium">{{ $isOnline ? 'Online' : 'Offline' }}</span>
                </button>

                <button wire:click="toggleAvailability"
                        class="inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 transition-all duration-300 hover:bg-white/20">
                    <span class="text-lg mr-2">📅</span>
                    <span class="text-white">{{ $isAvailableToday ? 'Dostępny dziś' : 'Niedostępny dziś' }}</span>
                </button>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('sitter-services.create') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all duration-300">
                    ➕ Dodaj usługę
                </a>
            </div>
        </div>

        <!-- Hero Stats -->
        <div class="text-center text-white/90 mb-8">
            <div class="space-y-2">
                @if($stats['today_bookings'] > 0)
                    Masz <strong>{{ $stats['today_bookings'] }} {{ $stats['today_bookings'] == 1 ? 'rezerwację' : 'rezerwacje' }}</strong> na dziś
                @endif

                @if($stats['active_bookings'] > 0)
                    i <strong>{{ $stats['active_bookings'] }} {{ $stats['active_bookings'] == 1 ? 'aktywną rezerwację' : 'aktywne rezerwacje' }}</strong>
                @endif
            </div>
        </div>

        <!-- Hero Quick Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 max-w-4xl mx-auto">
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-yellow-300 mb-1">{{ number_format($todayEarnings) }} zł</div>
                <div class="text-white/70 text-sm">Dziś zarobione</div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-yellow-300 mb-1">{{ $stats['today_bookings'] }}</div>
                <div class="text-white/70 text-sm">Dzisiejsze rezerwacje</div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-yellow-300 mb-1">{{ $stats['active_bookings'] }}</div>
                <div class="text-white/70 text-sm">Aktywne rezerwacje</div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-yellow-300 mb-1">{{ number_format($averageRating, 1) }}</div>
                <div class="text-white/70 text-sm">Średnia ocena</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content with Glass Cards -->
<div class="bg-white dark:bg-gray-900 rounded-t-3xl relative z-20 -mt-8">
    <div class="pt-8 pb-8 space-y-6">

        <!-- Enhanced Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Services Card -->
            <div class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white hover:scale-105 hover:shadow-xl">
                <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🔧</div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Usługi</h3>
                <p class="text-xl font-bold text-gray-900">{{ $stats['services'] }}</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Wszystkie usługi</p>
            </div>

            <!-- Active Services Card -->
            <div class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white hover:scale-105 hover:shadow-xl">
                <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">✅</div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Aktywne</h3>
                <p class="text-xl font-bold text-green-600">{{ $stats['active_services'] }}</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Dostępne usługi</p>
            </div>

            <!-- Total Bookings Card -->
            <div class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white hover:scale-105 hover:shadow-xl">
                <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">📅</div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Rezerwacje</h3>
                <p class="text-xl font-bold text-blue-600">{{ $stats['bookings'] }}</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Łącznie rezerwacji</p>
            </div>

            <!-- Reviews Card -->
            <div class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white hover:scale-105 hover:shadow-xl">
                <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">⭐</div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Średnia ocena</h3>
                <p class="text-xl font-bold text-yellow-600">{{ number_format($averageRating, 1) }}</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Z {{ $stats['reviews'] }} opinii</p>
            </div>

            <!-- Profile Views Card -->
            <div class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white hover:scale-105 hover:shadow-xl">
                <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">👁️</div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Wyświetlenia</h3>
                <p class="text-xl font-bold text-purple-600">{{ number_format($stats['profile_views']) }}</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Profil wyświetlany</p>
            </div>

            <!-- Completion Rate Card -->
            <div class="group bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:bg-white hover:scale-105 hover:shadow-xl">
                <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">📊</div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Ukończenia</h3>
                <p class="text-xl font-bold text-indigo-600">{{ $stats['completion_rate'] }}%</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Wskaźnik ukończeń</p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Today's Schedule -->
            <div class="lg:col-span-1">
                <div class="bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">📋</span>
                        Dzisiejszy harmonogram
                    </h3>

                    @if($todayBookings->count() > 0)
                        @foreach($todayBookings as $booking)
                            <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                                <div class="flex items-center">
                                    <div class="text-2xl mr-3">{{ $booking->service->category->icon ?? '🐾' }}</div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">{{ $booking->start_date->format('H:i') }} - {{ $booking->end_date->format('H:i') }}</div>
                                        <div class="text-xs text-gray-600">{{ $booking->service->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $booking->owner->name }} - {{ $booking->pet->name }}</div>
                                    </div>
                                </div>
                                <div class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">
                                    {{ $booking->status_label }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📅</div>
                            <p>Brak zaplanowanych rezerwacji na dziś</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Middle Column - Quick Actions & Earnings -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">⚡</span>
                        Szybkie akcje
                    </h3>

                    <div class="space-y-3">
                        <a href="{{ route('sitter-services.create') }}"
                           class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                            <div class="text-2xl mr-3">➕</div>
                            <div>
                                <div class="font-medium text-blue-900">Dodaj nową usługę</div>
                                <div class="text-sm text-blue-600">Rozszerz swoją ofertę</div>
                            </div>
                        </a>

                        <a href="{{ route('services.index') }}"
                           class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                            <div class="text-2xl mr-3">🔧</div>
                            <div>
                                <div class="font-medium text-green-900">Zarządzaj usługami</div>
                                <div class="text-sm text-green-600">Edytuj istniejące oferty</div>
                            </div>
                        </a>

                        <a href="{{ route('availability.calendar') }}"
                           class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                            <div class="text-2xl mr-3">📅</div>
                            <div>
                                <div class="font-medium text-purple-900">Ustaw dostępność</div>
                                <div class="text-sm text-purple-600">Kalendarz dostępności</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Earnings Overview -->
                <div class="bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">💰</span>
                        Przegląd zarobków
                    </h3>

                    <div class="space-y-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($thisMonthEarnings) }} zł</div>
                            <div class="text-sm text-green-700">Ten miesiąc</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-700">{{ number_format($todayEarnings) }} zł</div>
                                <div class="text-xs text-gray-500">Dziś</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-700">{{ $stats['active_bookings'] }}</div>
                                <div class="text-xs text-gray-500">Aktywne</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Reviews & Recent Bookings -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Reviews Summary -->
                <div class="bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">⭐</span>
                        Twoje opinie
                    </h3>

                    <div class="text-center">
                        @if($stats['reviews'] > 0)
                            <div class="mb-4">
                                <div class="text-3xl font-bold text-yellow-500">{{ number_format($averageRating, 1) }}</div>
                                <div class="flex justify-center items-center mt-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4" fill="{{ $i <= $averageRating ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ number_format($averageRating, 1) }}</span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">Na podstawie {{ $stats['reviews'] }} opinii</div>
                            </div>
                        @else
                            <div class="py-8 text-gray-500">
                                <div class="text-4xl mb-2">⭐</div>
                                <p>Nie masz jeszcze opinii</p>
                                <p class="text-sm">Wykonaj pierwszą usługę, aby otrzymać ocenę</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">📋</span>
                        Ostatnie rezerwacje
                    </h3>

                    @if($recentBookings->count() > 0)
                        @foreach($recentBookings as $booking)
                            <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                                <div class="flex items-center">
                                    <span class="text-lg">{{ $booking->service->category->icon ?? '🐾' }}</span>
                                    <div class="ml-3">
                                        <div class="font-medium text-sm">{{ $booking->service->title }}</div>
                                        <div class="text-xs text-gray-600">{{ $booking->owner->name }} - {{ $booking->pet->name }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">{{ $booking->start_date->format('d.m.Y') }}</div>
                                    <div class="text-xs px-2 py-1 rounded-full"
                                         style="background-color: {{ $booking->status_color }}; color: white;">
                                        {{ $booking->status_label }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📋</div>
                            <p>Brak ostatnich rezerwacji</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div class="bg-white/95 backdrop-blur-md border border-white/20 rounded-xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="text-2xl mr-2">🗓️</span>
                Nadchodzące rezerwacje
            </h3>

            @if($upcomingBookings->count() > 0)
                @foreach($upcomingBookings as $booking)
                    <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center">
                            <div class="text-2xl mr-3">{{ $booking->service->category->icon ?? '🐾' }}</div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $booking->start_date->format('d.m H:i') }}</div>
                                <div class="text-xs text-gray-600">{{ $booking->service->title }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->owner->name }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            {{ $booking->start_date->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">🗓️</div>
                    <p>Brak nadchodzących rezerwacji</p>
                </div>
            @endif
        </div>

        <!-- Flash Messages -->
        @if(session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" x-data="{ show: true }" x-show="show">
                <span class="block sm:inline">{{ session('message') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="show = false">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" viewBox="0 0 20 20">
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif
    </div>
</div>