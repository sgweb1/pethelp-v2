<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🤖 Innowacje AI w Pet Sitter Wizard - PetHelp</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Custom presentation styles */
        .gradient-hero {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #10b981, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-card {
            transition: all 0.3s ease;
        }

        .section-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .code-block {
            background: #1e293b;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .ai-badge {
            background: linear-gradient(135deg, #10b981, #14b8a6);
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .8; }
        }

        .feature-icon {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Navigation dots */
        .nav-dot {
            transition: all 0.3s ease;
        }

        .nav-dot:hover {
            transform: scale(1.2);
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none;
            }

            .section-card {
                page-break-inside: avoid;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Code syntax highlighting colors */
        .code-keyword { color: #c678dd; }
        .code-string { color: #98c379; }
        .code-function { color: #61afef; }
        .code-comment { color: #5c6370; }
        .code-tag { color: #e06c75; }
        .code-attr { color: #d19a66; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    {{-- Navigation Bar --}}
    <nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-sm shadow-sm z-50 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="text-3xl">🤖</div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Innowacje AI</h1>
                        <p class="text-xs text-gray-500">Pet Sitter Wizard</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="#przeglad" class="text-sm text-gray-600 hover:text-emerald-600 transition-colors">Przegląd</a>
                    <a href="#assistant" class="text-sm text-gray-600 hover:text-emerald-600 transition-colors">Assistant</a>
                    <a href="#pricing" class="text-sm text-gray-600 hover:text-emerald-600 transition-colors">Pricing</a>
                    <a href="#architektura" class="text-sm text-gray-600 hover:text-emerald-600 transition-colors">Architektura</a>
                    <button onclick="window.print()" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-sm">
                        Drukuj
                    </button>
                </div>

                {{-- Mobile menu button --}}
                <button class="md:hidden p-2" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-200 px-4 py-3">
            <a href="#przeglad" class="block py-2 text-sm text-gray-600 hover:text-emerald-600">Przegląd</a>
            <a href="#assistant" class="block py-2 text-sm text-gray-600 hover:text-emerald-600">Assistant</a>
            <a href="#pricing" class="block py-2 text-sm text-gray-600 hover:text-emerald-600">Pricing</a>
            <a href="#architektura" class="block py-2 text-sm text-gray-600 hover:text-emerald-600">Architektura</a>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="gradient-hero text-white pt-32 pb-20 px-4">
        <div class="max-w-6xl mx-auto text-center">
            <div class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full mb-6 animate-fade-in-up">
                <span class="text-sm font-medium">✨ Production Ready • v1.0.0</span>
            </div>

            <h1 class="text-5xl md:text-6xl font-bold mb-6 animate-fade-in-up" style="animation-delay: 0.1s;">
                🤖 Innowacje AI w<br>Pet Sitter Wizard
            </h1>

            <p class="text-xl md:text-2xl text-emerald-50 mb-8 max-w-3xl mx-auto animate-fade-in-up" style="animation-delay: 0.2s;">
                Zaawansowane technologie AI wspierające użytkowników w procesie rejestracji
            </p>

            <div class="flex flex-wrap justify-center gap-4 mb-12 animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="bg-white/10 backdrop-blur-sm px-6 py-3 rounded-xl">
                    <div class="text-2xl font-bold">4+</div>
                    <div class="text-sm text-emerald-100">Funkcje AI</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm px-6 py-3 rounded-xl">
                    <div class="text-2xl font-bold">3</div>
                    <div class="text-sm text-emerald-100">AI Services</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm px-6 py-3 rounded-xl">
                    <div class="text-2xl font-bold">60s</div>
                    <div class="text-sm text-emerald-100">Cache Time</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm px-6 py-3 rounded-xl">
                    <div class="text-2xl font-bold">100%</div>
                    <div class="text-sm text-emerald-100">Uptime Ready</div>
                </div>
            </div>

            <a href="#przeglad" class="inline-flex items-center px-8 py-4 bg-white text-emerald-600 rounded-xl font-semibold hover:bg-emerald-50 transform hover:scale-105 transition-all shadow-xl animate-fade-in-up" style="animation-delay: 0.4s;">
                <span>Rozpocznij prezentację</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </a>
        </div>
    </section>

    {{-- Main Content --}}
    <div class="max-w-6xl mx-auto px-4 py-16">

        {{-- Przegląd systemu AI --}}
        <section id="przeglad" class="mb-20 scroll-mt-20">
            <div class="text-center mb-12">
                <div class="inline-flex items-center space-x-2 mb-4">
                    <span class="text-4xl">🎯</span>
                    <h2 class="text-4xl font-bold gradient-text">Przegląd systemu AI</h2>
                </div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Pet Sitter Wizard wykorzystuje zaawansowane technologie AI do wspierania użytkowników w procesie rejestracji
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-12">
                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center text-white text-2xl mb-4">
                        ✅
                    </div>
                    <h3 class="text-xl font-bold mb-3">Redukcja friction</h3>
                    <p class="text-gray-600">Zmniejszenie wysiłku potrzebnego do wypełnienia formularza dzięki inteligentnym sugestiom i automatycznemu uzupełnianiu</p>
                </div>

                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center text-white text-2xl mb-4">
                        ✅
                    </div>
                    <h3 class="text-xl font-bold mb-3">Zwiększenie konwersji</h3>
                    <p class="text-gray-600">Więcej ukończonych rejestracji dzięki prowadzeniu użytkownika przez proces krok po kroku</p>
                </div>

                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center text-white text-2xl mb-4">
                        ✅
                    </div>
                    <h3 class="text-xl font-bold mb-3">Poprawa jakości profili</h3>
                    <p class="text-gray-600">Lepsze, bardziej szczegółowe opisy generowane przez AI z personalizacją</p>
                </div>

                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center text-white text-2xl mb-4">
                        ✅
                    </div>
                    <h3 class="text-xl font-bold mb-3">Wsparcie decyzyjne</h3>
                    <p class="text-gray-600">Pomoc w ustalaniu cen i strategii na podstawie analizy rynku lokalnego</p>
                </div>
            </div>
        </section>

        {{-- AI Assistant Panel --}}
        <section id="assistant" class="mb-20 scroll-mt-20">
            <div class="text-center mb-12">
                <div class="inline-flex items-center space-x-2 mb-4">
                    <span class="text-4xl">💡</span>
                    <h2 class="text-4xl font-bold gradient-text">AI Assistant Panel</h2>
                </div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Inteligentny panel z kontekstowymi wskazówkami wysuwa się z prawej strony ekranu
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-emerald-500 to-cyan-500 p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Slide-out Panel z Wskazówkami</h3>
                    <p class="text-emerald-50">Aktywny w krokach: 1, 2, 5, 6, 9, 10</p>
                </div>

                <div class="p-8">
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                1
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-lg mb-2">Krok 1 - Wprowadzenie</h4>
                                <ul class="space-y-1 text-gray-600">
                                    <li>• Proces rejestracji (4 fazy)</li>
                                    <li>• Szacowany czas (15-20 minut)</li>
                                    <li>• Wymagane dokumenty</li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-bold">
                                6
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-lg mb-2">Krok 6 - Opis motywacji</h4>
                                <div class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium mb-2">
                                    🤖 AI-powered generation
                                </div>
                                <ul class="space-y-1 text-gray-600">
                                    <li>• Wskazówki jak pisać przekonujący opis</li>
                                    <li>• Przykłady dobrych opisów</li>
                                    <li>• Real-time suggestions</li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-cyan-100 flex items-center justify-center text-cyan-600 font-bold">
                                10
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-lg mb-2">Krok 10 - Cennik</h4>
                                <div class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium mb-2">
                                    📊 Market Analysis
                                </div>
                                <ul class="space-y-1 text-gray-600">
                                    <li>• Strategie cenowe (Budget/Competitive/Premium)</li>
                                    <li>• Analiza cen w okolicy</li>
                                    <li>• Szacowane zarobki miesięczne</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Przykład implementacji --}}
            <div class="bg-gray-900 rounded-2xl overflow-hidden shadow-xl">
                <div class="bg-gray-800 px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <span class="text-gray-400 text-sm">step-6.blade.php</span>
                </div>
                <div class="p-6 overflow-x-auto">
                    <pre class="text-sm text-gray-300 leading-relaxed"><code><span class="code-comment">{{-- Button w nagłówku kroku --}}</span>
<span class="code-tag">&lt;button</span> <span class="code-attr">type=</span><span class="code-string">"button"</span> <span class="code-attr">@click=</span><span class="code-string">"$wire.showAIPanel = !$wire.showAIPanel"</span>
        <span class="code-attr">class=</span><span class="code-string">"flex items-center text-sm cursor-pointer
               hover:scale-105 transition-transform"</span><span class="code-tag">&gt;</span>
    <span class="code-tag">&lt;div</span> <span class="code-attr">class=</span><span class="code-string">"w-6 h-6 bg-gradient-to-r
                from-emerald-500 to-green-500 rounded-full"</span><span class="code-tag">&gt;</span>
        <span class="code-tag">&lt;span</span> <span class="code-attr">class=</span><span class="code-string">"text-white text-xs"</span><span class="code-tag">&gt;</span>💡<span class="code-tag">&lt;/span&gt;</span>
    <span class="code-tag">&lt;/div&gt;</span>
    <span class="code-tag">&lt;span</span> <span class="code-attr">x-text=</span><span class="code-string">"$wire.showAIPanel ?
                   'Ukryj wskazówki' :
                   'Analiza &amp; Wskazówki'"</span><span class="code-tag">&gt;&lt;/span&gt;</span>
<span class="code-tag">&lt;/button&gt;</span></code></pre>
                </div>
            </div>
        </section>

        {{-- Inteligentne sugestie tekstowe --}}
        <section id="suggestions" class="mb-20 scroll-mt-20">
            <div class="text-center mb-12">
                <div class="inline-flex items-center space-x-2 mb-4">
                    <span class="text-4xl">✍️</span>
                    <h2 class="text-4xl font-bold gradient-text">Inteligentne sugestie tekstowe</h2>
                </div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    AI generuje spersonalizowane opisy motywacji i doświadczenia
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 mb-8">
                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">AI Generator opisów motywacji</h3>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">Krok 6</span>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2"><strong>Technologia:</strong></p>
                            <p class="text-sm">HybridAIAssistant - serwis hybrydowy łączący local AI i API</p>
                        </div>

                        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg">
                            <p class="text-sm font-medium text-emerald-900 mb-2">Przykład wygenerowanego tekstu:</p>
                            <p class="text-sm text-gray-700 italic">
                                "Cześć! Nazywam się Anna i kocham zwierzęta od dziecka. Przez ostatnie 5 lat opiekowałam się psami różnych ras - od małych yorków po duże Golden Retrievery. Mam doświadczenie w spacerach, karmieniu i podawaniu leków..."
                            </p>
                        </div>

                        <div class="flex space-x-2">
                            <div class="flex-1 bg-blue-50 p-3 rounded-lg text-center">
                                <div class="text-xs text-blue-600 font-medium">Min</div>
                                <div class="text-lg font-bold text-blue-900">100</div>
                                <div class="text-xs text-blue-600">znaków</div>
                            </div>
                            <div class="flex-1 bg-purple-50 p-3 rounded-lg text-center">
                                <div class="text-xs text-purple-600 font-medium">Max</div>
                                <div class="text-lg font-bold text-purple-900">500</div>
                                <div class="text-xs text-purple-600">znaków</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">AI Generator opisów doświadczenia</h3>
                        <span class="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm font-medium">Krok 7</span>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2"><strong>Funkcjonalność:</strong></p>
                            <p class="text-sm">Rozbudowana wersja generatora z bardziej szczegółowymi promptami i dłuższym outputem</p>
                        </div>

                        <div class="bg-teal-50 border border-teal-200 p-4 rounded-lg">
                            <p class="text-sm font-medium text-teal-900 mb-2">Uwzględnia:</p>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>✓ Lata doświadczenia</li>
                                <li>✓ Specjalizacje</li>
                                <li>✓ Konkretne przykłady</li>
                            </ul>
                        </div>

                        <div class="flex space-x-2">
                            <div class="flex-1 bg-blue-50 p-3 rounded-lg text-center">
                                <div class="text-xs text-blue-600 font-medium">Min</div>
                                <div class="text-lg font-bold text-blue-900">100</div>
                                <div class="text-xs text-blue-600">znaków</div>
                            </div>
                            <div class="flex-1 bg-purple-50 p-3 rounded-lg text-center">
                                <div class="text-xs text-purple-600 font-medium">Max</div>
                                <div class="text-lg font-bold text-purple-900">1000</div>
                                <div class="text-xs text-purple-600">znaków</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Przepływ działania --}}
            <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl p-8 text-white shadow-xl">
                <h3 class="text-2xl font-bold mb-6 text-center">Przepływ działania AI Generator</h3>
                <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 md:space-x-4">
                    <div class="flex-1 text-center">
                        <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-2xl">👤</span>
                        </div>
                        <p class="text-sm">Klik "Generuj z AI"</p>
                    </div>
                    <div class="text-gray-400">→</div>
                    <div class="flex-1 text-center">
                        <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-2xl">📊</span>
                        </div>
                        <p class="text-sm">Zbiera kontekst</p>
                    </div>
                    <div class="text-gray-400">→</div>
                    <div class="flex-1 text-center">
                        <div class="w-12 h-12 bg-cyan-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-2xl">🤖</span>
                        </div>
                        <p class="text-sm">Wywołuje AI</p>
                    </div>
                    <div class="text-gray-400">→</div>
                    <div class="flex-1 text-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-2xl">✨</span>
                        </div>
                        <p class="text-sm">Generuje tekst</p>
                    </div>
                    <div class="text-gray-400">→</div>
                    <div class="flex-1 text-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-2xl">✏️</span>
                        </div>
                        <p class="text-sm">Użytkownik edytuje</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Analiza rynku cen --}}
        <section id="pricing" class="mb-20 scroll-mt-20">
            <div class="text-center mb-12">
                <div class="inline-flex items-center space-x-2 mb-4">
                    <span class="text-4xl">📊</span>
                    <h2 class="text-4xl font-bold gradient-text">Analiza rynku cen - AI Pricing</h2>
                </div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Inteligentny system analizy cen bazujący na rzeczywistych danych z bazy i lokalizacji
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">PricingAnalysisService</h3>
                    <p class="text-purple-50">Real-time market analysis w promieniu 20km</p>
                </div>

                <div class="p-8">
                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                            <div class="text-3xl mb-2">🌍</div>
                            <div class="text-2xl font-bold text-blue-900">20 km</div>
                            <div class="text-sm text-blue-600">Promień analizy</div>
                        </div>
                        <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                            <div class="text-3xl mb-2">⚡</div>
                            <div class="text-2xl font-bold text-green-900">60 min</div>
                            <div class="text-sm text-green-600">Cache time</div>
                        </div>
                        <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                            <div class="text-3xl mb-2">📈</div>
                            <div class="text-2xl font-bold text-purple-900">3+ próbek</div>
                            <div class="text-sm text-purple-600">Min. reliable data</div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="border-l-4 border-emerald-500 pl-6">
                            <h4 class="font-bold text-lg mb-2">Algorytm Haversine</h4>
                            <p class="text-gray-600 text-sm mb-3">Obliczanie odległości geograficznych z precyzją GPS</p>
                            <div class="bg-gray-900 p-4 rounded-lg overflow-x-auto">
                                <code class="text-xs text-green-400 font-mono">
distance = 6371 * acos(cos(lat1) * cos(lat2) * cos(lon2 - lon1) + sin(lat1) * sin(lat2))
                                </code>
                            </div>
                        </div>

                        <div class="border-l-4 border-teal-500 pl-6">
                            <h4 class="font-bold text-lg mb-2">Strategie cenowe</h4>
                            <div class="grid md:grid-cols-3 gap-4 mt-4">
                                <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg">
                                    <div class="text-2xl mb-2">💡</div>
                                    <h5 class="font-bold text-amber-900 mb-1">Budżetowa</h5>
                                    <p class="text-sm text-amber-700 mb-2">-20% średniej</p>
                                    <div class="text-xs text-amber-600">Niższe ceny, więcej klientów</div>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                    <div class="text-2xl mb-2">⚖️</div>
                                    <h5 class="font-bold text-blue-900 mb-1">Konkurencyjna</h5>
                                    <p class="text-sm text-blue-700 mb-2">100% średniej</p>
                                    <div class="text-xs text-blue-600">Ceny na poziomie rynkowym</div>
                                </div>
                                <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg">
                                    <div class="text-2xl mb-2">💎</div>
                                    <h5 class="font-bold text-purple-900 mb-1">Premium</h5>
                                    <p class="text-sm text-purple-700 mb-2">+30% średniej</p>
                                    <div class="text-xs text-purple-600">Wyższe ceny, premium service</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Przykład wyświetlania --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold">📊 Analiza cen w Twojej okolicy</h3>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        Live Data
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-emerald-500">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-medium">Spacery z psem: <span class="ml-2 text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded">✓ Real</span></div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 mb-1">25-45 PLN/h <span class="text-sm text-gray-500">(śr: 35)</span></div>
                        <div class="text-sm text-gray-500 mb-2">na podstawie 15 opiekunów</div>
                        <div class="text-sm">
                            <span class="font-semibold text-purple-700">Twoja cena: </span>
                            <span>30 PLN/h</span>
                            <span class="text-amber-600 ml-1" title="Poniżej średniej">↓</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-teal-500">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-medium">Opieka w domu: <span class="ml-2 text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded">✓ Real</span></div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 mb-1">20-35 PLN/h <span class="text-sm text-gray-500">(śr: 28)</span></div>
                        <div class="text-sm text-gray-500 mb-2">na podstawie 12 opiekunów</div>
                        <div class="text-sm">
                            <span class="font-semibold text-purple-700">Twoja cena: </span>
                            <span>28 PLN/h</span>
                            <span class="text-blue-600 ml-1" title="Na poziomie średniej">≈</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800 flex items-center">
                        <span class="text-xl mr-2">🟢</span>
                        <strong>Wysoka jakość danych</strong> - analiza oparta na 45 próbkach
                    </p>
                </div>
            </div>
        </section>

        {{-- Personalizowane rekomendacje --}}
        <section id="rekomendacje" class="mb-20 scroll-mt-20">
            <div class="text-center mb-12">
                <div class="inline-flex items-center space-x-2 mb-4">
                    <span class="text-4xl">💰</span>
                    <h2 class="text-4xl font-bold gradient-text">Personalizowane rekomendacje</h2>
                </div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Revenue optimization i szacowane zarobki miesięczne
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <span class="text-2xl mr-2">💰</span>
                        Kalkulator miesięcznych zarobków
                    </h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Spacery (24h/miesiąc):</span>
                            <span class="font-bold text-emerald-600">720 PLN</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Opieka w domu (8h/miesiąc):</span>
                            <span class="font-bold text-emerald-600">200 PLN</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Opieka nocna (3 noce/miesiąc):</span>
                            <span class="font-bold text-emerald-600">360 PLN</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between items-center">
                            <span class="font-bold text-gray-900">Łącznie:</span>
                            <span class="text-2xl font-bold text-emerald-600">1,280 PLN</span>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                        <p class="text-xs text-blue-700">*Szacunek konserwatywny przy niskiej aktywności</p>
                    </div>
                </div>

                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <span class="text-2xl mr-2">💡</span>
                        Zwiększ swoje zarobki
                    </h3>

                    <p class="text-sm text-gray-600 mb-4">Dodając poniższe usługi, możesz dodatkowo zarobić:</p>

                    <div class="space-y-3 mb-6">
                        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium text-gray-900">Opieka nocna</div>
                                    <div class="text-xs text-gray-500">3 noce/miesiąc</div>
                                </div>
                                <div class="text-xl font-bold text-emerald-600">+360 PLN</div>
                            </div>
                        </div>
                        <div class="bg-teal-50 border border-teal-200 p-4 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium text-gray-900">Transport zwierząt</div>
                                    <div class="text-xs text-gray-500">8 wyjazdów</div>
                                </div>
                                <div class="text-xl font-bold text-teal-600">+160 PLN</div>
                            </div>
                        </div>
                        <div class="bg-cyan-50 border border-cyan-200 p-4 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium text-gray-900">Wizyty u weterynarza</div>
                                    <div class="text-xs text-gray-500">2 wizyty</div>
                                </div>
                                <div class="text-xl font-bold text-cyan-600">+100 PLN</div>
                            </div>
                        </div>
                    </div>

                    <button class="w-full px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl font-semibold hover:from-emerald-600 hover:to-teal-600 transition-all transform hover:scale-105">
                        + Dodaj usługi w kroku 4
                    </button>
                </div>
            </div>
        </section>

        {{-- Architektura techniczna --}}
        <section id="architektura" class="mb-20 scroll-mt-20">
            <div class="text-center mb-12">
                <div class="inline-flex items-center space-x-2 mb-4">
                    <span class="text-4xl">🏗️</span>
                    <h2 class="text-4xl font-bold gradient-text">Architektura techniczna</h2>
                </div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Struktura plików i flow danych w systemie AI
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-6 text-white">
                    <h3 class="text-2xl font-bold">Struktura plików</h3>
                </div>

                <div class="p-8">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-6 rounded-xl">
                            <h4 class="font-bold mb-4 flex items-center">
                                <span class="text-xl mr-2">🔧</span>
                                Services
                            </h4>
                            <div class="space-y-2 text-sm font-mono">
                                <div class="flex items-start">
                                    <span class="text-emerald-500 mr-2">├─</span>
                                    <div>
                                        <div class="font-medium">HybridAIAssistant.php</div>
                                        <div class="text-xs text-gray-500 font-sans">Główny serwis AI</div>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-emerald-500 mr-2">├─</span>
                                    <div>
                                        <div class="font-medium">LocalAIAssistant.php</div>
                                        <div class="text-xs text-gray-500 font-sans">Local AI (fallback)</div>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-emerald-500 mr-2">├─</span>
                                    <div>
                                        <div class="font-medium">RuleEngine.php</div>
                                        <div class="text-xs text-gray-500 font-sans">Rule-based generation</div>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-emerald-500 mr-2">└─</span>
                                    <div>
                                        <div class="font-medium">PricingAnalysisService.php</div>
                                        <div class="text-xs text-gray-500 font-sans">Analiza rynku cen</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-xl">
                            <h4 class="font-bold mb-4 flex items-center">
                                <span class="text-xl mr-2">⚡</span>
                                Components
                            </h4>
                            <div class="space-y-2 text-sm font-mono">
                                <div class="flex items-start">
                                    <span class="text-purple-500 mr-2">├─</span>
                                    <div>
                                        <div class="font-medium">PetSitterWizard.php</div>
                                        <div class="text-xs text-gray-500 font-sans">Główny komponent Livewire</div>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-purple-500 mr-2">├─</span>
                                    <div>
                                        <div class="font-medium">wizard-step-6-v3.js</div>
                                        <div class="text-xs text-gray-500 font-sans">Krok motywacji z AI</div>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-purple-500 mr-2">├─</span>
                                    <div>
                                        <div class="font-medium">wizard-step-10-v3.js</div>
                                        <div class="text-xs text-gray-500 font-sans">Krok cennika z AI</div>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-purple-500 mr-2">└─</span>
                                    <div>
                                        <div class="font-medium">wizard-state-manager-v3.js</div>
                                        <div class="text-xs text-gray-500 font-sans">Centralized state</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flow diagram --}}
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 text-white shadow-xl">
                <h3 class="text-2xl font-bold mb-8 text-center">Flow danych</h3>

                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-48 bg-blue-500/20 border border-blue-400 rounded-lg p-3 text-center hover:bg-blue-500/30 transition-colors">
                            <div class="font-medium">User Input</div>
                        </div>
                        <div class="text-2xl">→</div>
                        <div class="flex-1 text-sm text-gray-400">Dane użytkownika</div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-48 bg-purple-500/20 border border-purple-400 rounded-lg p-3 text-center hover:bg-purple-500/30 transition-colors">
                            <div class="font-medium">Alpine.js Component</div>
                        </div>
                        <div class="text-2xl">→</div>
                        <div class="flex-1 text-sm text-gray-400">Frontend logic</div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-48 bg-indigo-500/20 border border-indigo-400 rounded-lg p-3 text-center hover:bg-indigo-500/30 transition-colors">
                            <div class="font-medium">WizardStateManager</div>
                        </div>
                        <div class="text-2xl">→</div>
                        <div class="flex-1 text-sm text-gray-400">Centralized state</div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-48 bg-teal-500/20 border border-teal-400 rounded-lg p-3 text-center hover:bg-teal-500/30 transition-colors">
                            <div class="font-medium">Livewire Component</div>
                        </div>
                        <div class="text-2xl">→</div>
                        <div class="flex-1 text-sm text-gray-400">Backend controller</div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-48 bg-emerald-500/20 border border-emerald-400 rounded-lg p-3 text-center hover:bg-emerald-500/30 transition-colors">
                            <div class="text-sm">HybridAIAssistant</div>
                            <div class="text-xs text-gray-400">PricingAnalysisService</div>
                        </div>
                        <div class="text-2xl">→</div>
                        <div class="flex-1 text-sm text-gray-400">AI Services</div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-48 bg-yellow-500/20 border border-yellow-400 rounded-lg p-3 text-center hover:bg-yellow-500/30 transition-colors">
                            <div class="font-medium">Database</div>
                        </div>
                        <div class="text-2xl">→</div>
                        <div class="flex-1 text-sm text-gray-400">Persistence layer</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Performance & Error Handling --}}
        <section id="performance" class="mb-20 scroll-mt-20">
            <div class="text-center mb-12">
                <div class="inline-flex items-center space-x-2 mb-4">
                    <span class="text-4xl">⚡</span>
                    <h2 class="text-4xl font-bold gradient-text">Performance & Error Handling</h2>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <span class="text-2xl mr-2">🚀</span>
                        Optymalizacja wydajności
                    </h3>

                    <div class="space-y-4">
                        <div class="border-l-4 border-blue-500 pl-4 hover:bg-blue-50 p-3 rounded-r transition-colors">
                            <h4 class="font-bold mb-1">Lazy Loading</h4>
                            <p class="text-sm text-gray-600">Analiza cen ładuje się tylko przy wejściu na krok 10</p>
                        </div>
                        <div class="border-l-4 border-green-500 pl-4 hover:bg-green-50 p-3 rounded-r transition-colors">
                            <h4 class="font-bold mb-1">Optimistic UI</h4>
                            <p class="text-sm text-gray-600">Zmiany pokazują się natychmiast, sync w tle</p>
                        </div>
                        <div class="border-l-4 border-purple-500 pl-4 hover:bg-purple-50 p-3 rounded-r transition-colors">
                            <h4 class="font-bold mb-1">Smart Caching</h4>
                            <p class="text-sm text-gray-600">60 minut cache dla analizy cenowej</p>
                        </div>
                        <div class="border-l-4 border-orange-500 pl-4 hover:bg-orange-50 p-3 rounded-r transition-colors">
                            <h4 class="font-bold mb-1">Database Indexing</h4>
                            <p class="text-sm text-gray-600">Indeksy na latitude, longitude i pricing</p>
                        </div>
                    </div>
                </div>

                <div class="section-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <span class="text-2xl mr-2">🛡️</span>
                        Graceful Degradation
                    </h3>

                    <div class="space-y-4">
                        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <span class="text-xl mr-2">✓</span>
                                <span class="font-bold text-emerald-900">AI Available</span>
                            </div>
                            <p class="text-sm text-gray-600">Pełna funkcjonalność AI generation</p>
                        </div>

                        <div class="text-center text-gray-400 font-bold">↓ Fallback</div>

                        <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <span class="text-xl mr-2">⚙️</span>
                                <span class="font-bold text-blue-900">Rule-based Engine</span>
                            </div>
                            <p class="text-sm text-gray-600">Template-based generation</p>
                        </div>

                        <div class="text-center text-gray-400 font-bold">↓ Fallback</div>

                        <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <span class="text-xl mr-2">📝</span>
                                <span class="font-bold text-gray-900">Static Templates</span>
                            </div>
                            <p class="text-sm text-gray-600">Podstawowe szablony</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Przyszłe rozszerzenia --}}
        <section id="future" class="mb-20 scroll-mt-20">
            <div class="text-center mb-12">
                <div class="inline-flex items-center space-x-2 mb-4">
                    <span class="text-4xl">🔮</span>
                    <h2 class="text-4xl font-bold gradient-text">Przyszłe rozszerzenia AI</h2>
                </div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Planowane funkcje na kolejne iteracje systemu
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="section-card bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="text-3xl mb-3">🤖</div>
                    <h3 class="text-lg font-bold mb-2">ML Model przewidywania</h3>
                    <p class="text-sm text-gray-600">Analiza kompletności profilu i przewidywanie liczby rezerwacji</p>
                </div>

                <div class="section-card bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="text-3xl mb-3">📝</div>
                    <h3 class="text-lg font-bold mb-2">Natural Language Processing</h3>
                    <p class="text-sm text-gray-600">Sentiment analysis i keyword extraction dla opisów</p>
                </div>

                <div class="section-card bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="text-3xl mb-3">📸</div>
                    <h3 class="text-lg font-bold mb-2">Computer Vision</h3>
                    <p class="text-sm text-gray-600">Automatyczna ocena jakości zdjęć i detekcja zwierząt</p>
                </div>

                <div class="section-card bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="text-3xl mb-3">💬</div>
                    <h3 class="text-lg font-bold mb-2">Chatbot AI Assistant</h3>
                    <p class="text-sm text-gray-600">Interaktywna pomoc w czasie rzeczywistym</p>
                </div>

                <div class="section-card bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="text-3xl mb-3">💰</div>
                    <h3 class="text-lg font-bold mb-2">Dynamic Pricing</h3>
                    <p class="text-sm text-gray-600">Analiza popytu i sugestie zmian cen sezonowych</p>
                </div>

                <div class="section-card bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="text-3xl mb-3">📊</div>
                    <h3 class="text-lg font-bold mb-2">Advanced Analytics</h3>
                    <p class="text-sm text-gray-600">Dashboard z KPI i metrykami sukcesu AI</p>
                </div>
            </div>
        </section>

        {{-- Wnioski --}}
        <section id="wnioski" class="mb-20">
            <div class="bg-gradient-to-r from-emerald-500 via-teal-600 to-cyan-600 rounded-2xl p-12 text-white text-center shadow-2xl">
                <h2 class="text-4xl font-bold mb-6">Wnioski</h2>
                <p class="text-xl text-emerald-50 mb-8 max-w-3xl mx-auto">
                    System AI w Pet Sitter Wizard to kompleksowe rozwiązanie wspierające użytkowników na każdym etapie rejestracji
                </p>

                <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-colors">
                        <div class="text-3xl mb-2">✅</div>
                        <div class="font-bold mb-1">Kontekstowe sugestie</div>
                        <div class="text-sm text-emerald-100">Dostosowane do użytkownika</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-colors">
                        <div class="text-3xl mb-2">✅</div>
                        <div class="font-bold mb-1">Analiza rynku</div>
                        <div class="text-sm text-emerald-100">Real-time data</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-colors">
                        <div class="text-3xl mb-2">✅</div>
                        <div class="font-bold mb-1">Revenue optimization</div>
                        <div class="text-sm text-emerald-100">Maksymalizacja zarobków</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-colors">
                        <div class="text-3xl mb-2">✅</div>
                        <div class="font-bold mb-1">Graceful degradation</div>
                        <div class="text-sm text-emerald-100">Zawsze działa</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-colors">
                        <div class="text-3xl mb-2">✅</div>
                        <div class="font-bold mb-1">Performance</div>
                        <div class="text-sm text-emerald-100">Cache & optimization</div>
                    </div>
                </div>

                <div class="inline-block px-6 py-3 bg-white text-emerald-600 rounded-full font-bold text-lg shadow-xl hover:scale-105 transition-transform">
                    ✅ Production Ready
                </div>
            </div>
        </section>

    </div>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white py-12 px-4">
        <div class="max-w-6xl mx-auto text-center">
            <div class="text-4xl mb-4">🐾</div>
            <h3 class="text-2xl font-bold mb-2">PetHelp - Pet Sitter Wizard</h3>
            <p class="text-gray-400 mb-6">Innowacje AI wspierające rejestrację opiekunów</p>

            <div class="flex justify-center space-x-6 text-sm text-gray-400">
                <div>Wersja: 1.0.0</div>
                <div>•</div>
                <div>Data: 2025-09-30</div>
                <div>•</div>
                <div>Autor: Claude AI Assistant</div>
            </div>
        </div>
    </footer>

    {{-- Scroll to top button --}}
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="no-print fixed bottom-8 right-8 w-12 h-12 bg-emerald-500 text-white rounded-full shadow-lg hover:bg-emerald-600 transition-all flex items-center justify-center z-50 hover:scale-110">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>

    {{-- JavaScript --}}
    <script>
        // Toggle mobile menu
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Close mobile menu when clicking a link
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileMenu').classList.add('hidden');
            });
        });

        // Add scroll reveal animation
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all section cards
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.section-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

</body>
</html>
