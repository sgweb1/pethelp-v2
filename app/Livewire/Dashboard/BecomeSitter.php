<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class BecomeSitter extends Component
{
    public $isOpen = false;
    public $step = 1;

    // Step 1: Basic info
    public $experience = '';
    public $pets_experience = [];
    public $service_radius = 5;

    // Step 2: Services selection
    public $selected_services = [];

    // Step 3: Availability
    public $availability_schedule = [];
    public $emergency_available = false;

    // Step 4: Pricing
    public $base_price = '';
    public $additional_info = '';

    protected $rules = [
        'experience' => 'required|string|min:50|max:500',
        'pets_experience' => 'required|array|min:1',
        'service_radius' => 'required|integer|min:1|max:50',
        'selected_services' => 'required|array|min:1',
    ];

    protected $messages = [
        'experience.required' => 'Opisz swoje doświadczenie z zwierzętami',
        'experience.min' => 'Opis powinien mieć minimum 50 znaków',
        'pets_experience.required' => 'Wybierz rodzaje zwierząt, z którymi masz doświadczenie',
        'selected_services.required' => 'Wybierz przynajmniej jedną usługę',
    ];

    public function mount()
    {
        $user = auth()->user();

        // Check if user already has sitter profile
        if ($user->isSitter()) {
            $this->isOpen = false;
        }
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset();
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validateOnly('experience');
            $this->validateOnly('pets_experience');
            $this->validateOnly('service_radius');
        }

        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function activateSitterAccount()
    {
        $this->validate();

        $user = auth()->user();
        $profile = $user->profile;

        // Update user profile to include sitter role
        $profile->update([
            'role' => $profile->role === 'owner' ? 'both' : 'sitter',
            'bio' => $this->experience,
            'service_radius' => $this->service_radius,
            'pets_experience' => json_encode($this->pets_experience),
            'emergency_available' => $this->emergency_available,
            'sitter_activated_at' => now(),
        ]);

        // Create basic services for the sitter
        foreach ($this->selected_services as $serviceType) {
            $this->createDefaultService($serviceType);
        }

        session()->flash('success', 'Gratulacje! Twoje konto Pet Sittera zostało aktywowane!');

        // Dispatch event to refresh dashboard
        $this->dispatch('refreshDashboard');
        $this->dispatch('sitter-account-activated');

        $this->closeModal();

        // Redirect to services management
        return redirect()->route('services.index');
    }

    private function createDefaultService($type)
    {
        $serviceTypes = [
            'walking' => [
                'name' => 'Spacery z psem',
                'category' => 2, // Walking category ID
                'description' => 'Profesjonalne spacery z Twoim psem',
                'price' => 30,
            ],
            'home_care' => [
                'name' => 'Opieka w domu właściciela',
                'category' => 1, // Home care category ID
                'description' => 'Opieka nad zwierzęciem w Twoim domu',
                'price' => 50,
            ],
            'sitter_home' => [
                'name' => 'Opieka u opiekuna',
                'category' => 3, // Sitter home category ID
                'description' => 'Opieka nad zwierzęciem w moim domu',
                'price' => 60,
            ],
        ];

        if (isset($serviceTypes[$type])) {
            $serviceData = $serviceTypes[$type];

            auth()->user()->services()->create([
                'service_category_id' => $serviceData['category'],
                'title' => $serviceData['name'],
                'description' => $serviceData['description'],
                'price_per_hour' => $serviceData['price'],
                'is_active' => false, // Start as inactive, let sitter configure first
                'duration_minutes' => 60,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.become-sitter');
    }
}