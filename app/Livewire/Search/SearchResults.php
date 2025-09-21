<?php

namespace App\Livewire\Search;

use App\Models\MapItem;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchResults extends Component
{
    public array $filters = [];
    public string $viewMode = 'grid';
    public int $perPage = 12;
    public int $currentPage = 1;
    public bool $loading = false;
    public bool $hasMore = true;

    protected $listeners = [
        'filters-updated' => 'updateFilters',
        'update-results-filters' => 'updateFilters',
        'update-view-mode' => 'updateViewMode'
    ];

    public function mount($viewMode = 'grid'): void
    {
        // Initialize view mode
        $this->viewMode = $viewMode;

        // Initialize filters from URL parameters
        $this->filters = [
            'content_type' => request('service_type', request('content_type', '')),
            'search_term' => request('search', ''),
            'location' => request('location', ''),
            'category_name' => request('category', ''),
            'pet_type' => request('pet_type', ''),
        ];
    }

    public function updateFilters(array $filters): void
    {
        $this->filters = $filters;
        $this->currentPage = 1;
        $this->hasMore = true;
    }

    public function updatingFilters(): void
    {
        $this->currentPage = 1;
        $this->hasMore = true;
    }

    public function loadMore(): void
    {
        $this->loading = true;
        $this->currentPage++;
        $this->loading = false;
    }

    public function updateViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function getItemsProperty()
    {
        $query = MapItem::published()
            ->with(['user']);

        // Text search
        if (! empty($this->filters['search_term'])) {
            $query->search($this->filters['search_term']);
        }

        // Location-based search
        if (! empty($this->filters['location'])) {
            $query->where(function ($q) {
                $q->where('city', 'like', "%{$this->filters['location']}%")
                    ->orWhere('full_address', 'like', "%{$this->filters['location']}%");
            });
        }

        // Content type filter (pet_sitter, service, etc.)
        if (! empty($this->filters['content_type'])) {
            $query->byContentType($this->filters['content_type']);
        }

        // Category filter by name
        if (! empty($this->filters['category_name'])) {
            $query->where('category_name', 'like', "%{$this->filters['category_name']}%");
        }

        // Pet type filter (stored in category_name)
        if (! empty($this->filters['pet_type'])) {
            $query->where('category_name', 'like', "%{$this->filters['pet_type']}%");
        }

        // Price range filter
        if (! empty($this->filters['min_price']) || ! empty($this->filters['max_price'])) {
            $minPrice = ! empty($this->filters['min_price']) ? (float) $this->filters['min_price'] : null;
            $maxPrice = ! empty($this->filters['max_price']) ? (float) $this->filters['max_price'] : null;
            $query->priceRange($minPrice, $maxPrice);
        }

        // Rating filter
        if (! empty($this->filters['min_rating'])) {
            $query->where('rating_avg', '>=', $this->filters['min_rating']);
        }

        // Featured items
        if (! empty($this->filters['featured_only'])) {
            $query->featured();
        }

        // City filter
        if (! empty($this->filters['city'])) {
            $query->inCity($this->filters['city']);
        }

        // Voivodeship filter
        if (! empty($this->filters['voivodeship'])) {
            $query->inVoivodeship($this->filters['voivodeship']);
        }

        // Sorting
        $sortBy = $this->filters['sort_by'] ?? 'relevance';
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_from', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_from', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_avg', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'distance':
                // Location-based distance sorting would need coordinates
                if (! empty($this->filters['latitude']) && ! empty($this->filters['longitude'])) {
                    $query->nearLocation($this->filters['latitude'], $this->filters['longitude']);
                } else {
                    $query->orderBy('created_at', 'desc');
                }
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
            default: // relevance
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('rating_avg', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }

        // Get total count for hasMore check
        $total = $query->count();
        $offset = ($this->currentPage - 1) * $this->perPage;
        $this->hasMore = $total > $offset + $this->perPage;

        // Get paginated results
        return $query->offset($offset)
                    ->limit($this->perPage * $this->currentPage)
                    ->get();
    }

    #[Computed]
    public function resultsCount(): int
    {
        $query = MapItem::published();

        // Apply same filters as getItemsProperty but just count
        if (! empty($this->filters['search_term'])) {
            $query->search($this->filters['search_term']);
        }

        if (! empty($this->filters['location'])) {
            $query->where(function ($q) {
                $q->where('city', 'like', "%{$this->filters['location']}%")
                    ->orWhere('full_address', 'like', "%{$this->filters['location']}%");
            });
        }

        if (! empty($this->filters['content_type'])) {
            $query->byContentType($this->filters['content_type']);
        }

        if (! empty($this->filters['category_name'])) {
            $query->where('category_name', 'like', "%{$this->filters['category_name']}%");
        }

        if (! empty($this->filters['pet_type'])) {
            $query->where('category_name', 'like', "%{$this->filters['pet_type']}%");
        }

        if (! empty($this->filters['min_price']) || ! empty($this->filters['max_price'])) {
            $minPrice = ! empty($this->filters['min_price']) ? (float) $this->filters['min_price'] : null;
            $maxPrice = ! empty($this->filters['max_price']) ? (float) $this->filters['max_price'] : null;
            $query->priceRange($minPrice, $maxPrice);
        }

        if (! empty($this->filters['min_rating'])) {
            $query->where('rating_avg', '>=', $this->filters['min_rating']);
        }

        if (! empty($this->filters['featured_only'])) {
            $query->featured();
        }

        if (! empty($this->filters['city'])) {
            $query->inCity($this->filters['city']);
        }

        if (! empty($this->filters['voivodeship'])) {
            $query->inVoivodeship($this->filters['voivodeship']);
        }

        return $query->count();
    }

    public function render()
    {
        return view('livewire.search.search-results');
    }
}
