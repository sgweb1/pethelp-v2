{{-- Hero Image Component --}}
<div class="relative max-w-lg mx-auto">
    <!-- Main image container -->
    <div class="relative z-10 group">
        <div class="aspect-square overflow-hidden rounded-3xl shadow-2xl bg-white dark:bg-gray-800 p-4 transform hover:scale-105 transition-transform duration-300">
            <img src="{{ asset('images/hero-pets.webp') }}"
                 alt="Szczęśliwy pies z opiekunem"
                 class="w-full h-full object-cover rounded-2xl"
                 loading="eager"
                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMDAgMTAwQzIzMC45IDEwMCAyNTUgMTI0LjEgMjU1IDE1NUMyNTUgMTg1LjkgMjMwLjkgMjEwIDIwMCAyMTBDMTY5LjEgMjEwIDE0NSAxODUuOSAxNDUgMTU1QzE0NSAxMjQuMSAxNjkuMSAxMDAgMjAwIDEwMFoiIGZpbGw9IiM5Q0EzQUYiLz4KPHBhdGggZD0iTTI4NSAyNzBIMTE1QzEwNy4yIDI3MCAxMDEgMjc2LjIgMTAxIDI4NFYzMTVDMTAxIDMyMi44IDEwNy4yIDMyOSAxMTUgMzI5SDI4NUMyOTIuOCAzMjkgMjk5IDMyMi44IDI5OSAzMTVWMjg0QzI5OSAyNzYuMiAyOTIuOCAyNzAgMjg1IDI3MFoiIGZpbGw9IiM5Q0EzQUYiLz4KPHN2Zz4K'">
        </div>

        <!-- Trust badge overlay -->
        <div class="absolute -bottom-4 -left-4 bg-green-500 text-white rounded-full p-3 shadow-lg z-20">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
        </div>
    </div>

    <!-- Floating decorative elements -->
    <div class="absolute -top-6 -right-6 w-20 h-20 bg-yellow-400/20 rounded-full animate-pulse delay-200"></div>
    <div class="absolute -bottom-6 -left-2 w-16 h-16 bg-purple-400/20 rounded-full animate-pulse delay-700"></div>
    <div class="absolute top-1/2 -right-4 w-12 h-12 bg-blue-400/20 rounded-full animate-pulse delay-1200"></div>

    <!-- Heart icons floating -->
    <div class="absolute top-8 right-8 text-red-400 animate-bounce delay-300">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
        </svg>
    </div>
</div>