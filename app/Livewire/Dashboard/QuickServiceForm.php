<?php

namespace App\Livewire\Dashboard;

use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Component;

class QuickServiceForm extends Component
{
    public $showForm = false;
    public $title = '';
    public $category_id = '';
    public $description = '';
    public $price_per_hour = '';
    public $pet_types = [];
    public $pet_sizes = [];
    public $home_service = false;
    public $sitter_home = false;

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->title = '';
        $this->category_id = '';
        $this->description = '';
        $this->price_per_hour = '';
        $this->pet_types = [];
        $this->pet_sizes = [];
        $this->home_service = false;
        $this->sitter_home = false;
    }

    public function createService()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:service_categories,id',
            'description' => 'required|string|min:10',
            'price_per_hour' => 'required|numeric|min:0',
            'pet_types' => 'required|array|min:1',
            'pet_sizes' => 'required|array|min:1',
        ]);

        Service::create([
            'sitter_id' => auth()->id(),
            'category_id' => $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'price_per_hour' => $this->price_per_hour,
            'pet_types' => $this->pet_types,
            'pet_sizes' => $this->pet_sizes,
            'home_service' => $this->home_service,
            'sitter_home' => $this->sitter_home,
            'is_active' => true,
            'max_pets' => 1,
        ]);

        session()->flash('message', 'Usługa została pomyślnie utworzona!');
        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('service-created');
    }

    public function getCategoriesProperty()
    {
        return ServiceCategory::all();
    }

    public function render()
    {
        return view('livewire.dashboard.quick-service-form');
    }
}
