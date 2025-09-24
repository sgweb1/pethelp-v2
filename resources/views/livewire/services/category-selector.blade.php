@section('title', 'Wybierz rodzaj usługi - PetHelp')

@section('header-title')
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Dodaj usługę</h1>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Wybierz kategorię</span>
        </div>

        <a href="{{ route('sitter-services.index') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Powrót do listy
        </a>
    </div>
@endsection

<div class="space-y-6">
    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Info Box -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100">Informacje o kategoriach</h3>
                <ul class="mt-2 text-sm text-blue-800 dark:text-blue-200 space-y-1">
                    <li>• Możesz mieć tylko jedną usługę w każdej kategorii</li>
                    <li>• Twoja oferta pojawi się automatycznie w wyszukiwarce</li>
                    <li>• Klienci będą mogli Cię znaleźć na mapie</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($categories as $category)
        <div
            wire:click="selectCategory({{ $category->id }})"
            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-200
                   {{ $category->has_service ? 'cursor-not-allowed opacity-60' : 'cursor-pointer hover:border-blue-500' }}
                   group"
        >
            <div class="p-4 text-center">
                <!-- Icon -->
                <div class="w-12 h-12 mx-auto mb-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-xl group-hover:bg-blue-500 group-hover:text-white transition-colors">
                    {{ $category->icon ?: '🐾' }}
                </div>

                <!-- Category Name -->
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                    {{ $category->name }}
                </h3>

                <!-- Description -->
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                    {{ $category->description }}
                </p>

                <!-- Status -->
                @if($category->has_service)
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        ✓ Masz już usługę
                    </span>
                @else
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                        Kliknij aby stworzyć
                    </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
