{{-- Final CTA Section Component --}}
<section class="py-16 lg:py-24 bg-gradient-to-r from-blue-600 to-purple-600">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
            Gotowy na spokój o swojego pupila?
        </h2>
        <p class="text-xl text-blue-100 mb-12 max-w-3xl mx-auto">
            Dołącz do tysięcy zadowolonych właścicieli, którzy zaufali PetHelp.
            Znajdź idealnego opiekuna już dziś!
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('search') }}"
               class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-600 bg-white hover:bg-gray-100 rounded-lg transition-colors duration-300">
                <x-icon name="heroicon-s-magnifying-glass" class="w-5 h-5 mr-2" />
                Znajdź opiekuna teraz
            </a>

            @guest
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-white hover:bg-white hover:text-blue-600 rounded-lg transition-all duration-300">
                <x-icon name="heroicon-s-user-plus" class="w-5 h-5 mr-2" />
                Zarejestruj się za darmo
            </a>
            @endguest
        </div>
    </div>
</section>