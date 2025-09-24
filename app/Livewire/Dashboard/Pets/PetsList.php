<?php

namespace App\Livewire\Dashboard\Pets;

use App\Models\Pet;
use App\Models\PetType;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class PetsList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterType = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';
    public bool $showDeleteModal = false;
    public ?Pet $petToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    #[Computed]
    public function pets()
    {
        return Pet::query()
            ->where('owner_id', auth()->id())
            ->with(['petType'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('breed', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterType, function ($query) {
                $query->where('pet_type_id', $this->filterType);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }

    #[Computed]
    public function petTypes()
    {
        return PetType::orderBy('name')->get();
    }

    #[Computed]
    public function stats()
    {
        $pets = Pet::where('owner_id', auth()->id());

        return [
            'total' => $pets->count(),
            'active' => $pets->where('is_active', true)->count(),
            'types' => $pets->join('pet_types', 'pets.pet_type_id', '=', 'pet_types.id')
                          ->selectRaw('pet_types.name, COUNT(*) as count')
                          ->groupBy('pet_types.name')
                          ->pluck('count', 'name')
                          ->toArray()
        ];
    }

    public function confirmDelete(Pet $pet): void
    {
        $this->petToDelete = $pet;
        $this->showDeleteModal = true;
    }

    public function deletePet(): void
    {
        if ($this->petToDelete && $this->petToDelete->owner_id === auth()->id()) {
            $this->petToDelete->delete();

            session()->flash('success', 'Zwierzę zostało usunięte.');
            $this->showDeleteModal = false;
            $this->petToDelete = null;
            $this->dispatch('pet-deleted');
        }
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->petToDelete = null;
    }

    public function togglePetStatus(Pet $pet): void
    {
        if ($pet->owner_id === auth()->id()) {
            $pet->update(['is_active' => !$pet->is_active]);
            $this->dispatch('pet-updated');
        }
    }

    public function render()
    {
        return view('livewire.dashboard.pets.pets-list')
            ->layout('components.dashboard-layout', [
                'title' => 'Moje Zwierzęta',
                'activeSection' => 'pets'
            ]);
    }
}
