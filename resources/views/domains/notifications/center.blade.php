@php
    $breadcrumbs = [
        [
            'title' => 'Panel',
            'url' => route('profile.dashboard'),
            'icon' => '🏠'
        ],
        [
            'title' => 'Powiadomienia',
            'icon' => '🔔'
        ]
    ];
@endphp

<x-dashboard-layout :breadcrumbs="$breadcrumbs">
    @section('title', 'Powiadomienia - PetHelp')

    @section('header-title')
        <div class="flex items-center">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                <svg class="w-6 h-6 inline mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 21.73a2 2 0 01-4 0M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9z"/>
                </svg>
                Powiadomienia
            </h1>
        </div>
    @endsection

    <div class="space-y-6">
        <!-- Notification Center Component -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <livewire:notification-center />
            </div>
        </div>
    </div>
</x-dashboard-layout>