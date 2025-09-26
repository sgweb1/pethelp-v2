<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Mobile app meta tags -->
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'PetHelp') }}">
        <meta name="theme-color" content="#3b82f6">
        <meta name="format-detection" content="telephone=no">

        <!-- Title -->
        <title>@yield('title', config('app.name', 'PetHelp'))</title>

        <!-- Optional Meta Section -->
        @if(View::hasSection('meta'))
            @yield('meta')
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="desktop-container min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-warm-600 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <!-- Navigation Header -->
            <x-layout.navbar :breadcrumbs="$breadcrumbs ?? null" />

            <!-- Page Content -->
            <main class="desktop-window">
            @if(View::hasSection('content'))
                @yield('content')
            @else
                {{ $slot }}
            @endif
            </main>
        </div>

        <!-- Alpine.js Store for Mobile Menu is defined in alpine-components.js -->

        <!-- Global notification components -->
        <livewire:notification-toast />
        <livewire:confirmation-modal />

        @livewireScripts
        @stack('scripts')
    </body>
</html>
