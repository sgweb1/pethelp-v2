<x-dashboard-layout>
    @section('title', 'Kalendarz dostępności - PetHelp')

    @section('header-title')
        <div class="flex items-center">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Kalendarz dostępności</h1>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                Zarządzaj swoją dostępnością
            </span>
        </div>
    @endsection

    <div class="space-y-6">
        <!-- Info Box -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100">Jak korzystać z kalendarza</h3>
                    <ul class="mt-2 text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <li>• Kliknij na dzień, aby dodać lub edytować dostępność</li>
                        <li>• Ustaw godziny pracy i dni cykliczne</li>
                        <li>• Dodaj notatki dla klientów</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Calendar Component -->
        <livewire:availability-calendar />
    </div>
</x-dashboard-layout>