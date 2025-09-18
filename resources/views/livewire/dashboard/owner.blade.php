<?php

use Livewire\Volt\Component;

new class extends Component {
    public function getStatsProperty()
    {
        $user = auth()->user();
        return [
            'pets' => $user->pets()->count(),
            'bookings' => $user->ownerBookings()->count(),
            'active_bookings' => $user->ownerBookings()->whereIn('status', ['pending', 'confirmed', 'in_progress'])->count(),
            'reviews' => 0, // TODO: implement reviews
        ];
    }

    public function getRecentBookingsProperty()
    {
        return auth()->user()->ownerBookings()
            ->with(['service.category', 'sitter', 'pet'])
            ->latest()
            ->take(3)
            ->get();
    }
}; ?>

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">
                Witaj {{ auth()->user()->profile?->first_name ?? auth()->user()->name }}! 🐾
            </h1>
            <p class="text-white/90">Panel właściciela - zarządzaj opieką nad swoimi pupilami</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <div class="bg-white/95 backdrop-blur-md rounded-xl p-6 shadow-lg">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">🐕</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->stats['pets'] }}</p>
                        <p class="text-sm text-gray-600">Moje pupile</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/95 backdrop-blur-md rounded-xl p-6 shadow-lg">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">📅</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->stats['bookings'] }}</p>
                        <p class="text-sm text-gray-600">Rezerwacje</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/95 backdrop-blur-md rounded-xl p-6 shadow-lg">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">⏰</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->stats['active_bookings'] }}</p>
                        <p class="text-sm text-gray-600">Aktywne</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/95 backdrop-blur-md rounded-xl p-6 shadow-lg">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">⭐</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->stats['reviews'] }}</p>
                        <p class="text-sm text-gray-600">Opinie</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white/95 backdrop-blur-md rounded-xl p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Szybkie akcje</h3>
                <div class="space-y-3">
                    <a href="/search" class="flex items-center w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <span class="text-xl mr-3">🔍</span>
                        Znajdź opiekuna
                    </a>
                    <a href="/pets/create" class="flex items-center w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <span class="text-xl mr-3">➕</span>
                        Dodaj pupila
                    </a>
                    <a href="/bookings" class="flex items-center w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span class="text-xl mr-3">📋</span>
                        Moje rezerwacje
                    </a>
                </div>
            </div>

            <div class="bg-white/95 backdrop-blur-md rounded-xl p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ostatnie rezerwacje</h3>
                <div class="space-y-3">
                    @if($this->recentBookings->count() > 0)
                        @foreach($this->recentBookings as $booking)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg">{{ $booking->service->category->icon ?? '🐾' }}</span>
                                    <div>
                                        <div class="font-medium text-sm">{{ $booking->service->title }}</div>
                                        <div class="text-xs text-gray-600">{{ $booking->sitter->name }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">{{ $booking->start_date->format('d.m.Y') }}</div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($booking->status === 'completed') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $booking->status_label }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center pt-2">
                            <a href="/bookings" class="text-indigo-600 text-sm hover:text-indigo-800">
                                Zobacz wszystkie rezerwacje →
                            </a>
                        </div>
                    @else
                        <div class="text-sm text-gray-600">Brak rezerwacji</div>
                        <div class="text-xs text-gray-500">
                            Rozpocznij od znalezienia opiekuna dla swojego pupila!
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Completion -->
        @if(!auth()->user()->profile?->bio || !auth()->user()->profile?->phone)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 shadow-lg">
            <div class="flex items-start">
                <div class="text-2xl mr-4">⚠️</div>
                <div>
                    <h3 class="text-lg font-medium text-yellow-800 mb-2">Uzupełnij swój profil</h3>
                    <p class="text-yellow-700 mb-4">
                        Kompletny profil zwiększa zaufanie opiekunów i ułatwia znajdowanie najlepszej opieki.
                    </p>
                    <a href="/profile" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        Uzupełnij profil
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
