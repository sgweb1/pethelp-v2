<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\PetType;
use Livewire\Component;

class EditService extends Component
{
    public Service $service;
    public $title;
    public $description;
    public $pet_types = [];
    public $pet_sizes = [];
    public $max_pets;
    public $home_service = false;
    public $sitter_home = false;
    public $price_per_hour;
    public $price_per_day;
    public $price_per_week;

    protected $rules = [
        'title' => 'required|string|min:5|max:100',
        'description' => 'required|string|min:20|max:1000',
        'pet_types' => 'required|array|min:1',
        'pet_types.*' => 'string|in:dog,cat,bird,rabbit,hamster,fish,reptile,other',
        'pet_sizes' => 'required|array|min:1',
        'pet_sizes.*' => 'string|in:small,medium,large',
        'max_pets' => 'required|integer|min:1|max:10',
        'home_service' => 'boolean',
        'sitter_home' => 'boolean',
        'price_per_hour' => 'nullable|numeric|min:0|max:1000',
        'price_per_day' => 'nullable|numeric|min:0|max:2000',
        'price_per_week' => 'nullable|numeric|min:0|max:10000',
    ];

    protected $messages = [
        'title.required' => 'Tytuł jest wymagany.',
        'title.min' => 'Tytuł musi mieć przynajmniej 5 znaków.',
        'title.max' => 'Tytuł może mieć maksymalnie 100 znaków.',
        'description.required' => 'Opis jest wymagany.',
        'description.min' => 'Opis musi mieć przynajmniej 20 znaków.',
        'description.max' => 'Opis może mieć maksymalnie 1000 znaków.',
        'pet_types.required' => 'Wybierz przynajmniej jeden rodzaj zwierzęcia.',
        'pet_types.min' => 'Wybierz przynajmniej jeden rodzaj zwierzęcia.',
        'pet_sizes.required' => 'Wybierz przynajmniej jeden rozmiar zwierzęcia.',
        'pet_sizes.min' => 'Wybierz przynajmniej jeden rozmiar zwierzęcia.',
        'max_pets.required' => 'Podaj maksymalną liczbę zwierząt.',
        'max_pets.min' => 'Minimalna liczba zwierząt to 1.',
        'max_pets.max' => 'Maksymalna liczba zwierząt to 10.',
        'price_per_hour.min' => 'Cena za godzinę nie może być ujemna.',
        'price_per_hour.max' => 'Cena za godzinę nie może przekraczać 1000 zł.',
        'price_per_day.min' => 'Cena za dzień nie może być ujemna.',
        'price_per_day.max' => 'Cena za dzień nie może przekraczać 2000 zł.',
        'price_per_week.min' => 'Cena za tydzień nie może być ujemna.',
        'price_per_week.max' => 'Cena za tydzień nie może przekraczać 10000 zł.',
    ];

    public function mount(Service $service)
    {
        // Check if user owns this service
        if ($service->sitter_id !== auth()->id()) {
            abort(403, 'Nie masz uprawnień do edycji tej usługi.');
        }

        $this->service = $service;
        $this->title = $service->title;
        $this->description = $service->description;
        $this->pet_types = $service->pet_types ?? [];
        $this->pet_sizes = $service->pet_sizes ?? [];
        $this->max_pets = $service->max_pets;
        $this->home_service = $service->home_service;
        $this->sitter_home = $service->sitter_home;
        $this->price_per_hour = $service->price_per_hour;
        $this->price_per_day = $service->price_per_day;
        $this->price_per_week = $service->price_per_week;
    }

    public function getPetTypesProperty()
    {
        return [
            'dog' => 'Psy',
            'cat' => 'Koty',
            'bird' => 'Ptaki',
            'rabbit' => 'Króliki',
            'hamster' => 'Chomiki',
            'fish' => 'Ryby',
            'reptile' => 'Gady',
            'other' => 'Inne'
        ];
    }

    public function getPetSizesProperty()
    {
        return [
            'small' => 'Małe (do 10kg)',
            'medium' => 'Średnie (10-25kg)',
            'large' => 'Duże (powyżej 25kg)'
        ];
    }

    public function validateAndSave()
    {
        $this->validate();

        // Check if at least one service type is selected
        if (!$this->home_service && !$this->sitter_home) {
            $this->addError('service_type', 'Wybierz przynajmniej jeden typ usługi.');
            return;
        }

        // Check if at least one price is set
        if (!$this->price_per_hour && !$this->price_per_day && !$this->price_per_week) {
            $this->addError('pricing', 'Ustaw przynajmniej jedną cenę.');
            return;
        }

        try {
            $this->service->update([
                'title' => $this->title,
                'description' => $this->description,
                'pet_types' => $this->pet_types,
                'pet_sizes' => $this->pet_sizes,
                'max_pets' => $this->max_pets,
                'home_service' => $this->home_service,
                'sitter_home' => $this->sitter_home,
                'price_per_hour' => $this->price_per_hour ?: null,
                'price_per_day' => $this->price_per_day ?: null,
                'price_per_week' => $this->price_per_week ?: null,
            ]);

            session()->flash('success', 'Usługa została pomyślnie zaktualizowana.');
            return redirect()->route('services.index');

        } catch (\Exception $e) {
            \Log::error('Error updating service: ' . $e->getMessage());
            session()->flash('error', 'Wystąpił błąd podczas aktualizacji usługi. Spróbuj ponownie.');
        }
    }

    public function render()
    {
        return view('livewire.services.edit-service')->layout('components.dashboard-layout');
    }
}