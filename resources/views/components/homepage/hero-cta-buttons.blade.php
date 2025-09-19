{{-- Hero CTA Buttons Component --}}
<div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
    <a href="{{ route('search') }}"
       class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
        <x-icon name="heroicon-s-magnifying-glass" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
        Znajdź opiekuna
        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
    </a>

    <a href="{{ route('register') }}?type=sitter"
       class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 border-2 border-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-md hover:shadow-lg">
        <x-icon name="heroicon-s-user-plus" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
        Zostań opiekunem
    </a>
</div>