<?php

namespace App\Livewire\Dashboard\Gallery;

use App\Models\Photo;
use App\Models\Pet;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class GalleryIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterAlbum = '';
    public string $filterPet = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public string $viewMode = 'grid'; // grid or list
    public bool $showUploadModal = false;
    public bool $showDeleteModal = false;
    public ?Photo $photoToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterAlbum' => ['except' => ''],
        'filterPet' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'viewMode' => ['except' => 'grid'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAlbum(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPet(): void
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
    public function photos()
    {
        return Photo::query()
            ->forUser(auth()->id())
            ->with(['pet'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('file_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterAlbum, function ($query) {
                if ($this->filterAlbum === 'none') {
                    $query->whereNull('album');
                } else {
                    $query->where('album', $this->filterAlbum);
                }
            })
            ->when($this->filterPet, function ($query) {
                if ($this->filterPet === 'none') {
                    $query->whereNull('pet_id');
                } else {
                    $query->where('pet_id', $this->filterPet);
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(24);
    }

    #[Computed]
    public function stats()
    {
        $photos = Photo::forUser(auth()->id());

        return [
            'total' => $photos->count(),
            'featured' => $photos->where('is_featured', true)->count(),
            'public' => $photos->where('is_public', true)->count(),
            'total_size' => $photos->sum('file_size'),
            'albums' => $photos->whereNotNull('album')->distinct('album')->count('album'),
        ];
    }

    #[Computed]
    public function albums()
    {
        return Photo::forUser(auth()->id())
            ->whereNotNull('album')
            ->distinct()
            ->pluck('album')
            ->sort()
            ->values();
    }

    #[Computed]
    public function userPets()
    {
        return Pet::where('owner_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    public function toggleFeatured(Photo $photo): void
    {
        if ($photo->user_id === auth()->id()) {
            $photo->update(['is_featured' => !$photo->is_featured]);
            $this->dispatch('photo-updated');
        }
    }

    public function togglePublic(Photo $photo): void
    {
        if ($photo->user_id === auth()->id()) {
            $photo->update(['is_public' => !$photo->is_public]);
            $this->dispatch('photo-updated');
        }
    }

    public function confirmDelete(Photo $photo): void
    {
        $this->photoToDelete = $photo;
        $this->showDeleteModal = true;
    }

    public function deletePhoto(): void
    {
        if ($this->photoToDelete && $this->photoToDelete->user_id === auth()->id()) {
            $this->photoToDelete->delete();

            session()->flash('success', 'Zdjęcie zostało usunięte.');
            $this->showDeleteModal = false;
            $this->photoToDelete = null;
            $this->dispatch('photo-deleted');
        }
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->photoToDelete = null;
    }

    public function openUploadModal(): void
    {
        $this->showUploadModal = true;
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
    }

    public function render()
    {
        $breadcrumbs = [
            [
                'title' => 'Panel',
                'icon' => '🏠',
                'url' => route('dashboard')
            ],
            [
                'title' => 'Galeria zdjęć',
                'icon' => '📸'
            ]
        ];

        return view('livewire.dashboard.gallery.gallery-index')
            ->layout('components.dashboard-layout', [
                'title' => 'Galeria Zdjęć',
                'activeSection' => 'gallery',
                'breadcrumbs' => $breadcrumbs
            ]);
    }
}
