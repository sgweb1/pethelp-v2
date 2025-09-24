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
        <title>@yield('title', 'Dashboard - ' . config('app.name', 'PetHelp'))</title>

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
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <div class="hidden lg:flex lg:flex-shrink-0">
                <div class="flex flex-col w-64">
                    <x-dashboard.sidebar />
                </div>
            </div>

            <!-- Main content -->
            <div class="flex-1 overflow-hidden flex flex-col">
                <!-- Top navigation -->
                <x-dashboard.header />

                <!-- Page content -->
                <main class="flex-1 relative overflow-y-auto focus:outline-none">
                    <div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                            @if(View::hasSection('content'))
                                @yield('content')
                            @else
                                {{ $slot }}
                            @endif
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Mobile menu -->
        <x-dashboard.mobile-menu />

        <!-- Global notification components -->
        <livewire:notification-toast />
        <livewire:confirmation-modal />

        @livewireScripts
        @stack('scripts')
    </body>
</html>