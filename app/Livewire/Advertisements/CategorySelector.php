<?php

namespace App\Livewire\Advertisements;

use App\Models\AdvertisementCategory;
use Livewire\Component;

class CategorySelector extends Component
{
    public $selectedType = null;
    public $selectedCategory = null;

    public function mount()
    {
        // Set default or detect from route
        $this->selectedType = request()->get('type');
    }

    public function selectType($type)
    {
        $this->selectedType = $type;
        $this->selectedCategory = null;
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;

        // Redirect to the appropriate form
        return redirect()->route('advertisements.create.form', $categoryId);
    }

    public function getMainCategoriesProperty()
    {
        return AdvertisementCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getSubcategoriesProperty()
    {
        if (!$this->selectedType) {
            return collect();
        }

        return AdvertisementCategory::whereNotNull('parent_id')
            ->where('type', $this->selectedType)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function render()
    {
        return view('livewire.advertisements.category-selector')
            ->layout('components.dashboard-layout', [
                'title' => 'Dodaj nowe ogłoszenie - PetHelp'
            ]);
    }
}