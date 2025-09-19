<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'PetHelp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="desktop-container min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-warm-600 flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo Section -->
            <div class="mb-8">
                <a href="/" wire:navigate class="flex items-center group">
                    <div class="w-12 h-12 mr-4 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center group-hover:bg-white/30 transition-colors duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-white group-hover:text-white/90 transition-colors duration-300">PetHelp</span>
                </a>
            </div>

            <!-- Auth Card -->
            <div class="w-full sm:max-w-md bg-white/95 backdrop-blur-md shadow-large px-8 py-10 rounded-3xl border border-white/20 desktop-form">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-white/80 text-sm">
                    Bezpieczna platforma opieki nad zwierzętami
                </p>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
