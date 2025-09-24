@props([
    'title' => 'Wiadomości',
    'subtitle' => 'Czat z opiekunami',
    'height' => 'calc(100vh - 8rem)',
    'fullscreen' => false
])

@php
    // Jeśli fullscreen, używamy h-screen, inaczej calc height
    $containerClass = $fullscreen ? 'h-screen' : 'h-full';
    $wrapperStyle = $fullscreen ? '' : "height: {$height};";
@endphp

@if($fullscreen)
    {{-- Tryb fullscreen - bez dashboard layout --}}
    <div class="{{ $containerClass }} bg-gray-100 dark:bg-gray-900">
        <livewire:chat-app />
    </div>
@else
    {{-- Tryb dashboard - z headerem --}}
    @section('title', $title . ' - PetHelp')

    @section('header-title')
        <div class="flex items-center">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h1>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                {{ $subtitle }}
            </span>
        </div>
    @endsection

    <div style="{{ $wrapperStyle }}">
        <livewire:chat-app :full-width="true" />
    </div>
@endif