<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHelp - Inteligentna Wyszukiwarka (Mockup)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .search-suggestion {
            transition: all 0.2s;
        }
        .search-suggestion:hover {
            background-color: #f3f4f6;
            transform: translateX(4px);
        }
        .sitter-card {
            transition: all 0.3s ease;
        }
        .sitter-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .map-container {
            height: calc(100vh - 280px);
            min-height: 500px;
        }
        .results-list {
            height: calc(100vh - 280px);
            min-height: 500px;
            overflow-y: auto;
        }
        .filter-pill {
            transition: all 0.2s;
        }
        .filter-pill:hover {
            transform: scale(1.05);
        }
        .filter-pill.active {
            background-color: #3b82f6;
            color: white;
        }
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .loading-pulse {
            animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        /* Custom scrollbar */
        .results-list::-webkit-scrollbar {
            width: 8px;
        }
        .results-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .results-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .results-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="h-full bg-gray-50">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-blue-600">🐾 PetHelp</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-gray-700 hover:text-blue-600">Szukaj</a>
                    <a href="#" class="text-gray-700 hover:text-blue-600">Zostań opiekunem</a>
                    <a href="#" class="text-gray-700 hover:text-blue-600">Pomoc</a>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Zaloguj się
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Search Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Main Search Bar -->
            <div class="relative">
                <div class="relative">
                    <input
                        type="text"
                        id="mainSearch"
                        placeholder="Znajdź idealnego opiekuna dla Twojego pupila..."
                        class="w-full px-6 py-4 pr-48 rounded-xl text-gray-900 text-lg focus:outline-none focus:ring-4 focus:ring-blue-300 shadow-xl"
                    />
                    <div class="absolute right-2 top-2 flex items-center space-x-2">
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            <span>Filtry</span>
                        </button>
                        <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Szukaj
                        </button>
                    </div>
                </div>

                <!-- Auto-suggestions (show on focus) -->
                <div id="suggestions" class="hidden absolute z-10 w-full mt-2 bg-white rounded-xl shadow-2xl max-h-96 overflow-y-auto">
                    <div class="p-2">
                        <!-- AI Suggestions -->
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Sugestie AI</div>
                        <div class="search-suggestion px-4 py-3 cursor-pointer rounded-lg flex items-center space-x-3">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <div>
                                <div class="font-medium text-gray-900">Spacer z psem w Warszawie Mokotów</div>
                                <div class="text-sm text-gray-500">42 dostępnych opiekunów</div>
                            </div>
                        </div>

                        <!-- Recent Searches -->
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase mt-2">Ostatnie wyszukiwania</div>
                        <div class="search-suggestion px-4 py-3 cursor-pointer rounded-lg flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="font-medium text-gray-700">Opieka nad kotem Kraków</div>
                        </div>

                        <!-- Popular Searches -->
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase mt-2">Popularne</div>
                        <div class="search-suggestion px-4 py-3 cursor-pointer rounded-lg flex items-center space-x-3">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                            </svg>
                            <div class="font-medium text-gray-700">Najlepsi opiekunowie psów 2025</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Filter Pills -->
            <div class="flex flex-wrap gap-2 mt-4">
                <button class="filter-pill active px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium hover:bg-white/30">
                    🐕 Psy
                </button>
                <button class="filter-pill px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium hover:bg-white/30">
                    🐈 Koty
                </button>
                <button class="filter-pill px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium hover:bg-white/30">
                    📍 W pobliżu (5km)
                </button>
                <button class="filter-pill px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium hover:bg-white/30">
                    ⭐ Najlepsi (4.5+)
                </button>
                <button class="filter-pill px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium hover:bg-white/30">
                    💰 Do 50zł/h
                </button>
                <button class="filter-pill px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium hover:bg-white/30">
                    📅 Dziś dostępni
                </button>
                <button class="filter-pill px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium hover:bg-white/30">
                    ✓ Zweryfikowani
                </button>
                <button class="filter-pill px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium hover:bg-white/20 border border-white/30">
                    + Więcej filtrów
                </button>
            </div>

            <!-- Result Tabs -->
            <div class="flex space-x-4 mt-4 border-b border-white/20">
                <button class="px-4 py-2 border-b-2 border-white font-medium">
                    🐾 Opiekunowie (142)
                </button>
                <button class="px-4 py-2 border-b-2 border-transparent hover:border-white/50 opacity-70 hover:opacity-100">
                    🎉 Wydarzenia (8)
                </button>
                <button class="px-4 py-2 border-b-2 border-transparent hover:border-white/50 opacity-70 hover:opacity-100">
                    🏥 Usługi (12)
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content: Map + Results -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            <!-- Map Section (60% - 3 columns) -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Map Controls -->
                    <div class="px-4 py-3 border-b flex items-center justify-between bg-gray-50">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-700">Warstwy mapy:</span>
                            <label class="inline-flex items-center">
                                <input type="checkbox" checked class="form-checkbox h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm">Opiekunowie</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm">Wydarzenia</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm">Usługi</span>
                            </label>
                        </div>
                        <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            📍 Moja lokalizacja
                        </button>
                    </div>

                    <!-- Map -->
                    <div id="map" class="map-container bg-gray-100">
                        <!-- Leaflet map will render here -->
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <div class="loading-pulse">
                                    <svg class="w-16 h-16 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                </div>
                                <p class="mt-4 font-medium">Ładowanie mapy...</p>
                                <p class="text-sm">Przygotowujemy wyniki wyszukiwania</p>
                            </div>
                        </div>
                    </div>

                    <!-- Map Legend -->
                    <div class="px-4 py-2 border-t bg-gray-50 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-1"></div>
                                <span>Opiekun</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                                <span>Dostępny dziś</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-1"></div>
                                <span>SuperSitter</span>
                            </div>
                        </div>
                        <div class="text-gray-500">
                            Kliknij na pinezkę aby zobaczyć szczegóły
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results List (40% - 2 columns) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Results Header -->
                    <div class="px-4 py-3 border-b bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">142 wyniki</h3>
                                <p class="text-sm text-gray-500">Sortuj według</p>
                            </div>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Najlepsze dopasowanie</option>
                                <option>Najwyższa ocena</option>
                                <option>Najniższa cena</option>
                                <option>Najbliższa odległość</option>
                                <option>Najszybsza dostępność</option>
                            </select>
                        </div>
                    </div>

                    <!-- Results Scroll Container -->
                    <div class="results-list p-4 space-y-4">

                        <!-- Sitter Card 1 - Premium -->
                        <div class="sitter-card bg-white border-2 border-yellow-400 rounded-xl p-4 shadow-sm">
                            <!-- Premium Badge -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <img src="https://i.pravatar.cc/80?img=1" alt="Avatar" class="w-16 h-16 rounded-full">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-semibold text-gray-900">Anna Kowalska</h4>
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                                ⭐ SuperSitter
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-1 text-sm">
                                            <span class="text-yellow-500">★★★★★</span>
                                            <span class="font-medium">4.9</span>
                                            <span class="text-gray-500">(142 opinie)</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="text-gray-400 hover:text-red-500">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Quick Stats -->
                            <div class="grid grid-cols-3 gap-2 mb-3">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-gray-700">1.2 km</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-900">35-50 zł/h</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-green-600 font-medium">98%</span>
                                </div>
                            </div>

                            <!-- Specializations -->
                            <div class="flex flex-wrap gap-1 mb-3">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded">Duże psy</span>
                                <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">Szkolenia</span>
                                <span class="px-2 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded">Podawanie leków</span>
                                <span class="px-2 py-1 bg-orange-50 text-orange-700 text-xs font-medium rounded">+3 więcej</span>
                            </div>

                            <!-- Availability -->
                            <div class="mb-3 p-2 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-800">
                                    <span class="font-medium">Dostępna:</span> Dziś 14:00-18:00
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <button class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                    Zarezerwuj teraz
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Zobacz profil
                                </button>
                                <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    💬
                                </button>
                            </div>
                        </div>

                        <!-- Sitter Card 2 - Regular -->
                        <div class="sitter-card bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:border-blue-300">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <img src="https://i.pravatar.cc/80?img=5" alt="Avatar" class="w-16 h-16 rounded-full">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-semibold text-gray-900">Piotr Nowak</h4>
                                            <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                ✓ Zweryfikowany
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-1 text-sm">
                                            <span class="text-yellow-500">★★★★☆</span>
                                            <span class="font-medium">4.7</span>
                                            <span class="text-gray-500">(89 opinii)</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="text-gray-400 hover:text-red-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-2 mb-3">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    <span class="text-gray-700">2.8 km</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-900">30-40 zł/h</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-green-600 font-medium">95%</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-1 mb-3">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded">Koty</span>
                                <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">Małe psy</span>
                                <span class="px-2 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded">Wizyty domowe</span>
                            </div>

                            <div class="mb-3 p-2 bg-yellow-50 rounded-lg">
                                <p class="text-sm text-yellow-800">
                                    <span class="font-medium">Dostępny:</span> Jutro od 09:00
                                </p>
                            </div>

                            <div class="flex space-x-2">
                                <button class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                    Zarezerwuj
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Profil
                                </button>
                                <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    💬
                                </button>
                            </div>
                        </div>

                        <!-- Sitter Card 3 -->
                        <div class="sitter-card bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:border-blue-300">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <img src="https://i.pravatar.cc/80?img=20" alt="Avatar" class="w-16 h-16 rounded-full">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Katarzyna Wiśniewska</h4>
                                        <div class="flex items-center space-x-1 text-sm">
                                            <span class="text-yellow-500">★★★★★</span>
                                            <span class="font-medium">5.0</span>
                                            <span class="text-gray-500">(23 opinie)</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="text-red-500">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-2 mb-3">
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 mr-1">📍</span>
                                    <span class="text-gray-700">3.5 km</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 mr-1">💰</span>
                                    <span class="font-medium text-gray-900">45 zł/h</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-green-500 mr-1">✓</span>
                                    <span class="text-green-600 font-medium">100%</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-1 mb-3">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded">Wszystkie rasy</span>
                                <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">10 lat doświadczenia</span>
                            </div>

                            <div class="mb-3 p-2 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Dostępna:</span> W weekend
                                </p>
                            </div>

                            <div class="flex space-x-2">
                                <button class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                    Zarezerwuj
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Profil
                                </button>
                                <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    💬
                                </button>
                            </div>
                        </div>

                        <!-- Loading More Indicator -->
                        <div class="text-center py-4">
                            <div class="inline-flex items-center space-x-2 text-gray-500">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Ładowanie kolejnych wyników...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="px-4 py-3 border-t bg-gray-50 flex items-center justify-between">
                        <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 disabled:opacity-50" disabled>
                            ← Poprzednie
                        </button>
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-1 bg-blue-600 text-white rounded">1</button>
                            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">2</button>
                            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">3</button>
                            <span class="text-gray-500">...</span>
                            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">8</button>
                        </div>
                        <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">
                            Następne →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple JS for Interactions -->
    <script>
        // Initialize map (demo)
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Leaflet map
            const map = L.map('map').setView([52.2297, 21.0122], 13); // Warsaw coordinates

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add sample markers
            const sitters = [
                {lat: 52.2297, lng: 21.0122, name: 'Anna Kowalska', rating: 4.9},
                {lat: 52.2397, lng: 21.0222, name: 'Piotr Nowak', rating: 4.7},
                {lat: 52.2197, lng: 21.0022, name: 'Katarzyna Wiśniewska', rating: 5.0},
            ];

            sitters.forEach(sitter => {
                const marker = L.marker([sitter.lat, sitter.lng]).addTo(map);
                marker.bindPopup(`<b>${sitter.name}</b><br>⭐ ${sitter.rating}<br><a href="#">Zobacz profil</a>`);
            });

            // Search suggestions
            const searchInput = document.getElementById('mainSearch');
            const suggestions = document.getElementById('suggestions');

            searchInput.addEventListener('focus', () => {
                suggestions.classList.remove('hidden');
            });

            searchInput.addEventListener('blur', () => {
                setTimeout(() => suggestions.classList.add('hidden'), 200);
            });

            // Filter pills interaction
            const filterPills = document.querySelectorAll('.filter-pill');
            filterPills.forEach(pill => {
                pill.addEventListener('click', () => {
                    pill.classList.toggle('active');
                });
            });

            console.log('🐾 PetHelp Smart Search Mockup loaded!');
        });
    </script>
</body>
</html>
