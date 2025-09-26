{{-- Komponent przycisków szybkiego logowania - tylko środowisko lokalne --}}
@if($localEnvironment)
<div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-medium text-blue-900 dark:text-blue-200">
            🚀 Szybkie logowanie testowe
        </h3>
        <button
            wire:click="toggleExpanded"
            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors"
        >
            {{ $showExpanded ? '🔽 Mniej' : '🔼 Więcej' }}
        </button>
    </div>

    {{-- Podstawowe przyciski --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-3">
        <a href="/quick-login-owner"
           class="inline-flex flex-col items-center justify-center px-3 py-2 text-xs font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-800 dark:text-blue-200 dark:hover:bg-blue-700 rounded-md transition-colors">
            <span class="flex items-center">
                👤 <strong class="ml-1">Właściciel</strong>
            </span>
            @if($users['owners']->count() > 0)
                <span class="text-xs opacity-75 mt-1">{{ $users['owners']->first()['email'] }}</span>
            @else
                <span class="text-xs opacity-75 mt-1">Stworzy testowego</span>
            @endif
        </a>

        <a href="/quick-login-sitter"
           class="inline-flex flex-col items-center justify-center px-3 py-2 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-800 dark:text-green-200 dark:hover:bg-green-700 rounded-md transition-colors">
            <span class="flex items-center">
                🐕 <strong class="ml-1">Pet Sitter</strong>
            </span>
            @if($users['sitters']->count() > 0)
                <span class="text-xs opacity-75 mt-1">{{ $users['sitters']->first()['email'] }}</span>
            @else
                <span class="text-xs opacity-75 mt-1">Stworzy testowego</span>
            @endif
        </a>

        <a href="/quick-login"
           class="inline-flex flex-col items-center justify-center px-3 py-2 text-xs font-medium text-purple-700 bg-purple-100 hover:bg-purple-200 dark:bg-purple-800 dark:text-purple-200 dark:hover:bg-purple-700 rounded-md transition-colors">
            <span class="flex items-center">
                ⭐ <strong class="ml-1">Użytkownik</strong>
            </span>
            @if($users['regular']->count() > 0)
                <span class="text-xs opacity-75 mt-1">{{ $users['regular']->first()['email'] }}</span>
            @else
                <span class="text-xs opacity-75 mt-1">Stworzy testowego</span>
            @endif
        </a>
    </div>

    {{-- Rozszerzona lista użytkowników --}}
    @if($showExpanded)
        <div class="border-t border-blue-200 dark:border-blue-700 pt-3">
            {{-- Właściciele zwierząt --}}
            @if($users['owners']->count() > 0)
                <div class="mb-4">
                    <h4 class="text-xs font-medium text-blue-800 dark:text-blue-300 mb-2">👤 Właściciele zwierząt:</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($users['owners'] as $owner)
                            <a href="/quick-login/{{ $owner['id'] }}"
                               class="block p-2 text-xs bg-blue-50 dark:bg-blue-800/50 hover:bg-blue-100 dark:hover:bg-blue-700/50 rounded border border-blue-200 dark:border-blue-600 transition-colors">
                                <div class="font-medium text-blue-900 dark:text-blue-200">{{ $owner['name'] }}</div>
                                <div class="text-blue-700 dark:text-blue-400">{{ $owner['email'] }}</div>
                                <div class="text-blue-600 dark:text-blue-500 mt-1">{{ $owner['description'] }}</div>
                                @if(isset($owner['pets_names']))
                                    <div class="text-blue-500 dark:text-blue-400 text-xs">{{ $owner['pets_names'] }}</div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Opiekunowie zwierząt --}}
            @if($users['sitters']->count() > 0)
                <div class="mb-4">
                    <h4 class="text-xs font-medium text-green-800 dark:text-green-300 mb-2">🐕 Opiekunowie zwierząt:</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($users['sitters'] as $sitter)
                            <a href="/quick-login/{{ $sitter['id'] }}"
                               class="block p-2 text-xs bg-green-50 dark:bg-green-800/50 hover:bg-green-100 dark:hover:bg-green-700/50 rounded border border-green-200 dark:border-green-600 transition-colors">
                                <div class="font-medium text-green-900 dark:text-green-200">{{ $sitter['name'] }}</div>
                                <div class="text-green-700 dark:text-green-400">{{ $sitter['email'] }}</div>
                                <div class="text-green-600 dark:text-green-500 mt-1">{{ $sitter['description'] }}</div>
                                @if(isset($sitter['services']))
                                    <div class="text-green-500 dark:text-green-400 text-xs">{{ $sitter['services'] }}</div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Zwykli użytkownicy --}}
            @if($users['regular']->count() > 0)
                <div class="mb-2">
                    <h4 class="text-xs font-medium text-purple-800 dark:text-purple-300 mb-2">⭐ Zwykli użytkownicy:</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($users['regular'] as $user)
                            <a href="/quick-login/{{ $user['id'] }}"
                               class="block p-2 text-xs bg-purple-50 dark:bg-purple-800/50 hover:bg-purple-100 dark:hover:bg-purple-700/50 rounded border border-purple-200 dark:border-purple-600 transition-colors">
                                <div class="font-medium text-purple-900 dark:text-purple-200">{{ $user['name'] }}</div>
                                <div class="text-purple-700 dark:text-purple-400">{{ $user['email'] }}</div>
                                <div class="text-purple-600 dark:text-purple-500 mt-1">{{ $user['description'] }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-xs text-blue-600 dark:text-blue-400">
            💡 Kliknij aby zalogować się natychmiast jako wybrany użytkownik
        </p>

        <div class="text-xs text-blue-500 dark:text-blue-400">
            👤 {{ $users['owners']->count() }} właścicieli |
            🐕 {{ $users['sitters']->count() }} opiekunów |
            ⭐ {{ $users['regular']->count() }} użytkowników
        </div>
    </div>
</div>
@endif
