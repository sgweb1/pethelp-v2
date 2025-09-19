<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;

class Search extends Component
{
    // UI state
    public bool $show_map = false;
    public bool $show_mobile_map = false;
    public string $sort_by = 'relevance';

    public array $filters = [];

    // Search state management
    protected $listeners = [
        'filters-updated' => 'handleFiltersUpdate',
        'location-detected' => 'handleLocationDetected',
        'search-saved' => 'handleSearchSaved',
        'map-bounds-changed' => 'handleMapBoundsChanged',
        'result-hovered' => 'handleResultHovered',
        'result-clicked' => 'handleResultClicked',
    ];

    public function mount(): void
    {
        // Initialize with URL parameters
        $this->filters = [
            'search_term' => request('search', ''),
            'location' => request('location', ''),
            'category_id' => request('category', ''),
            'pet_type' => request('pet_type', ''),
        ];
    }

    public function handleFiltersUpdate(array $filters): void
    {
        $this->filters = $filters;

        // Propagate filters to all child components
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);
    }

    public function handleLocationDetected(array $locationData): void
    {
        $this->filters = array_merge($this->filters, [
            'latitude' => $locationData['latitude'],
            'longitude' => $locationData['longitude'],
            'location' => $locationData['address'],
        ]);

        // Update all child components with new location
        $this->dispatch('filters-updated', $this->filters);
        $this->dispatch('location-updated', $locationData);
    }

    public function handleMapBoundsChanged(array $bounds): void
    {
        // Update results when map bounds change (for viewport-based filtering)
        $this->dispatch('map-viewport-changed', $bounds);
    }

    public function handleResultHovered(?int $resultId): void
    {
        // Highlight corresponding marker on map
        $this->dispatch('highlight-map-marker', $resultId);
    }

    public function handleResultClicked(int $resultId): void
    {
        // Focus on specific marker on map
        $this->dispatch('focus-map-marker', $resultId);
    }

    public function handleSearchSaved(): void
    {
        if (! auth()->check()) {
            return;
        }

        // Save search to session for now (can be extended to database)
        session()->put('last_search', $this->filters);
    }

    public function toggleMap(): void
    {
        $this->show_map = ! $this->show_map;
        $this->dispatch('map-toggled');
    }

    public function saveSearch(): void
    {
        $this->dispatch('search-saved');
    }

    public function loadSavedSearch(): void
    {
        if (! auth()->check()) {
            return;
        }

        $searchData = session()->get('last_search');
        if ($searchData) {
            $this->filters = array_merge($this->filters, $searchData);
            $this->dispatch('filters-updated', $this->filters);
        }
    }

    #[Computed]
    public function totalResults(): int
    {
        // This will be calculated by the SearchResults component
        // For now, return a placeholder
        return 0;
    }

    public function isMobile(): bool
    {
        $userAgent = request()->header('User-Agent');

        // Basic mobile detection
        return preg_match('/Mobile|Android|iPhone|iPad|Windows Phone/', $userAgent) === 1;
    }

    public function render()
    {
        return view('livewire.search');
    }
}
