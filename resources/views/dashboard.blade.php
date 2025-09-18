<x-app-layout>
    @if(auth()->user()->isOwner())
        @livewire('dashboard.owner')
    @elseif(auth()->user()->isSitter())
        @livewire('dashboard.sitter')
    @else
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h2 class="text-xl font-semibold mb-4">Witaj w PetHelp!</h2>
                        <p class="mb-4">Aby korzystać z platformy, musisz ukończyć swój profil.</p>
                        <a href="{{ route('profile.edit') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">
                            Uzupełnij profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
