<x-layouts.app>
    <x-slot name="title">Płatność - Rezerwacja {{ $booking->service->title }} - PetHelp</x-slot>

    <div class="py-8 bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
        @livewire('payment-process', ['booking' => $booking])
    </div>
</x-layouts.app>