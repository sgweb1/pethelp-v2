<x-layouts.app>
    <x-slot name="title">Moje rezerwacje - PetHelp</x-slot>

    <div class="py-8 bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
        @livewire('booking-management', ['view' => $view])
    </div>
</x-layouts.app>