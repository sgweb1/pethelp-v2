<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Component;

class CategorySelector extends Component
{
    public ?int $selectedCategory = null;

    public function getCategories()
    {
        $categories = ServiceCategory::active()->ordered()->get();

        // Mark categories that user already has services for
        $userCategoryIds = Service::where('sitter_id', auth()->id())
            ->pluck('category_id')
            ->toArray();

        return $categories->map(function ($category) use ($userCategoryIds) {
            $category->has_service = in_array($category->id, $userCategoryIds);

            return $category;
        });
    }

    public function selectCategory(int $categoryId)
    {
        // Check if user already has a service in this category
        $existingService = Service::where('sitter_id', auth()->id())
            ->where('category_id', $categoryId)
            ->first();

        if ($existingService) {
            $category = ServiceCategory::find($categoryId);
            session()->flash('error', 'Masz już usługę w kategorii "'.$category->name.'". Usuń najpierw istniejącą usługę aby dodać nową.');

            return;
        }

        $this->selectedCategory = $categoryId;

        return $this->redirect(route('profile.services.create.form', ['category' => $categoryId]));
    }

    public function render()
    {
        $breadcrumbs = [
            [
                'title' => 'Panel',
                'icon' => '🏠',
                'url' => route('profile.dashboard'),
            ],
            [
                'title' => 'Pet Sitter',
                'icon' => '🐕',
                'url' => route('profile.dashboard'), // lub odpowiednia strona pet sitter jeśli istnieje
            ],
            [
                'title' => 'Usługi',
                'icon' => '🐾',
                'url' => route('profile.services.index'),
            ],
            [
                'title' => 'Wybierz kategorię',
                'icon' => '➕',
            ],
        ];

        return view('livewire.services.category-selector', [
            'categories' => $this->getCategories(),
        ])->layout('components.dashboard-layout', compact('breadcrumbs'));
    }
}
