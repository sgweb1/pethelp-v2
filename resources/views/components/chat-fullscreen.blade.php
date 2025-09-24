{{--
    Komponent czatu w trybie pełnoekranowym.

    Używaj gdy potrzebujesz czat jako standalone stronę
    bez dashboard layout (np. w modalu, iframe, etc.)
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Czat - {{ config('app.name', 'PetHelp') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <livewire:chat-app />

    @livewireScripts
</body>
</html>