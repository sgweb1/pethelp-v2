<?php

namespace App\Livewire\Dashboard\Pets;

use App\Models\Pet;
use App\Models\PetType;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;

class PetForm extends Component
{
    use WithFileUploads;

    public ?Pet $pet = null;
    public bool $editing = false;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|exists:pet_types,id')]
    public string $pet_type_id = '';

    #[Validate('nullable|string|max:255')]
    public string $breed = '';

    #[Validate('nullable|date|before:today')]
    public string $birth_date = '';

    #[Validate('nullable|in:male,female')]
    public string $gender = '';

    #[Validate('nullable|numeric|min:0|max:200')]
    public string $weight = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('nullable|image|max:2048')]
    public $photo;

    #[Validate('nullable|array')]
    public array $medical_info = [];

    #[Validate('nullable|array')]
    public array $behavior_traits = [];

    #[Validate('nullable|array')]
    public array $emergency_contacts = [];

    public bool $is_active = true;

    public function mount(?Pet $pet = null): void
    {
        if ($pet && $pet->owner_id === auth()->id()) {
            $this->pet = $pet;
            $this->editing = true;
            $this->fillForm();
        }
    }

    private function fillForm(): void
    {
        $this->name = $this->pet->name ?? '';
        $this->pet_type_id = (string) ($this->pet->pet_type_id ?? '');
        $this->breed = $this->pet->breed ?? '';
        $this->birth_date = $this->pet->birth_date?->format('Y-m-d') ?? '';
        $this->gender = $this->pet->gender ?? '';
        $this->weight = $this->pet->weight ? (string) $this->pet->weight : '';
        $this->description = $this->pet->description ?? '';
        $this->medical_info = $this->pet->medical_info ?? [];
        $this->behavior_traits = $this->pet->behavior_traits ?? [];
        $this->emergency_contacts = $this->pet->emergency_contacts ?? [];
        $this->is_active = $this->pet->is_active ?? true;
    }

    #[Computed]
    public function petTypes()
    {
        return PetType::orderBy('name')->get();
    }

    public function addMedicalInfo(): void
    {
        $this->medical_info[] = [
            'type' => '',
            'description' => '',
            'date' => ''
        ];
    }

    public function removeMedicalInfo(int $index): void
    {
        unset($this->medical_info[$index]);
        $this->medical_info = array_values($this->medical_info);
    }

    public function addBehaviorTrait(): void
    {
        $this->behavior_traits[] = '';
    }

    public function removeBehaviorTrait(int $index): void
    {
        unset($this->behavior_traits[$index]);
        $this->behavior_traits = array_values($this->behavior_traits);
    }

    public function addEmergencyContact(): void
    {
        $this->emergency_contacts[] = [
            'name' => '',
            'phone' => '',
            'relationship' => ''
        ];
    }

    public function removeEmergencyContact(int $index): void
    {
        unset($this->emergency_contacts[$index]);
        $this->emergency_contacts = array_values($this->emergency_contacts);
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'owner_id' => auth()->id(),
            'name' => $this->name,
            'pet_type_id' => $this->pet_type_id,
            'breed' => $this->breed ?: null,
            'birth_date' => $this->birth_date ?: null,
            'gender' => $this->gender ?: null,
            'weight' => $this->weight ? (float) $this->weight : null,
            'description' => $this->description ?: null,
            'medical_info' => array_filter($this->medical_info),
            'behavior_traits' => array_filter($this->behavior_traits),
            'emergency_contacts' => array_filter($this->emergency_contacts, function($contact) {
                return !empty($contact['name']) || !empty($contact['phone']);
            }),
            'is_active' => $this->is_active,
        ];

        // Handle photo upload
        if ($this->photo) {
            if ($this->editing && $this->pet->photo_url && $this->pet->photo_url !== asset('images/pet-placeholder.png')) {
                // Remove old photo
                $oldPath = str_replace(asset('storage/'), '', $this->pet->photo_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $this->photo->store('pets', 'public');
            $data['photo_url'] = $path;
        }

        if ($this->editing) {
            $this->pet->update($data);
            session()->flash('success', 'Zwierzę zostało zaktualizowane.');
        } else {
            Pet::create($data);
            session()->flash('success', 'Zwierzę zostało dodane.');
        }

        $this->redirect(route('pets.index'), navigate: true);
    }

    public function cancel(): void
    {
        $this->redirect(route('pets.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.dashboard.pets.pet-form')
            ->layout('components.dashboard-layout', [
                'title' => $this->editing ? 'Edytuj zwierzę' : 'Dodaj zwierzę',
                'activeSection' => 'pets'
            ]);
    }
}
