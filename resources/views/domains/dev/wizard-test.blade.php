<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Sitter Wizard - Testy Kroków</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">🧙‍♀️ Pet Sitter Wizard - Test Kroków</h1>
            <p class="text-lg text-gray-600">Testowe linki do otwierania wizard'a na konkretnych krokach (tylko w trybie deweloperskim)</p>
            <div class="mt-4 p-4 bg-yellow-100 border border-yellow-400 rounded-lg">
                <p class="text-yellow-800"><strong>Uwaga:</strong> Te linki działają tylko w środowisku lokalnym (APP_ENV=local)</p>
            </div>
        </div>

        {{-- Grid z krokami --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($stepNames as $stepNumber => $stepName)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl">{{ $stepNumber === 1 ? '👋' : ($stepNumber === 8 ? '📸' : ($stepNumber === 12 ? '🎯' : '⭐')) }}</span>
                            <span class="bg-emerald-100 text-emerald-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                                Krok {{ $stepNumber }}
                            </span>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $stepName }}</h3>

                        <p class="text-sm text-gray-600 mb-6">
                            @switch($stepNumber)
                                @case(1)
                                    Motywacja i wprowadzenie do procesu rejestracji
                                    @break
                                @case(2)
                                    Doświadczenie użytkownika ze zwierzętami
                                    @break
                                @case(3)
                                    Wybór rodzajów zwierząt do opieki
                                    @break
                                @case(8)
                                    Upload zdjęć profilowych i domu
                                    @break
                                @case(12)
                                    Finalizacja i podgląd profilu
                                    @break
                                @default
                                    Konfiguracja {{ strtolower($stepName) }}
                            @endswitch
                        </p>

                        <a href="{{ route('profile.become-sitter', ['step' => $stepNumber]) }}"
                           class="inline-flex items-center justify-center w-full px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-medium rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200 transform hover:scale-105">
                            <span>Otwórz Krok {{ $stepNumber }}</span>
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Dodatkowe opcje testowe --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">🔧 Dodatkowe Opcje Testowe</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Normalny wizard --}}
                <a href="{{ route('profile.become-sitter') }}"
                   class="flex items-center p-4 border border-gray-300 rounded-lg hover:border-emerald-500 hover:bg-emerald-50 transition-colors duration-200">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">🏠</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Normalny Wizard</h3>
                        <p class="text-sm text-gray-600">Rozpocznij od kroku 1 (standardowy przepływ)</p>
                    </div>
                </a>

                {{-- Quick login + wizard --}}
                <a href="/quick-login-owner"
                   class="flex items-center p-4 border border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors duration-200">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">⚡</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Quick Login (Owner)</h3>
                        <p class="text-sm text-gray-600">Zaloguj jako właściciel zwierzęcia</p>
                    </div>
                </a>

                {{-- Random step --}}
                <a href="{{ route('profile.become-sitter', ['step' => rand(1, 12)]) }}"
                   class="flex items-center p-4 border border-gray-300 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-colors duration-200">
                    <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">🎲</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Random Step</h3>
                        <p class="text-sm text-gray-600">Otwórz losowy krok wizard'a</p>
                    </div>
                </a>

                {{-- Dashboard --}}
                <a href="{{ route('profile.dashboard') }}"
                   class="flex items-center p-4 border border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors duration-200">
                    <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-2xl">🏡</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Dashboard</h3>
                        <p class="text-sm text-gray-600">Wróć do głównego dashboardu</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Instrukcje --}}
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">📋 Instrukcje Testowania</h3>
            <ul class="space-y-2 text-blue-800">
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">1.</span>
                    Upewnij się, że jesteś zalogowany (użyj Quick Login jeśli potrzeba)
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">2.</span>
                    Kliknij na dowolny krok, aby otworzyć wizard bezpośrednio na tym kroku
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">3.</span>
                    Wizard otworzy się w trybie fullscreen z komunikatem o trybie deweloperskim
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">4.</span>
                    Możesz nawigować między krokami lub zamknąć wizard w dowolnym momencie
                </li>
            </ul>
        </div>

        {{-- Powered by info --}}
        <div class="text-center mt-12 text-gray-500 text-sm">
            <p>🚀 Powered by Laravel Livewire & Alpine.js | 🎨 Styled with Tailwind CSS</p>
        </div>
    </div>
</body>
</html>