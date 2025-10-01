@php
    $breadcrumbs = [
        [
            'title' => 'Panel',
            'url' => route('profile.dashboard'),
            'icon' => '🏠'
        ],
        [
            'title' => 'Kalendarz dostępności',
            'icon' => '📅'
        ]
    ];
@endphp

<x-dashboard-layout :breadcrumbs="$breadcrumbs">
    @section('title', 'Kalendarz dostępności - PetHelp')

    @section('header-title')
        <div class="flex items-center">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                <svg class="w-6 h-6 inline mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Kalendarz dostępności
            </h1>
        </div>
    @endsection

    <div class="space-y-6">
        <!-- Availability Calendar Component -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <livewire:availability-calendar />
            </div>
        </div>
    </div>
</x-dashboard-layout>