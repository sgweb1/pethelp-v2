<x-layouts.app>
    <x-slot name="title">{{ $sitter->name }} - Profil opiekuna - PetHelp</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Sitter Header -->
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-8 py-12">
                    <div class="flex items-center space-x-6">
                        <!-- Avatar -->
                        <div class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center text-4xl">
                            👤
                        </div>

                        <!-- Sitter Info -->
                        <div class="text-white">
                            <h1 class="text-4xl font-bold mb-2">{{ $sitter->name }}</h1>
                            <p class="text-lg opacity-90 mb-4">Profesjonalny opiekun zwierząt</p>

                            <!-- Contact Info -->
                            <div class="flex flex-wrap gap-4 text-sm">
                                <span class="bg-white/20 px-3 py-1 rounded-full">
                                    📧 {{ $sitter->email }}
                                </span>
                                @if($sitter->locations->count() > 0)
                                    <span class="bg-white/20 px-3 py-1 rounded-full">
                                        📍 {{ $sitter->locations->first()->city }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="px-8 py-6 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-indigo-600">{{ $services->count() }}</div>
                            <div class="text-sm text-gray-600">Usługi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">⭐</div>
                            <div class="text-sm text-gray-600">Zweryfikowany</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $sitter->locations->count() }}</div>
                            <div class="text-sm text-gray-600">Lokalizacji</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            @if($services->count() > 0)
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Oferowane usługi</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($services as $service)
                            <div class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow">
                                <!-- Service Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-2xl">{{ $service->category->icon ?? '🐾' }}</div>
                                        <div>
                                            <h3 class="font-semibold text-lg text-gray-900">{{ $service->title }}</h3>
                                            <p class="text-sm text-gray-600">{{ $service->category->name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-indigo-600">{{ $service->display_price }}</div>
                                    </div>
                                </div>

                                <!-- Service Description -->
                                @if($service->description)
                                    <p class="text-gray-700 mb-4">{{ $service->description }}</p>
                                @endif

                                <!-- Service Details -->
                                <div class="space-y-2 text-sm">
                                    @if($service->pet_types && count($service->pet_types) > 0)
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-600">Zwierzęta:</span>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($service->pet_types as $petType)
                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">{{ $petType }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if($service->pet_sizes && count($service->pet_sizes) > 0)
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-600">Rozmiary:</span>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($service->pet_sizes as $petSize)
                                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">{{ $petSize }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if($service->service_types && count($service->service_types) > 0)
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-600">Gdzie:</span>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($service->service_types as $serviceType)
                                                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">{{ $serviceType }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if($service->max_pets)
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-600">Max zwierząt:</span>
                                            <span class="text-gray-900">{{ $service->max_pets }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Booking Button -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="{{ route('booking.create', $service) }}"
                                       class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-semibold text-center block">
                                        Zarezerwuj tę usługę
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Locations Section -->
            @if($sitter->locations->count() > 0)
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Obszary działania</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($sitter->locations as $location)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="text-lg">📍</span>
                                    <h3 class="font-semibold text-gray-900">{{ $location->city }}</h3>
                                </div>
                                @if($location->street)
                                    <p class="text-sm text-gray-600">{{ $location->street }}</p>
                                @endif
                                @if($location->postal_code)
                                    <p class="text-sm text-gray-600">{{ $location->postal_code }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Contact Section -->
            <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Kontakt</h2>

                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-6">
                    <div class="text-center">
                        <div class="text-4xl mb-4">📞</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Skontaktuj się z {{ $sitter->name }}</h3>
                        <p class="text-gray-600 mb-6">Zaloguj się, aby zobaczyć dane kontaktowe i umówić opiekę</p>

                        @auth
                            <div class="space-y-2">
                                <p class="text-sm text-gray-700">
                                    <span class="font-medium">Email:</span> {{ $sitter->email }}
                                </p>
                                <button class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                                    Wyślij wiadomość
                                </button>
                            </div>
                        @else
                            <div class="space-x-4">
                                <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors inline-block">
                                    Zaloguj się
                                </a>
                                <a href="{{ route('register') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors inline-block">
                                    Zarejestruj się
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Back to Search -->
            <div class="mt-8 text-center">
                <a href="{{ route('search') }}" class="inline-flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition-colors">
                    <span>←</span>
                    <span>Powrót do wyszukiwania</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>