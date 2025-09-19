{{-- Hero Section - Pet Sitters CORE Focus --}}
<section class="hero-gradient min-h-screen relative overflow-hidden" id="hero">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
                <pattern id="petpattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="10" cy="10" r="2" fill="white" opacity="0.3"/>
                </pattern>
            </defs>
            <rect width="100" height="100" fill="url(#petpattern)"/>
        </svg>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 flex items-center min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Column: Content -->
                <div class="text-center lg:text-left">
                    <!-- Badge -->
                    <div class="inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 mb-8">
                        <span class="text-white/90 text-sm font-medium">🏆 #1 Platforma Pet Sitterów w Polsce</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                        Znajdź <span class="text-yellow-300">zweryfikowanego</span> opiekuna dla swojego pupila
                    </h1>

                    <!-- Value Proposition -->
                    <p class="text-xl text-white/90 mb-8 leading-relaxed">
                        Największa platforma pet sitterów w Polsce z zaawansowanymi funkcjami dla bezpiecznej i wygodnej opieki nad zwierzętami
                    </p>

                    <!-- Unique Features Pills -->
                    <div class="grid grid-cols-2 gap-3 mb-10">
                        <div class="feature-pill rounded-xl px-4 py-3 text-white text-sm font-medium flex items-center hover:bg-white/20 transition-colors cursor-pointer">
                            <span class="text-lg mr-2">🔄</span>
                            Rezerwacje online
                        </div>
                        <div class="feature-pill rounded-xl px-4 py-3 text-white text-sm font-medium flex items-center hover:bg-white/20 transition-colors cursor-pointer">
                            <span class="text-lg mr-2">💬</span>
                            Chat na żywo
                        </div>
                        <div class="feature-pill rounded-xl px-4 py-3 text-white text-sm font-medium flex items-center hover:bg-white/20 transition-colors cursor-pointer">
                            <span class="text-lg mr-2">📍</span>
                            GPS tracking
                        </div>
                        <div class="feature-pill rounded-xl px-4 py-3 text-white text-sm font-medium flex items-center hover:bg-white/20 transition-colors cursor-pointer">
                            <span class="text-lg mr-2">💳</span>
                            Bezpieczne płatności
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap justify-center lg:justify-start gap-6 mb-10">
                        <div class="flex items-center text-white/90">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Weryfikacja tożsamości</span>
                        </div>
                        <div class="flex items-center text-white/90">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Ubezpieczeni opiekunowie</span>
                        </div>
                        <div class="flex items-center text-white/90">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">24/7 wsparcie</span>
                        </div>
                    </div>

                    <!-- Primary CTAs -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-8">
                        <a href="/pet-sitters" class="group bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-xl text-center touch-friendly">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2 group-hover:animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                                Znajdź Pet Sittera
                            </span>
                        </a>
                        <a href="/register?type=sitter" class="group bg-white/10 hover:bg-white/20 text-white font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 border-2 border-white/20 hover:border-white/40 text-center backdrop-blur-md touch-friendly">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2 group-hover:animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                                </svg>
                                Zostań Opiekunem
                            </span>
                        </a>
                    </div>

                    <!-- Earning Potential for Sitters -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 mb-8">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white font-semibold text-sm">Zarabiaj jako opiekun</div>
                                    <div class="text-white/70 text-xs">40-80 zł/godz - ustal własne stawki</div>
                                </div>
                            </div>
                            <div class="text-yellow-400 font-bold text-lg">→</div>
                        </div>
                    </div>

                    <!-- Social Proof Statistics -->
                    <div class="pt-8 border-t border-white/20">
                        <div class="grid grid-cols-3 gap-6 text-center">
                            <div class="group cursor-pointer">
                                <div class="text-2xl sm:text-3xl font-bold text-white mb-1 group-hover:text-yellow-300 transition-colors">1,247</div>
                                <div class="text-white/70 text-sm">Zweryfikowanych opiekunów</div>
                            </div>
                            <div class="group cursor-pointer">
                                <div class="text-2xl sm:text-3xl font-bold text-white mb-1 group-hover:text-yellow-300 transition-colors">8,432</div>
                                <div class="text-white/70 text-sm">Zadowolonych pupili</div>
                            </div>
                            <div class="group cursor-pointer">
                                <div class="text-2xl sm:text-3xl font-bold text-white mb-1 group-hover:text-yellow-300 transition-colors">4.9★</div>
                                <div class="text-white/70 text-sm">Średnia ocena</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Hero Visual -->
                <div class="hidden lg:block">
                    <div class="relative">
                        <!-- Main Hero Image Container -->
                        <div class="aspect-square rounded-3xl overflow-hidden shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-700 bg-gradient-to-br from-blue-400 to-purple-500">
                            <!-- Placeholder for now - replace with actual image -->
                            <div class="w-full h-full flex items-center justify-center text-white text-6xl">
                                🐕‍🦺
                            </div>
                        </div>

                        <!-- Floating Feature Cards -->
                        <div class="absolute -top-4 -left-4 bg-white rounded-xl p-4 shadow-lg animate-float-slow hover:shadow-2xl transition-shadow cursor-pointer">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Zweryfikowany</div>
                                    <div class="text-xs text-gray-600">Opiekun</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute -bottom-4 -right-4 bg-white rounded-xl p-4 shadow-lg animate-float-fast hover:shadow-2xl transition-shadow cursor-pointer">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Live Chat</div>
                                    <div class="text-xs text-gray-600">Dostępny 24/7</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute top-1/2 -left-8 bg-white rounded-xl p-3 shadow-lg animate-pulse">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-gray-900">4.9/5</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute top-1/2 -right-8 bg-white rounded-xl p-3 shadow-lg" style="animation-delay: 1s;">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-gray-900">GPS</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/60 animate-bounce cursor-pointer">
        <a href="#services-overview" class="block">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </a>
    </div>
</section>

<!-- Rover-style Search Section -->
<livewire:search.rover-search />

<style>
    .hero-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .feature-pill {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .touch-friendly {
        min-height: 44px;
        min-width: 44px;
    }

    @keyframes float-slow {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    @keyframes float-fast {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }

    .animate-float-slow {
        animation: float-slow 3s ease-in-out infinite;
    }

    .animate-float-fast {
        animation: float-fast 2s ease-in-out infinite;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        .hero-gradient {
            min-height: calc(100vh - 4rem);
        }

        .feature-pill {
            padding: 0.75rem;
            text-align: center;
        }

        .grid.grid-cols-2 {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        @media (min-width: 640px) {
            .grid.grid-cols-2 {
                grid-template-columns: 1fr 1fr;
            }
        }
    }

    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {
        .animate-float-slow,
        .animate-float-fast,
        .animate-bounce,
        .animate-pulse {
            animation: none;
        }

        .hover\:scale-105:hover {
            transform: none;
        }
    }

    /* High contrast mode */
    @media (prefers-contrast: high) {
        .hero-gradient {
            background: #000000;
        }

        .text-white\/90,
        .text-white\/70 {
            color: #ffffff !important;
        }

        .border-white\/20 {
            border-color: #ffffff !important;
        }
    }
</style>