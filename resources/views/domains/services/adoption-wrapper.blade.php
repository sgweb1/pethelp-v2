<x-dashboard-layout>
    @section('title', 'Dodaj ogłoszenie adopcyjne - PetHelp')

    <div class="max-w-4xl mx-auto">
        @livewire('advertisements.adoption-form', ['categoryId' => $categoryId ?? null, 'advertisement' => $advertisement ?? null])
    </div>
</x-dashboard-layout>