<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Dodaj nowe ogłoszenie
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Wybierz rodzaj ogłoszenia, które chcesz dodać
        </p>
    </div>

    <!-- Step indicator -->
    <div class="mb-8">
        <div class="flex items-center">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                    1
                </div>
                <span class="ml-2 text-sm font-medium text-primary-600 dark:text-primary-400">
                    Wybierz kategorię
                </span>
            </div>
            <div class="flex-1 mx-4 h-px bg-gray-300 dark:bg-gray-600"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">
                    2
                </div>
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                    Wypełnij formularz
                </span>
            </div>
        </div>
    </div>

    <!-- Category Types -->
    @if(!$selectedType)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->mainCategories as $category)
                <div wire:click="selectType('{{ $category->type }}')"
                     class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 cursor-pointer transition-all duration-200 hover:shadow-md hover:border-primary-300 dark:hover:border-primary-600 group">

                    <!-- Icon -->
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4 transition-colors duration-200"
                         style="background-color: {{ $category->color }}20">
                        @switch($category->icon)
                            @case('heart')
                                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200"
                                     style="color: {{ $category->color }}" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                @break
                            @case('currency-dollar')
                                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200"
                                     style="color: {{ $category->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                @break
                            @case('exclamation-triangle')
                                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200"
                                     style="color: {{ $category->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                @break
                            @case('shopping-bag')
                                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200"
                                     style="color: {{ $category->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 11H6L5 9z"/>
                                </svg>
                                @break
                            @default
                                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200"
                                     style="color: {{ $category->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                        @endswitch
                    </div>

                    <!-- Content -->
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-200">
                        {{ $category->name }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ $category->description }}
                    </p>

                    <!-- Arrow -->
                    <div class="flex items-center text-primary-600 dark:text-primary-400 text-sm font-medium group-hover:translate-x-1 transition-transform duration-200">
                        <span>Wybierz</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Subcategories -->
    @if($selectedType && $this->subcategories->count() > 0)
        <div class="mb-6">
            <button wire:click="selectType(null)"
                    class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Wróć do wyboru kategorii
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Wybierz szczegółową kategorię
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($this->subcategories as $subcategory)
                    <div wire:click="selectCategory({{ $subcategory->id }})"
                         class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-primary-300 dark:hover:border-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/10 group">

                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-colors duration-200"
                                 style="background-color: {{ $subcategory->color }}20">
                                @switch($subcategory->icon)
                                    @case('heart')
                                        <svg class="w-5 h-5" style="color: {{ $subcategory->color }}" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                        @break
                                    @case('check-circle')
                                        <svg class="w-5 h-5" style="color: {{ $subcategory->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        @break
                                    @default
                                        <svg class="w-5 h-5" style="color: {{ $subcategory->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                @endswitch
                            </div>

                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-200">
                                    {{ $subcategory->name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $subcategory->description }}
                                </p>
                            </div>

                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>