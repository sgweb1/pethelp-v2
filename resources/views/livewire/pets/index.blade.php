<?php

use App\Models\Pet;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

    /**
     * Breadcrumbs dla listy pupili.
     *
     * @return array
     */
    public function getBreadcrumbsProperty(): array
    {
        return [
            [
                'title' => 'Panel',
                'icon' => '🏠',
                'url' => route('dashboard')
            ],
            [
                'title' => 'Moje pupile',
                'icon' => '🐾'
            ]
        ];
    }

    public $search = '';
    public $filterType = '';
    public $filterStatus = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function with()
    {
        $query = auth()->user()->pets();

        // Search by name or breed
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('breed', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by type
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        // Filter by status
        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        // Sort
        $query->orderBy($this->sortBy, $this->sortDirection);

        return [
            'pets' => $query->get(),
            'petCounts' => [
                'total' => auth()->user()->pets()->count(),
                'active' => auth()->user()->pets()->where('is_active', true)->count(),
                'inactive' => auth()->user()->pets()->where('is_active', false)->count(),
            ]
        ];
    }

    public function updatedSearch()
    {
        // Reset to first page when searching
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->filterStatus = '';
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus(Pet $pet)
    {
        // Check if the pet belongs to the authenticated user
        if ($pet->user_id !== auth()->id()) {
            session()->flash('error', 'Nie masz uprawnień do modyfikacji tego zwierzęcia.');
            return;
        }

        $pet->update(['is_active' => !$pet->is_active]);

        $status = $pet->is_active ? 'aktywowany' : 'dezaktywowany';
        session()->flash('success', "Profil zwierzęcia {$pet->name} został {$status}.");
    }

    public function deletePet(Pet $pet)
    {
        // Sprawdź czy zwierzę należy do zalogowanego użytkownika
        if ($pet->user_id !== auth()->id()) {
            session()->flash('error', 'Nie masz uprawnień do usunięcia tego zwierzęcia.');
            return;
        }

        $pet->delete();
        session()->flash('success', 'Zwierzę zostało usunięte pomyślnie.');
    }
}; ?>

@php
    // Przekaż breadcrumbs do layoutu
    $breadcrumbs = $this->breadcrumbs;
@endphp

<div class="desktop-window">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Moje zwierzęta
                    </h1>
                    <p class="text-white/80 mt-1">
                        Zarządzaj profilami swoich pupili
                    </p>
                </div>

                <a href="{{ route('pets.create') }}" wire:navigate class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-white/10 border border-white/20 rounded-xl hover:bg-white/20 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj zwierzę
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Pets Grid -->
        @if($pets->isEmpty())
            <div class="text-center py-12">
                <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-large p-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Brak zwierząt</h3>
                    <p class="text-gray-600 mb-6">Dodaj profil swojego pierwszego pupila, aby rozpocząć korzystanie z PetHelp.</p>
                    <a href="{{ route('pets.create') }}" wire:navigate class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dodaj pierwsze zwierzę
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pets as $pet)
                    <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-large p-6 hover:shadow-xl transition-shadow duration-200">
                        <!-- Pet Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mr-3">
                                    @if($pet->type === 'dog')
                                        <span class="text-2xl">🐕</span>
                                    @elseif($pet->type === 'cat')
                                        <span class="text-2xl">🐱</span>
                                    @elseif($pet->type === 'bird')
                                        <span class="text-2xl">🐦</span>
                                    @elseif($pet->type === 'rabbit')
                                        <span class="text-2xl">🐰</span>
                                    @else
                                        <span class="text-2xl">🐾</span>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $pet->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $pet->breed ?? ucfirst($pet->type) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($pet->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktywny
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Nieaktywny
                                    </span>
                                @endif

                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="p-1 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                        </svg>
                                    </button>

                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                        <div class="py-1">
                                            <a href="{{ route('pets.edit', $pet) }}" wire:navigate class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 block">
                                                Edytuj
                                            </a>
                                            <button wire:click="deletePet({{ $pet->id }})" wire:confirm="Czy na pewno chcesz usunąć profil zwierzęcia {{ $pet->name }}?" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                Usuń
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pet Details -->
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Płeć:</span>
                                <span class="font-medium">{{ ucfirst($pet->gender) }}</span>
                            </div>
                            @if($pet->weight)
                                <div class="flex justify-between">
                                    <span>Waga:</span>
                                    <span class="font-medium">{{ $pet->weight }} kg</span>
                                </div>
                            @endif
                            @if($pet->birth_date)
                                <div class="flex justify-between">
                                    <span>Wiek:</span>
                                    <span class="font-medium">{{ $pet->birth_date->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>

                        @if($pet->description)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600 leading-relaxed">{{ Str::limit($pet->description, 100) }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>