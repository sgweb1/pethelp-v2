<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Przekierowanie do PayU - PetHelp</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <!-- Logo -->
                <div class="mx-auto w-16 h-16 bg-primary-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Przekierowanie do PayU
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mb-8">
                    Za chwilę zostaniesz przekierowany do bezpiecznej strony płatności PayU
                </p>

                <!-- Loading animation -->
                <div class="flex justify-center mb-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
                </div>

                <!-- Auto-submit form -->
                <form id="payu-form" method="POST" action="{{ $redirectUrl }}" class="hidden" onsubmit="console.log('Form submitting to PayU...');">
                    @foreach($formData as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>

                <!-- Manual submit button (fallback) -->
                <button id="manual-submit"
                        onclick="document.getElementById('payu-form').submit()"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
                        style="display: none;">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    Przejdź do płatności
                </button>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                    Jeśli nie zostaniesz automatycznie przekierowany w ciągu 10 sekund, kliknij przycisk powyżej
                </p>
            </div>

            <!-- Security info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            Bezpieczne połączenie SSL
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Twoje dane są chronione przez PayU
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ensure no Livewire/JavaScript interference
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, preparing PayU form submission...');

            // Debug info for development
            @if(config('app.debug'))
            console.log('PayU Form Data:', @json($formData));
            console.log('PayU URL:', '{{ $redirectUrl }}');
            @endif

            const form = document.getElementById('payu-form');

            // Prevent any event listeners from interfering
            form.addEventListener('submit', function(e) {
                console.log('Form submitting to PayU...');
                console.log('Target URL:', form.action);
                console.log('Method:', form.method);

                @if(config('app.debug'))
                const formData = new FormData(form);
                for (let [key, value] of formData.entries()) {
                    console.log(key + ':', value);
                }
                @endif

                // Let the form submit normally - don't prevent default
                return true;
            });

            // Auto-submit form after 3 seconds
            setTimeout(function() {
                console.log('Auto-submitting form to PayU...');
                form.submit();
            }, 3000);

            // Show manual submit button after 10 seconds
            setTimeout(function() {
                document.getElementById('manual-submit').style.display = 'inline-flex';
            }, 10000);
        });
    </script>
</body>
</html>