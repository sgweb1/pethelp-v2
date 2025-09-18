<x-layouts.app>
    <x-slot name="title">Rezerwacja - {{ $service->title }} - PetHelp</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @livewire('booking-form', ['service' => $service])
        </div>
    </div>
</x-layouts.app>