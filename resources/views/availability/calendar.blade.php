@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Zarządzaj swoją dostępnością</h1>
            <p class="text-gray-600 mt-2">Ustaw godziny, w których jesteś dostępny dla swoich klientów</p>
        </div>

        <!-- Calendar Component -->
        <livewire:availability-calendar />

        <!-- Tips Section -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">💡 Wskazówki</h3>
            <ul class="text-blue-800 space-y-2 text-sm">
                <li>• <strong>Regularna dostępność:</strong> Ustaw cykliczne dni tygodnia, aby automatycznie dodać dostępność na kilka tygodni w przód</li>
                <li>• <strong>Dni niedostępne:</strong> Odznacz checkbox "Jestem dostępny", aby oznaczyć dzień jako niedostępny</li>
                <li>• <strong>Szybkie akcje:</strong> Użyj przycisków poniżej kalendarza, aby szybko ustawić dostępność na dzisiaj lub jutro</li>
                <li>• <strong>Edycja:</strong> Kliknij na dowolny dzień w kalendarzu, aby edytować lub dodać dostępność</li>
                <li>• <strong>Notatki:</strong> Dodaj dodatkowe informacje, które mogą być przydatne dla klientów</li>
            </ul>
        </div>
    </div>
</div>
@endsection