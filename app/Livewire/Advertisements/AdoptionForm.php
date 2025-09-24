<?php

namespace App\Livewire\Advertisements;

use App\Models\Advertisement;
use App\Models\AdvertisementCategory;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdoptionForm extends Component
{
    use WithFileUploads;

    public $categoryId;
    public $advertisement;
    public $isEditing = false;

    // Basic info
    public $title = '';
    public $description = '';
    public $city = '';
    public $voivodeship = '';
    public $full_address = '';

    // Pet info
    public $pet_name = '';
    public $pet_type = '';
    public $pet_breed = '';
    public $pet_gender = '';
    public $pet_birth_date = '';
    public $pet_weight = '';
    public $pet_vaccinated = false;
    public $pet_sterilized = false;
    public $pet_health_info = '';

    // Contact info
    public $contact_phone = '';
    public $contact_email = '';
    public $show_phone = true;
    public $show_email = true;
    public $preferred_contact = 'phone';

    // Images
    public $photos = [];
    public $existingPhotos = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:2000',
        'city' => 'required|string|max:100',
        'voivodeship' => 'required|string|max:100',
        'pet_name' => 'required|string|max:100',
        'pet_type' => 'required|in:dog,cat,rabbit,bird,other',
        'pet_gender' => 'required|in:male,female',
        'pet_birth_date' => 'nullable|date|before:today',
        'pet_weight' => 'nullable|numeric|min:0|max:200',
        'contact_phone' => 'nullable|string|max:20',
        'contact_email' => 'nullable|email|max:255',
        'photos.*' => 'image|max:5120', // 5MB max
    ];

    public function mount($categoryId = null, $advertisement = null)
    {
        $this->categoryId = $categoryId;

        if ($advertisement) {
            $this->advertisement = $advertisement;
            $this->isEditing = true;
            $this->loadAdvertisementData();
        }

        // Set default contact info
        if (!$this->isEditing) {
            $this->contact_email = auth()->user()->email;
        }
    }

    public function loadAdvertisementData()
    {
        $ad = $this->advertisement;

        $this->title = $ad->title;
        $this->description = $ad->description;
        $this->city = $ad->city;
        $this->voivodeship = $ad->voivodeship;
        $this->full_address = $ad->full_address;

        $this->pet_name = $ad->pet_name;
        $this->pet_type = $ad->pet_type;
        $this->pet_breed = $ad->pet_breed;
        $this->pet_gender = $ad->pet_gender;
        $this->pet_birth_date = $ad->pet_birth_date?->format('Y-m-d');
        $this->pet_weight = $ad->pet_weight;
        $this->pet_vaccinated = $ad->pet_vaccinated;
        $this->pet_sterilized = $ad->pet_sterilized;
        $this->pet_health_info = $ad->pet_health_info;

        $this->contact_phone = $ad->contact_phone;
        $this->contact_email = $ad->contact_email;
        $this->show_phone = $ad->show_phone;
        $this->show_email = $ad->show_email;
        $this->preferred_contact = $ad->preferred_contact;

        $this->existingPhotos = $ad->images->toArray();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'advertisement_category_id' => $this->categoryId,
            'title' => $this->title,
            'description' => $this->description,
            'city' => $this->city,
            'voivodeship' => $this->voivodeship,
            'full_address' => $this->full_address,
            'pet_name' => $this->pet_name,
            'pet_type' => $this->pet_type,
            'pet_breed' => $this->pet_breed,
            'pet_gender' => $this->pet_gender,
            'pet_birth_date' => $this->pet_birth_date,
            'pet_weight' => $this->pet_weight,
            'pet_vaccinated' => $this->pet_vaccinated,
            'pet_sterilized' => $this->pet_sterilized,
            'pet_health_info' => $this->pet_health_info,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'show_phone' => $this->show_phone,
            'show_email' => $this->show_email,
            'preferred_contact' => $this->preferred_contact,
            'status' => 'draft', // Can be published later
        ];

        if ($this->isEditing) {
            $this->advertisement->update($data);
            $advertisement = $this->advertisement;
        } else {
            $advertisement = Advertisement::create($data);
        }

        // Handle photo uploads
        if ($this->photos) {
            foreach ($this->photos as $index => $photo) {
                $path = $photo->store('advertisements', 'public');

                $advertisement->images()->create([
                    'path' => $path,
                    'filename' => $photo->getClientOriginalName(),
                    'sort_order' => $index + 1,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        session()->flash('success', $this->isEditing ? 'Ogłoszenie zostało zaktualizowane!' : 'Ogłoszenie zostało dodane!');

        return redirect()->route('advertisements.index');
    }

    public function getCategoryProperty()
    {
        return AdvertisementCategory::find($this->categoryId);
    }

    public function render()
    {
        return view('livewire.advertisements.adoption-form');
    }
}