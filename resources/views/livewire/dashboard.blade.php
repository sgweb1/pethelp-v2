<?php

use App\Models\Pet;
use App\Models\UserProfile;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

    /**
     * Breadcrumbs dla głównego dashboardu.
     *
     * @return array
     */
    public function getBreadcrumbsProperty(): array
    {
        return [
            [
                'title' => 'Panel',
                'icon' => '🏠'
            ]
        ];
    }

    public function mount()
    {
        // Zapewniamy, że użytkownik ma profil
        $user = auth()->user();
        if ($user && !$user->profile) {
            $user->profile()->create([
                'role' => 'owner'
            ]);
        }
    }

    public function with()
    {
        $user = auth()->user();
        if (!$user) {
            return [
                'profile' => null,
                'totalPets' => 0,
                'activePets' => 0,
                'totalBookings' => 0,
                'upcomingBookings' => 0,
                'recentPets' => collect(),
            ];
        }

        $profile = $user->profile;

        // Statystyki użytkownika
        $totalPets = Pet::where('user_id', $user->id)->count();
        $activePets = Pet::where('user_id', $user->id)->where('is_active', true)->count();
        $totalBookings = 0; // TODO: Implement when bookings are ready
        $upcomingBookings = 0; // TODO: Implement when bookings are ready

        // Ostatnie zwierzęta
        $recentPets = Pet::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        return [
            'profile' => $profile,
            'totalPets' => $totalPets,
            'activePets' => $activePets,
            'totalBookings' => $totalBookings,
            'upcomingBookings' => $upcomingBookings,
            'recentPets' => $recentPets,
        ];
    }
}; ?>

@php
    // Przekaż breadcrumbs do layoutu
    $breadcrumbs = $this->breadcrumbs;
@endphp

<div class="desktop-window">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Panel główny
                    </h1>
                    <p class="text-white/80 mt-1">
                        Witaj z powrotem, {{ auth()->user()?->name ?? 'Użytkowniku' }}!
                    </p>
                </div>

                <div class="flex space-x-3">
                    <x-ui.button variant="outline" size="sm" class="bg-white/10 border-white/20 text-white hover:bg-white/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil
                    </x-ui.button>

                    <x-ui.button variant="warm" size="sm" href="{{ route('profile.pets.create') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dodaj zwierzę
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-dashboard.stats-card
                title="Moje zwierzęta"
                :value="$totalPets"
                color="primary"
                :icon="'<svg class=\"w-6 h-6\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z\"></path></svg>'"
                description="Łączna liczba zwierząt"
            />

            <x-dashboard.stats-card
                title="Aktywne profile"
                :value="$activePets"
                color="success"
                :icon="'<svg class=\"w-6 h-6\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\"></path></svg>'"
                description="Gotowe do rezerwacji"
            />

            <x-dashboard.stats-card
                title="Wszystkie rezerwacje"
                :value="$totalBookings"
                color="warm"
                :icon="'<svg class=\"w-6 h-6\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\"></path></svg>'"
                description="Historia opieki"
            />

            <x-dashboard.stats-card
                title="Nadchodzące"
                :value="$upcomingBookings"
                color="nature"
                :icon="'<svg class=\"w-6 h-6\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\"></path></svg>'"
                description="W tym tygodniu"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Pets -->
            <div class="lg:col-span-2">
                <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-large p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Moje zwierzęta</h2>
                        <x-ui.button variant="ghost" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Zobacz wszystkie
                        </x-ui.button>
                    </div>

                    @if($recentPets->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach($recentPets as $pet)
                                <x-dashboard.pet-card wire:key="pet-{{ $pet->id }}" :pet="$pet" compact="true" />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Brak zwierząt</h3>
                            <p class="text-gray-600 mb-4">Dodaj profil swojego pupila, aby rozpocząć.</p>
                            <x-ui.button variant="primary" size="sm" href="{{ route('profile.pets.create') }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Dodaj zwierzę
                            </x-ui.button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions & Profile -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-large p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Szybkie akcje</h3>
                    <div class="space-y-3">
                        <x-ui.button variant="outline" size="sm" fullWidth="true">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Znajdź opiekuna
                        </x-ui.button>

                        <x-ui.button variant="outline" size="sm" fullWidth="true">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Moje rezerwacje
                        </x-ui.button>

                        <x-ui.button variant="outline" size="sm" fullWidth="true">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                            Wiadomości
                        </x-ui.button>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-large p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mój profil</h3>

                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gradient-to-br from-primary-400 to-warm-400 flex items-center justify-center">
                            <span class="text-white font-semibold text-lg">
                                {{ substr(auth()->user()?->name ?? 'U', 0, 1) }}
                            </span>
                        </div>
                        <h4 class="font-medium text-gray-900">{{ auth()->user()?->name ?? 'Użytkownik' }}</h4>
                        <p class="text-sm text-gray-600">{{ auth()->user()?->email ?? 'Brak email' }}</p>

                        @if($profile)
                            <div class="mt-3 text-xs text-gray-500">
                                Typ konta: {{ $profile->role === 'owner' ? 'Właściciel' : ($profile->role === 'sitter' ? 'Opiekun' : 'Właściciel i Opiekun') }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <x-ui.button variant="primary" size="sm" fullWidth="true">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edytuj profil
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
