<!DOCTYPE html>
<html lang="pl" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Błąd panelu administracyjnego - PetHelp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    <div class="min-h-full flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Ikona błędu -->
            <div class="flex justify-center">
                <div class="rounded-full bg-red-100 p-6">
                    <svg class="h-16 w-16 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            <!-- Główna wiadomość -->
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-gray-900 mb-2">
                    Błąd panelu administracyjnego
                </h1>
                <p class="text-lg text-gray-600">
                    Wystąpił problem z panelem administracyjnym. Główna aplikacja działa prawidłowo.
                </p>
            </div>

            <!-- Szczegóły błędu (tylko w trybie debug) -->
            @if($showDebug && isset($message))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 mb-2">
                                Szczegóły błędu (tryb debug):
                            </h3>
                            <div class="text-sm text-red-700 font-mono bg-red-100 p-3 rounded overflow-auto max-h-64">
                                {{ $message }}
                                @if(isset($exception))
                                    <div class="mt-2 text-xs text-red-600">
                                        {{ $exception->getFile() }}:{{ $exception->getLine() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Akcje -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/admin') }}"
                   class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Powrót do panelu admina
                </a>

                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Przejdź do strony głównej
                </a>
            </div>

            <!-- Informacja o dostępności -->
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            <strong class="font-medium">Główna aplikacja działa prawidłowo.</strong><br>
                            Użytkownicy mogą nadal korzystać z serwisu PetHelp bez przeszkód.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Wskazówki -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-blue-900 mb-2">Co możesz zrobić?</h3>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>Spróbuj odświeżyć stronę (F5)</li>
                    <li>Wyczyść cache przeglądarki (Ctrl+Shift+R)</li>
                    <li>Sprawdź logi w <code class="bg-blue-100 px-1 rounded">storage/logs/laravel.log</code></li>
                    <li>Jeśli problem się powtarza, skontaktuj się z zespołem technicznym</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
