<?php

namespace App\Livewire;

use App\Helpers\PhotoStorageHelper;
use App\Models\PetType;
use App\Models\ServiceCategory;
use App\Models\WizardDraft;
use App\Services\AI\HybridAIAssistant;
use App\Services\GUSApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Komponent wizard'a rejestracji Pet Sittera w stylu Airbnb.
 *
 * Implementuje wieloetapowy proces rejestracji z czystym, fullscreen design,
 * progressive disclosure i smooth transitions. Zapewnia intuicyjny UX
 * podobny do procesu rejestracji gospodarzy na Airbnb.
 *
 * @version 1.0.0
 */
class PetSitterWizard extends Component
{
    use WithFileUploads;

    /**
     * Aktualny krok wizard'a (1-11).
     */
    public int $currentStep = 1;

    /**
     * Maksymalna liczba kroków w wizard'zie.
     */
    public int $maxSteps = 11;

    /**
     * Dane zebrane w trakcie procesu rejestracji.
     */
    public array $wizardData = [];

    /**
     * Czy panel AI Assistant jest widoczny.
     */
    public bool $showAIPanel = false;

    /**
     * Czy wizard jest aktywny (fullscreen mode).
     */
    public bool $isActive = false;

    /**
     * Aktualny draft użytkownika.
     */
    public $currentDraft = null;

    /**
     * Flaga wskazująca czy draft został już załadowany.
     */
    private bool $draftLoaded = false;

    /**
     * Stan animacji i loading states.
     */
    public bool $isTransitioning = false;

    public bool $isSaving = false;

    public bool $isValidating = false;

    public string $lastValidationMessage = '';

    public bool $showSuccessFeedback = false;

    // ===== KROK 1: WPROWADZENIE =====
    public string $motivation = '';

    public string $aiEditPrompt = '';

    public bool $isEditingWithAI = false;

    // ===== AI ASSISTANT FOR EXPERIENCE DESCRIPTION =====
    public string $aiEditPromptExperience = '';

    public bool $isEditingExperienceWithAI = false;

    // ===== KROK 2: DOŚWIADCZENIE Z ZWIERZĘTAMI =====
    public array $petExperience = [];

    public string $experienceDescription = '';

    public int $yearsOfExperience = 0;

    // ===== KROK 3: RODZAJE ZWIERZĄT =====
    public array $animalTypes = [];

    public array $animalSizes = [];

    // ===== KROK 4: USŁUGI =====
    public array $serviceTypes = [];

    public array $specialServices = [];

    // ===== KROK 5: LOKALIZACJA I PROMIEŃ =====
    public string $address = '';

    // Strukturalne dane adresowe z Nominatim API
    public string $road = '';

    public string $house_number = '';

    public string $postcode = '';

    public string $city = '';

    public string $town = '';

    public string $village = '';

    public string $municipality = '';

    public string $county = '';

    public string $state = '';

    public string $gus_city_name = '';

    public string $district = '';

    public float $latitude = 0;

    public float $longitude = 0;

    public int $serviceRadius = 10;

    /**
     * Szacowana liczba potencjalnych klientów w promieniu obsługi.
     * Wyliczana dynamicznie na podstawie rzeczywistych użytkowników w bazie.
     */
    public int $estimatedClients = 0;

    // ===== KROK 6: DOSTĘPNOŚĆ =====
    public array $weeklyAvailability = [];

    public bool $emergencyAvailable = false;

    public bool $flexibleSchedule = true;

    // ===== KROK 7: DOM I OGRÓD =====
    public string $homeType = '';

    public bool $hasGarden = false;

    public bool $isSmoking = false;

    public bool $hasOtherPets = false;

    public array $otherPets = [];

    // ===== KROK 8: ZDJĘCIA =====
    public $profilePhoto;

    public $tempHomePhoto; // Tymczasowe zdjęcie domu do zapisania

    private $profilePhotoProcessed = false; // Flaga zapobiegająca duplikacji

    public array $homePhotos = [];

    public array $existingPhotos = [];

    // ===== KROK 9: WERYFIKACJA =====
    public $identityDocument;

    public bool $hasCriminalRecordDeclaration = false; // Oświadczenie o niekaralności (switch)

    public array $references = [];

    // ===== KROK 10: CENNIK =====
    public array $servicePricing = [];

    public string $pricingStrategy = 'competitive'; // competitive, premium, budget

    // ===== KROK 11: PODSUMOWANIE =====
    public bool $agreedToTerms = false;

    public bool $marketingConsent = false;

    /**
     * Reguły walidacji dla poszczególnych kroków.
     * UWAGA: Kolejność kroków została zmieniona - najpierw zbieramy dane, potem AI generuje opisy.
     */
    protected array $stepValidationRules = [
        1 => [
            'animalTypes' => 'required|array|min:1',
            'animalSizes' => 'required_if:animalTypes.*,dogs,cats|array|min:1',
        ], // Rodzaje zwierząt
        2 => ['serviceTypes' => 'required|array|min:1'], // Usługi
        3 => [
            'address' => 'required|string|min:10|max:200',
            'serviceRadius' => 'required|integer|min:1|max:50',
        ], // Lokalizacja
        4 => ['weeklyAvailability' => 'required|array|min:1'], // Dostępność
        5 => ['homeType' => 'required|string'], // Dom i ogród
        6 => ['motivation' => 'required|string|min:50|max:500'], // Motywacja (z AI - po zebraniu kontekstu)
        7 => [
            'petExperience' => 'required|array|min:1',
            'experienceDescription' => 'required|string|min:100|max:1000',
            'yearsOfExperience' => 'required|integer|min:0|max:50',
        ], // Doświadczenie (z AI)
        8 => [], // Zdjęcia są opcjonalne
        9 => [], // Weryfikacja jest opcjonalna
        10 => ['servicePricing' => 'required|array|min:1'], // Cennik
        11 => ['agreedToTerms' => 'accepted'], // Finalizacja
    ];

    /**
     * Komunikaty błędów walidacji.
     *
     * @var array
     */
    protected $messages = [
        'motivation.required' => 'Opowiedz nam, dlaczego chcesz zostać pet sitterem',
        'motivation.min' => 'Opis powinien mieć minimum 50 znaków',
        'motivation.max' => 'Opis może mieć maksymalnie 500 znaków',
        'petExperience.required' => 'Wybierz swoje doświadczenie z zwierzętami',
        'petExperience.min' => 'Wybierz przynajmniej jeden rodzaj doświadczenia',
        'experienceDescription.required' => 'Opisz swoje doświadczenie szczegółowo',
        'experienceDescription.min' => 'Opis powinien mieć minimum 100 znaków',
        'experienceDescription.max' => 'Opis może mieć maksymalnie 1000 znaków',
        'yearsOfExperience.required' => 'Podaj lata doświadczenia',
        'yearsOfExperience.integer' => 'Lata doświadczenia muszą być liczbą',
        'yearsOfExperience.min' => 'Podaj liczbę lat od 0 wzwyż',
        'animalTypes.required' => 'Wybierz rodzaje zwierząt, którymi się zajmujesz',
        'animalTypes.min' => 'Wybierz przynajmniej jeden rodzaj zwierzęcia',
        'animalSizes.required' => 'Wybierz rozmiary zwierząt',
        'animalSizes.required_if' => 'Wybierz rozmiary dla psów lub kotów',
        'animalSizes.min' => 'Wybierz przynajmniej jeden rozmiar',
        'serviceTypes.required' => 'Wybierz przynajmniej jedną usługę',
        'serviceTypes.min' => 'Wybierz przynajmniej jedną usługę',
        'address.required' => 'Podaj swój adres',
        'address.min' => 'Adres musi mieć minimum 10 znaków',
        'serviceRadius.required' => 'Ustaw promień działania',
        'serviceRadius.integer' => 'Promień musi być liczbą',
        'serviceRadius.min' => 'Minimalny promień to 1 km',
        'serviceRadius.max' => 'Maksymalny promień to 50 km',
        'weeklyAvailability.required' => 'Ustaw swoją dostępność',
        'weeklyAvailability.min' => 'Zaznacz przynajmniej jeden dzień',
        'homeType.required' => 'Opisz swój dom',
        'servicePricing.required' => 'Ustaw ceny swoich usług',
        'servicePricing.min' => 'Ustaw ceny dla przynajmniej jednej usługi',
        'agreedToTerms.accepted' => 'Musisz zaakceptować regulamin',
    ];

    /**
     * Inicjalizacja komponentu - sprawdzenie stanu użytkownika.
     */
    public function mount(?int $step = null): void
    {
        // Sprawdź czy użytkownik już ukończył proces rejestracji pet sittera
        if (Auth::user() && Auth::user()->profile && Auth::user()->profile->sitter_activated_at) {
            session()->flash('info', 'Jesteś już zarejestrowanym pet sitterem.');
            $this->redirectRoute('profile.dashboard');

            return;
        }

        // Spróbuj załadować istniejący draft
        $this->loadDraft();

        // Inicjalizacja domyślnych wartości (jeśli nie załadowano draft'u)
        if (! $this->currentDraft) {
            $this->initializeDefaults();
        }

        // Automatycznie aktywuj wizard - zacznij od razu od kroku 1
        $this->isActive = true;

        // Jeśli podano krok w URL, ustaw go jako aktualny krok (tylko w lokalnym środowisku)
        if ($step !== null && app()->environment('local')) {
            if ($step >= 1 && $step <= $this->maxSteps) {
                $this->currentStep = $step;
                session()->flash('info', "Otworzyłeś wizard bezpośrednio na kroku {$step} (tryb deweloperski)");
            }
        }
    }

    /**
     * Inicjalizuje domyślne wartości dla formularza.
     */
    private function initializeDefaults(): void
    {
        $this->weeklyAvailability = [
            'monday' => ['available' => false, 'start' => '09:00', 'end' => '17:00'],
            'tuesday' => ['available' => false, 'start' => '09:00', 'end' => '17:00'],
            'wednesday' => ['available' => false, 'start' => '09:00', 'end' => '17:00'],
            'thursday' => ['available' => false, 'start' => '09:00', 'end' => '17:00'],
            'friday' => ['available' => false, 'start' => '09:00', 'end' => '17:00'],
            'saturday' => ['available' => false, 'start' => '10:00', 'end' => '16:00'],
            'sunday' => ['available' => false, 'start' => '10:00', 'end' => '16:00'],
        ];

        // Inicjalizuj servicePricing jako pustą tablicę
        // Frontend będzie zarządzał strukturą jako prosty obiekt {serviceKey: price}
        $this->servicePricing = [];
    }

    /**
     * Lifecycle hook wywoływany gdy promień obsługi się zmienia.
     * Automatycznie przelicza potencjalną liczbę klientów.
     *
     * @param  int  $value  Nowa wartość promienia
     */
    public function updatedServiceRadius(int $value): void
    {
        // Przelicz potencjalnych klientów z nowym promieniem
        $this->calculatePotentialClients();

        Log::info('Promień obsługi zaktualizowany', [
            'new_radius' => $value,
            'estimated_clients' => $this->estimatedClients,
        ]);
    }

    /**
     * Lifecycle hook wywoływany gdy szerokość geograficzna się zmienia.
     * Automatycznie przelicza estymację gdy mamy komplet danych.
     *
     * @param  float  $value  Nowa wartość latitude
     */
    public function updatedLatitude(float $value): void
    {
        // Przelicz estymację tylko jeśli mamy kompletne współrzędne
        if ($this->latitude != 0 && $this->longitude != 0 && $this->serviceRadius > 0) {
            $this->calculatePotentialClients();

            Log::info('Współrzędne zaktualizowane (latitude)', [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius' => $this->serviceRadius,
                'estimated_clients' => $this->estimatedClients,
            ]);
        }
    }

    /**
     * Lifecycle hook wywoływany gdy długość geograficzna się zmienia.
     * Automatycznie przelicza estymację gdy mamy komplet danych.
     *
     * @param  float  $value  Nowa wartość longitude
     */
    public function updatedLongitude(float $value): void
    {
        // Przelicz estymację tylko jeśli mamy kompletne współrzędne
        if ($this->latitude != 0 && $this->longitude != 0 && $this->serviceRadius > 0) {
            $this->calculatePotentialClients();

            Log::info('Współrzędne zaktualizowane (longitude)', [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius' => $this->serviceRadius,
                'estimated_clients' => $this->estimatedClients,
            ]);
        }
    }

    /**
     * Mapuje aktualny krok wizarda na odpowiedni plik widoku.
     *
     * Nowa kolejność kroków - najpierw zbieramy dane, potem AI generuje opisy.
     *
     * @return int Numer pliku kroku do załadowania
     */
    public function getStepFileNumber(): int
    {
        // Po refaktoryzacji: każdy krok odpowiada numerowi pliku widoku
        // Krok 1 → step-1.blade.php, Krok 2 → step-2.blade.php, itd.
        return $this->currentStep;
    }

    /**
     * Aktywuje wizard w trybie fullscreen.
     */
    public function activateWizard(): void
    {
        $this->isActive = true;
        $this->currentStep = 1;

        // Wyślij event do JavaScript dla smooth animation
        $this->dispatch('wizard-activated');
    }

    /**
     * Dezaktywuje wizard i wraca do poprzedniej strony.
     */
    public function deactivateWizard(): void
    {
        $this->isActive = false;
        $this->reset();

        // Wyślij event do JavaScript
        $this->dispatch('wizard-deactivated');
    }

    /**
     * Przełącza wybór rodzaju doświadczenia.
     */
    public function togglePetExperience(string $value): void
    {
        if (in_array($value, $this->petExperience)) {
            $this->petExperience = array_values(array_filter($this->petExperience, fn ($item) => $item !== $value));
        } else {
            $this->petExperience[] = $value;
        }
    }

    /**
     * Obsługa zaznaczania rodzajów zwierząt w kroku 3.
     */
    public function toggleAnimalType(string $value): void
    {
        \Log::info('toggleAnimalType() została wywołana', [
            'value' => $value,
            'current_animalTypes' => $this->animalTypes,
            'step' => $this->currentStep,
        ]);

        if (in_array($value, $this->animalTypes)) {
            $this->animalTypes = array_values(array_filter($this->animalTypes, fn ($item) => $item !== $value));
        } else {
            $this->animalTypes[] = $value;
        }

        \Log::info('toggleAnimalType() po zmianie', [
            'new_animalTypes' => $this->animalTypes,
        ]);

        // Wyczyść błędy walidacji po zmianie
        $this->resetValidation('animalTypes');

        // Livewire automatycznie odświeży widok
    }

    /**
     * Obsługa zaznaczania rozmiarów zwierząt w kroku 3.
     */
    public function toggleAnimalSize(string $value): void
    {
        \Log::info('toggleAnimalSize() została wywołana', [
            'value' => $value,
            'current_animalSizes' => $this->animalSizes,
            'step' => $this->currentStep,
        ]);

        if (in_array($value, $this->animalSizes)) {
            $this->animalSizes = array_values(array_filter($this->animalSizes, fn ($item) => $item !== $value));
        } else {
            $this->animalSizes[] = $value;
        }

        \Log::info('toggleAnimalSize() po zmianie', [
            'new_animalSizes' => $this->animalSizes,
        ]);

        // Wyczyść błędy walidacji po zmianie
        $this->resetValidation('animalSizes');

        // Livewire automatycznie odświeży widok
    }

    /**
     * Zapisuje dane z Alpine.js dla kroku 2.
     */
    public function saveStep2Data(array $petExperience): void
    {
        \Log::info('saveStep2Data() została wywołana', [
            'petExperience' => $petExperience,
            'step' => $this->currentStep,
        ]);

        $this->petExperience = $petExperience;

        \Log::info('saveStep2Data() - dane zapisane', [
            'new_petExperience' => $this->petExperience,
        ]);
    }

    /**
     * Zapisuje dane z Alpine.js dla kroku 3.
     */
    public function saveStep3Data(array $animalTypes, array $animalSizes): void
    {
        \Log::info('saveStep3Data() została wywołana', [
            'animalTypes' => $animalTypes,
            'animalSizes' => $animalSizes,
            'step' => $this->currentStep,
        ]);

        $this->animalTypes = $animalTypes;
        $this->animalSizes = $animalSizes;

        \Log::info('saveStep3Data() - dane zapisane', [
            'new_animalTypes' => $this->animalTypes,
            'new_animalSizes' => $this->animalSizes,
        ]);
    }

    /**
     * Obsługa zaznaczania rodzajów usług w kroku 4.
     *
     * Aktualizuje listę zaznaczonych usług i emituje event do frontendu
     * aby zsynchronizować WizardState.
     *
     * @param  string  $value  Klucz usługi do zaznaczenia/odznaczenia
     */
    public function toggleServiceType(string $value): void
    {
        \Log::info('toggleServiceType() została wywołana', [
            'value' => $value,
            'current_serviceTypes' => $this->serviceTypes,
            'step' => $this->currentStep,
        ]);

        $wasSelected = in_array($value, $this->serviceTypes);

        if ($wasSelected) {
            $this->serviceTypes = array_values(array_filter($this->serviceTypes, fn ($item) => $item !== $value));
        } else {
            $this->serviceTypes[] = $value;
        }

        \Log::info('toggleServiceType() po zmianie', [
            'new_serviceTypes' => $this->serviceTypes,
            'action' => $wasSelected ? 'removed' : 'added',
        ]);

        // Zapisz draft
        $this->saveDraft();

        // Wyślij event do frontendu aby zaktualizować WizardState
        $this->dispatch('service-types-updated', [
            'serviceTypes' => $this->serviceTypes,
            'action' => $wasSelected ? 'removed' : 'added',
            'serviceKey' => $value,
        ]);
    }

    /**
     * Szybkie dodanie usługi z kroku 10 (cennik).
     *
     * Pozwala na dodanie usługi bez przechodzenia do kroku 4.
     * Automatycznie dodaje usługę do selectedServices i zapisuje draft.
     *
     * @param  string  $serviceKey  Klucz usługi do dodania
     */
    public function quickAddService(string $serviceKey): void
    {
        \Log::info('🚀 quickAddService() wywołana', [
            'serviceKey' => $serviceKey,
            'current_serviceTypes' => $this->serviceTypes,
            'step' => $this->currentStep,
        ]);

        // Sprawdź czy usługa nie jest już dodana
        if (! in_array($serviceKey, $this->serviceTypes)) {
            $this->serviceTypes[] = $serviceKey;

            // Zapisz draft
            $this->saveDraft();

            \Log::info('✅ Usługa dodana pomyślnie', [
                'serviceKey' => $serviceKey,
                'new_serviceTypes' => $this->serviceTypes,
            ]);

            // Wyślij event do frontendu
            $this->dispatch('service-added', [
                'serviceKey' => $serviceKey,
                'success' => true,
                'message' => 'Usługa została dodana!',
            ]);
        } else {
            \Log::info('ℹ️ Usługa już dodana', [
                'serviceKey' => $serviceKey,
            ]);

            $this->dispatch('service-added', [
                'serviceKey' => $serviceKey,
                'success' => false,
                'message' => 'Ta usługa jest już dodana',
            ]);
        }
    }

    /**
     * Aktualizuje strategię cenową (krok 10).
     *
     * Obsługuje zmianę strategii cenowej przez użytkownika
     * i automatycznie zapisuje draft.
     *
     * @param  string  $strategy  Nazwa strategii (budget|competitive|premium)
     */
    public function updatePricingStrategy(string $strategy): void
    {
        \Log::info('💰 updatePricingStrategy() wywołana', [
            'strategy' => $strategy,
            'old_strategy' => $this->pricingStrategy,
            'step' => $this->currentStep,
        ]);

        $this->pricingStrategy = $strategy;

        // Zapisz draft
        $this->saveDraft();

        \Log::info('✅ Strategia cenowa zaktualizowana', [
            'new_strategy' => $this->pricingStrategy,
        ]);
    }

    /**
     * Aktualizuje cenę konkretnej usługi (krok 10).
     *
     * Obsługuje zmianę ceny usługi przez użytkownika.
     * Frontend przesyła prosty obiekt {serviceKey: price}.
     * Backend przechowuje w tej samej strukturze.
     *
     * @param  array  $pricing  Obiekt z cenami usług {serviceKey: price}
     */
    public function updateServicePricing(array $pricing): void
    {
        \Log::info('💰 updateServicePricing() wywołana', [
            'pricing' => $pricing,
            'old_servicePricing' => $this->servicePricing,
            'step' => $this->currentStep,
        ]);

        // Zachowaj prostą strukturę {serviceKey: price}
        // Usługi z ceną > 0 są automatycznie "enabled"
        $this->servicePricing = array_filter($pricing, fn ($price) => $price > 0);

        // Zapisz draft
        $this->saveDraft();

        \Log::info('✅ Cennik zaktualizowany', [
            'new_servicePricing' => $this->servicePricing,
        ]);
    }

    /**
     * Obsługa zaznaczania specjalnych usług w kroku 4.
     */
    public function toggleSpecialService(string $value): void
    {
        \Log::info('toggleSpecialService() została wywołana', [
            'value' => $value,
            'current_specialServices' => $this->specialServices,
            'step' => $this->currentStep,
        ]);

        if (in_array($value, $this->specialServices)) {
            $this->specialServices = array_values(array_filter($this->specialServices, fn ($item) => $item !== $value));
        } else {
            $this->specialServices[] = $value;
        }

        \Log::info('toggleSpecialService() po zmianie', [
            'new_specialServices' => $this->specialServices,
        ]);

        // Livewire automatycznie odświeży widok
    }

    /**
     * Przechodzi do następnego kroku z walidacją i animacjami.
     */
    public function nextStep(): void
    {
        // DEBUG: loguj wywołanie metody
        \Log::info('nextStep() została wywołana', [
            'current_step' => $this->currentStep,
            'motivation_length' => strlen($this->motivation),
            'motivation_content' => substr($this->motivation, 0, 100).'...',
        ]);

        $this->isValidating = true;
        $this->lastValidationMessage = '';

        try {
            $this->validateCurrentStep();

            // Debug: loguj sukces walidacji
            \Log::info('Validation passed in step '.$this->currentStep, [
                'motivation_length' => strlen($this->motivation),
                'current_step' => $this->currentStep,
            ]);

            // Pokaż animację przejścia
            $this->isTransitioning = true;
            $this->dispatch('step-transition-start', ['direction' => 'forward']);

            if ($this->currentStep < $this->maxSteps) {
                $this->currentStep++;

                // Wyczyść cache AI dla nowego kroku
                $this->cachedAISuggestions = [];

                // Zamknij AI Panel dla kroków 3 i 4 oraz 6 i dalszych (krok 5 ma swój sidebar)
                if ($this->currentStep >= 3 && $this->currentStep != 5) {
                    $this->showAIPanel = false;
                }

                // Włącz AI Panel dla kroku 5 z innym zawartością
                if ($this->currentStep == 5) {
                    $this->showAIPanel = true;
                }

                // Automatycznie zapisz draft po przejściu do następnego kroku
                $this->isSaving = true;
                $this->saveDraft();
                $this->isSaving = false;

                // Pokaż sukces walidacji
                $this->showSuccessFeedback = true;
                $this->lastValidationMessage = 'Krok ukończony pomyślnie!';

                $this->dispatch('step-changed', [
                    'step' => $this->currentStep,
                    'direction' => 'forward',
                    'animated' => true,
                ]);

                // Ukryj feedback po 2 sekundach
                $this->dispatch('hide-success-feedback-after', ['delay' => 2000]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->lastValidationMessage = 'Proszę poprawić błędy przed kontynuowaniem';
            $this->dispatch('validation-failed', ['errors' => $e->errors()]);

            // Debug: loguj błędy walidacji
            \Log::info('Validation failed in step '.$this->currentStep, [
                'errors' => $e->errors(),
                'motivation_length' => strlen($this->motivation),
                'motivation_content' => $this->motivation,
            ]);
        } finally {
            $this->isValidating = false;
            $this->isTransitioning = false;
        }
    }

    /**
     * Wraca do poprzedniego kroku z animacją.
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->isTransitioning = true;
            $this->dispatch('step-transition-start', ['direction' => 'backward']);

            $this->currentStep--;

            // Wyczyść cache AI dla nowego kroku
            $this->cachedAISuggestions = [];

            // Pokaż AI Panel gdy wracamy do kroków 1, 2 i 5
            if ($this->currentStep <= 2 || $this->currentStep == 5) {
                $this->showAIPanel = true;
            }

            $this->dispatch('step-changed', [
                'step' => $this->currentStep,
                'direction' => 'backward',
                'animated' => true,
            ]);

            $this->isTransitioning = false;
        }
    }

    /**
     * Przeskakuje do konkretnego kroku (tylko do przodu).
     */
    public function goToStep(int $step): void
    {
        if ($step <= $this->currentStep && $step >= 1 && $step <= $this->maxSteps) {
            $this->currentStep = $step;
            $this->dispatch('step-changed', ['step' => $this->currentStep]);
        }
    }

    /**
     * Waliduje aktualny krok.
     */
    private function validateCurrentStep(): void
    {
        if (isset($this->stepValidationRules[$this->currentStep]) && ! empty($this->stepValidationRules[$this->currentStep])) {
            // Specjalna logika dla kroku 1 - walidacja animalSizes
            if ($this->currentStep === 1) {
                // Sprawdź czy wybrano psy lub koty
                $hasDogsCats = array_intersect(['dogs', 'cats'], $this->animalTypes);

                if (! empty($hasDogsCats) && empty($this->animalSizes)) {
                    $this->addError('animalSizes', $this->messages['animalSizes.required_if'] ?? 'Wybierz rozmiary dla psów lub kotów');
                }

                // Walidacja animalTypes
                if (empty($this->animalTypes)) {
                    $this->addError('animalTypes', $this->messages['animalTypes.required'] ?? 'Wybierz rodzaje zwierząt, którymi się zajmujesz');
                }

                // Jeśli są błędy, rzuć wyjątek
                if ($this->getErrorBag()->isNotEmpty()) {
                    throw new \Illuminate\Validation\ValidationException(validator([], []));
                }
            } else {
                $this->validate($this->stepValidationRules[$this->currentStep], $this->messages);
            }
        }
    }

    /**
     * Przełącza widoczność panelu AI.
     */
    public function toggleAIPanel(): void
    {
        $this->showAIPanel = ! $this->showAIPanel;
        $this->dispatch('ai-panel-toggled', ['visible' => $this->showAIPanel]);
    }

    /**
     * Cache dla sugestii AI - żeby nie wywołować za każdym razem.
     */
    private array $cachedAISuggestions = [];

    /**
     * Generuje inteligentne sugestie AI na podstawie aktualnego kroku i danych użytkownika.
     *
     * Wykorzystuje system HybridAIAssistant do dostarczania spersonalizowanych
     * porad i wskazówek na każdym etapie procesu rejestracji.
     *
     * @return array Sugestie AI dla aktualnego kroku
     */
    public function getAISuggestions(): array
    {
        // Sprawdź cache
        $cacheKey = "step_{$this->currentStep}";
        if (isset($this->cachedAISuggestions[$cacheKey])) {
            return $this->cachedAISuggestions[$cacheKey];
        }

        try {
            $aiAssistant = app(HybridAIAssistant::class);

            // Przygotuj dane z aktualnego stanu wizarda
            $wizardData = $this->prepareWizardDataForAI();

            // Dodatkowy kontekst
            $context = [
                'user_type' => 'pet_sitter',
                'time_of_day' => now()->format('H:i'),
                'step_name' => $this->getStepName($this->currentStep),
            ];

            // Pobierz sugestie z AI
            $suggestions = $aiAssistant->getStepSuggestions($this->currentStep, $wizardData, $context);

            // Transformuj do formatu oczekiwanego przez frontend
            $result = $this->transformAISuggestionsForFrontend($suggestions);

            // Zapisz w cache
            $this->cachedAISuggestions[$cacheKey] = $result;

            return $result;

        } catch (\Exception $e) {
            // Fallback w przypadku błędu AI
            \Log::warning('AI suggestions failed, using fallback', [
                'step' => $this->currentStep,
                'error' => $e->getMessage(),
            ]);

            $fallback = $this->getFallbackSuggestions();
            $this->cachedAISuggestions[$cacheKey] = $fallback;

            return $fallback;
        }
    }

    /**
     * Przygotowuje dane wizarda dla systemu AI.
     *
     * Konwertuje aktualne dane wizarda do formatu oczekiwanego przez AI Assistant.
     *
     * @return array Dane wizarda w formacie AI
     */
    private function prepareWizardDataForAI(): array
    {
        $user = Auth::user();

        return [
            // Dane użytkownika
            'name' => $user->name ?? '',
            'email' => $user->email ?? '',
            'city' => $this->extractCityFromAddress($this->address),

            // Dane z wizarda
            'current_step' => $this->currentStep,
            'motivation' => $this->motivation,
            'pet_experience' => $this->petExperience,
            'experience_description' => $this->experienceDescription,
            'years_of_experience' => $this->yearsOfExperience,
            'animal_types' => $this->animalTypes,
            'animal_sizes' => $this->animalSizes,
            'service_types' => $this->serviceTypes,
            'special_services' => $this->specialServices,
            'address' => $this->address,
            'service_radius' => $this->serviceRadius,
            'weekly_availability' => $this->weeklyAvailability ?? [],
            'emergency_available' => $this->emergencyAvailable,
            'flexible_schedule' => $this->flexibleSchedule,
            'home_type' => $this->homeType,
            'has_garden' => $this->hasGarden,
            'is_smoking' => $this->isSmoking,
            'has_other_pets' => $this->hasOtherPets,
            'other_pets' => $this->otherPets,

            // Krok 8: Zdjęcia
            'has_profile_photo' => ! empty($this->profilePhoto),
            'home_photos_count' => count($this->homePhotos),

            // Krok 9: Weryfikacja
            'has_identity_document' => ! empty($this->identityDocument),
            'has_criminal_record' => ! empty($this->criminalRecord),
            'references_count' => count($this->references),

            // Krok 10: Cennik
            'pricing_strategy' => $this->pricingStrategy,
            'service_pricing' => $this->servicePricing,

            // Krok 11: Finalizacja
            'agreed_to_terms' => $this->agreedToTerms,
            'marketing_consent' => $this->marketingConsent,

            // Metadane
            'completion_percentage' => $this->getProgressPercentage(),
            'completed_steps' => array_filter(range(1, $this->currentStep - 1), fn ($step) => $this->isStepCompleted($step)),
        ];
    }

    /**
     * Pobiera nazwę kroku dla lepszego kontekstu AI.
     *
     * @param  int  $step  Numer kroku
     * @return string Nazwa kroku
     */
    private function getStepName(int $step): string
    {
        // Nowa kolejność kroków po refaktoryzacji
        $stepNames = [
            1 => 'animal_types',      // Rodzaje zwierząt
            2 => 'services',           // Usługi
            3 => 'location',           // Lokalizacja i promień
            4 => 'availability',       // Dostępność
            5 => 'home_environment',   // Dom i ogród
            6 => 'introduction',       // Motywacja (AI z kontekstem)
            7 => 'experience',         // Doświadczenie (AI z kontekstem)
            8 => 'photos',             // Zdjęcia
            9 => 'verification',       // Weryfikacja
            10 => 'pricing',           // Cennik
            11 => 'completion',        // Finalizacja
        ];

        return $stepNames[$step] ?? 'unknown';
    }

    /**
     * Transformuje odpowiedź AI do formatu oczekiwanego przez frontend.
     *
     * @param  array  $aiSuggestions  Sugestie z AI Assistant
     * @return array Sugestie w formacie frontend
     */
    private function transformAISuggestionsForFrontend(array $aiSuggestions): array
    {
        return [
            'title' => $aiSuggestions['title'] ?? 'Sugestie AI',
            'type' => $aiSuggestions['type'] ?? 'rule_based',
            'items' => $this->extractSuggestionsItems($aiSuggestions),
            'warnings' => $aiSuggestions['warnings'] ?? [],
            'recommendations' => $aiSuggestions['recommendations'] ?? [],
            'insights' => $aiSuggestions['insights'] ?? [],
            'market_data' => $aiSuggestions['pricing'] ?? $aiSuggestions['market_insights'] ?? null,
        ];
    }

    /**
     * Wyciąga elementy sugestii z różnych struktur AI.
     *
     * @param  array  $aiSuggestions  Sugestie AI
     * @return array Lista elementów sugestii
     */
    private function extractSuggestionsItems(array $aiSuggestions): array
    {
        $items = [];

        // Sprawdź różne możliwe struktury
        if (! empty($aiSuggestions['suggestions'])) {
            $items = is_array($aiSuggestions['suggestions']) ? $aiSuggestions['suggestions'] : [$aiSuggestions['suggestions']];
        } elseif (! empty($aiSuggestions['tips'])) {
            $items = is_array($aiSuggestions['tips']) ? $aiSuggestions['tips'] : [$aiSuggestions['tips']];
        } elseif (! empty($aiSuggestions['examples'])) {
            $items = is_array($aiSuggestions['examples']) ? $aiSuggestions['examples'] : [$aiSuggestions['examples']];
        } else {
            return ['Brak dostępnych sugestii dla tego kroku'];
        }

        // Upewnij się, że wszystkie elementy są stringami
        return array_map(function ($item) {
            if (is_string($item)) {
                return $item;
            } elseif (is_array($item)) {
                return implode(', ', array_filter($item, 'is_string'));
            } else {
                return 'Sugestia niedostępna';
            }
        }, $items);
    }

    /**
     * Zwraca fallback sugestie gdy AI nie działa.
     *
     * @return array Podstawowe sugestie
     */
    private function getFallbackSuggestions(): array
    {
        $fallbackSuggestions = [
            1 => [
                'title' => 'Wskazówki do opisu motywacji',
                'items' => [
                    'Opisz swoją pasję do zwierząt',
                    'Wspomnieć o doświadczeniu z własnymi pupilami',
                    'Wyjaśnij, dlaczego ludzie mogą Ci zaufać',
                ],
            ],
            2 => [
                'title' => 'Jak opisać doświadczenie',
                'items' => [
                    'Podaj konkretne przykłady opieki nad zwierzętami',
                    'Opisz różne sytuacje, z którymi się zmierzyłeś',
                    'Wspomnieć o szkoleniach lub kursach',
                ],
            ],
            3 => [
                'title' => 'Wybór zwierząt',
                'items' => [
                    'Wybierz tylko te zwierzęta, z którymi masz doświadczenie',
                    'Małe psy są najłatwiejsze dla początkujących',
                    'Koty wymagają innego podejścia niż psy',
                ],
            ],
            4 => [
                'title' => 'Dobór usług',
                'items' => [
                    'Zacznij od 2-3 podstawowych usług',
                    'Spacery z psem to najpopularniejsza usługa',
                    'Opieka nocna przynosi najwyższe zyski',
                ],
            ],
            5 => [
                'title' => 'Lokalizacja i promień',
                'items' => [
                    'Promień 5-10km to dobry start',
                    'Sprawdź konkurencję w swojej okolicy',
                    'Większy promień = więcej klientów',
                ],
            ],
            6 => [
                'title' => 'Planowanie dostępności',
                'items' => [
                    'Weekendy są najbardziej pożądane',
                    'Elastyczność zwiększa szanse na rezerwacje',
                    'Unikaj zbyt wąskich okien czasowych',
                ],
            ],
            7 => [
                'title' => 'Opis domu',
                'items' => [
                    'Ogród to duży atut dla właścicieli psów',
                    'Środowisko bez dymu jest ważne',
                    'Bądź szczery co do swoich zwierząt',
                ],
            ],
            8 => [
                'title' => 'Zdjęcia profilu',
                'items' => [
                    'Uśmiechnij się naturalnie na zdjęciu profilowym',
                    'Pokaż czyste i bezpieczne przestrzenie',
                    'Naturalne światło działa najlepiej',
                ],
            ],
            9 => [
                'title' => 'Weryfikacja profilu',
                'items' => [
                    'Dokument tożsamości to podstawa zaufania',
                    'Referencje znacznie zwiększają wiarygodność',
                    'Zaświadczenie o niekaralności wyróżnia na rynku',
                ],
            ],
            10 => [
                'title' => 'Strategia cenowa',
                'items' => [
                    'Sprawdź ceny konkurencji w okolicy',
                    'Zacznij od cen competitive, podnieś po zebraniu opinii',
                    'Weekend i święta można wycenić 20-30% wyżej',
                ],
            ],
            11 => [
                'title' => 'Finalizacja rejestracji',
                'items' => [
                    'Sprawdź wszystkie dane przed potwierdzeniem',
                    'Przeczytaj regulamin dokładnie',
                    'Po rejestracji będziesz mógł edytować profil',
                ],
            ],
            12 => [
                'title' => 'Podgląd profilu',
                'items' => [
                    'Sprawdź jak wygląda Twój profil dla klientów',
                    'Upewnij się, że wszystkie informacje są poprawne',
                    'Możesz wrócić i edytować dowolny krok',
                ],
            ],
        ];

        return $fallbackSuggestions[$this->currentStep] ?? [
            'title' => 'Ogólne wskazówki',
            'items' => ['Wypełnij formularz zgodnie ze swoimi możliwościami i doświadczeniem'],
        ];
    }

    /**
     * Wyciąga miejscowość i ulicę z pełnego adresu.
     *
     * Parsuje adres zwrócony przez Nominatim i wyciąga tylko najważniejsze
     * elementy: ulicę i miejscowość (bez kodu pocztowego, powiatu, województwa).
     *
     * @param  string  $address  Pełny adres
     * @return string Uproszczona lokalizacja (ulica, miejscowość)
     *
     * @example
     * extractCityFromAddress("ul. Poligon, 05-075 Droga czołgowa, powiat wołomiński, woj. mazowieckie")
     * // zwraca: "ul. Poligon, Droga czołgowa"
     */
    private function extractCityFromAddress(string $address): string
    {
        if (empty($address)) {
            return 'Warszawa';
        }

        // Rozdziel adres po przecinkach
        $parts = array_map('trim', explode(',', $address));

        $street = '';
        $city = '';

        foreach ($parts as $part) {
            // Pomijamy kod pocztowy (XX-XXX)
            if (preg_match('/^\d{2}-\d{3}/', $part)) {
                continue;
            }

            // Pomijamy "powiat ...", "gmina ...", "woj. ...", "województwo ..."
            if (preg_match('/(powiat|gmina|woj\.|województwo)/i', $part)) {
                continue;
            }

            // Pierwsza część z "ul.", "al." to ulica
            if (empty($street) && preg_match('/(ul\.|al\.)/i', $part)) {
                $street = $part;

                continue;
            }

            // Pierwsza inna niepusta część to miejscowość
            if (empty($city) && ! empty($part)) {
                // Usuń ewentualny kod pocztowy z początku
                $city = preg_replace('/^\d{2}-\d{3}\s+/', '', $part);
            }
        }

        // Zbuduj wynik
        if (! empty($street) && ! empty($city)) {
            return $street.', '.$city;
        } elseif (! empty($street)) {
            return $street;
        } elseif (! empty($city)) {
            return $city;
        }

        // Fallback - pierwsza część
        return trim($parts[0] ?? 'Warszawa');
    }

    /**
     * Regeneruje sugestie AI dla aktualnego kroku.
     *
     * Metoda wywoływana z frontend gdy użytkownik chce odświeżyć sugestie.
     */
    public function refreshAISuggestions(): void
    {
        // Wyczyść lokalny cache
        $cacheKey = "step_{$this->currentStep}";
        unset($this->cachedAISuggestions[$cacheKey]);

        // Wyczyść cache dla aktualnego kroku
        try {
            $aiAssistant = app(HybridAIAssistant::class);
            $wizardData = $this->prepareWizardDataForAI();
            $context = ['user_type' => 'pet_sitter'];

            $aiAssistant->clearStepCache($this->currentStep, $wizardData, $context);

            // Wyemituj event dla frontend
            $this->dispatch('ai-suggestions-refreshed', [
                'step' => $this->currentStep,
                'suggestions' => $this->getAISuggestions(),
            ]);

        } catch (\Exception $e) {
            \Log::warning('Failed to refresh AI suggestions', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Edytuje tekst motywacji za pomocą AI na podstawie instrukcji użytkownika.
     *
     * Pozwala użytkownikowi na przepisanie, uzupełnienie lub poprawę tekstu
     * za pomocą naturalnych poleceń jak "dodaj że mam 10 lat doświadczenia".
     */
    public function editMotivationWithAI(): void
    {
        \Log::info('🔧 editMotivationWithAI wywołana', [
            'aiEditPrompt' => $this->aiEditPrompt,
            'motivation_length' => strlen($this->motivation),
            'user_id' => Auth::id(),
            'step' => $this->currentStep,
        ]);

        if (empty($this->aiEditPrompt)) {
            \Log::warning('🔧 Pusta instrukcja AI');

            return;
        }

        $this->isEditingWithAI = true;

        try {
            $aiAssistant = app(HybridAIAssistant::class);

            // Przygotuj kontekst dla AI
            $context = [
                'action' => 'edit_text',
                'field' => 'motivation',
                'current_text' => $this->motivation,
                'user_instruction' => $this->aiEditPrompt,
                'user_data' => [
                    'name' => Auth::user()->name ?? '',
                    'step' => $this->currentStep,
                ],
                'requirements' => [
                    'min_length' => 50,
                    'max_length' => 500,
                    'style' => 'professional_friendly',
                    'language' => 'polish',
                ],
            ];

            // Wywołaj AI do edycji tekstu
            $editedText = $aiAssistant->editText($context);

            \Log::info('🔧 AI editText result', [
                'original_length' => strlen($this->motivation),
                'edited_length' => strlen($editedText),
                'is_empty' => empty($editedText),
                'original_preview' => substr($this->motivation, 0, 100),
                'edited_preview' => substr($editedText, 0, 100),
                'instruction' => $this->aiEditPrompt,
            ]);

            if (! empty($editedText)) {
                $this->motivation = $editedText;
                $this->aiEditPrompt = '';

                \Log::info('🔧 Text updated successfully', [
                    'new_motivation_length' => strlen($this->motivation),
                ]);

                // Wyślij feedback
                $this->dispatch('ai-suggestion-applied', [
                    'field' => 'motivation',
                    'success' => true,
                    'message' => 'Tekst został przepisany przez AI',
                ]);
            } else {
                \Log::warning('🔧 AI returned empty text!', [
                    'instruction' => $this->aiEditPrompt,
                    'original_text' => $this->motivation,
                ]);
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to edit motivation with AI', [
                'error' => $e->getMessage(),
                'prompt' => $this->aiEditPrompt,
            ]);

            $this->dispatch('ai-suggestion-applied', [
                'field' => 'motivation',
                'success' => false,
                'message' => 'Nie udało się przepisać tekstu. Spróbuj ponownie.',
            ]);
        } finally {
            $this->isEditingWithAI = false;
        }
    }

    /**
     * Alias dla generateMotivationSuggestion() - używany w widokach V4.
     */
    public function generateMotivationWithAI(): void
    {
        $this->generateMotivationSuggestion();
    }

    /**
     * Alias dla generateExperienceSuggestion() - używany w widokach V4.
     */
    public function generateExperienceWithAI(): void
    {
        $this->generateExperienceSuggestion();
    }

    /**
     * Generuje poprawiony tekst motywacji bazujący na podstawowych informacjach.
     */
    public function generateMotivationSuggestion(): void
    {
        \Log::info('🔧 generateMotivationSuggestion wywołana', [
            'user_id' => Auth::id(),
            'step' => $this->currentStep,
            'current_motivation_length' => strlen($this->motivation),
        ]);

        $this->isEditingWithAI = true;

        try {
            $aiAssistant = app(HybridAIAssistant::class);
            $user = Auth::user();

            // Kontekst dla generowania sugestii - PEŁNY kontekst z kroków 1-5
            $wizardData = $this->prepareWizardDataForAI();

            $context = [
                'action' => 'generate_motivation',
                'user_data' => [
                    'name' => $user->name ?? '',
                    'current_text' => $this->motivation,
                ],
                'wizard_context' => [
                    'animal_types' => $wizardData['animal_types'] ?? [],
                    'animal_sizes' => $wizardData['animal_sizes'] ?? [],
                    'service_types' => $wizardData['service_types'] ?? [],
                    'address' => $wizardData['address'] ?? '',
                    'city' => $wizardData['city'] ?? '',
                    'service_radius' => $wizardData['service_radius'] ?? 0,
                    'weekly_availability' => $wizardData['weekly_availability'] ?? [],
                    'home_type' => $wizardData['home_type'] ?? '',
                    'has_garden' => $wizardData['has_garden'] ?? false,
                    'has_other_pets' => $wizardData['has_other_pets'] ?? false,
                    'other_pets' => $wizardData['other_pets'] ?? '',
                ],
                'requirements' => [
                    'include_name' => true,
                    'use_context' => true,
                    'mention_animals' => ! empty($wizardData['animal_types']),
                    'mention_services' => ! empty($wizardData['service_types']),
                    'mention_location' => ! empty($wizardData['city']),
                    'professional_tone' => true,
                    'min_length' => 50,
                    'max_length' => 500,
                    'language' => 'polish',
                ],
                'suggestions' => [
                    'Wspomnieć o rodzajach zwierząt którymi się zajmujesz',
                    'Podkreślić oferowane usługi',
                    'Nawiązać do lokalizacji i obszaru działania',
                    'Wymienić prawdziwe imię dla budowania zaufania',
                    'Zachować przyjazny ale profesjonalny ton',
                ],
            ];

            $suggestion = $aiAssistant->generateText($context);

            \Log::info('🔧 AI wygenerował tekst', [
                'suggestion_length' => strlen($suggestion),
                'suggestion_preview' => substr($suggestion, 0, 100).'...',
                'is_empty' => empty($suggestion),
            ]);

            if (! empty($suggestion)) {
                $this->motivation = $suggestion;

                \Log::info('🔧 Tekst przypisany do $this->motivation', [
                    'motivation_length' => strlen($this->motivation),
                ]);

                $this->dispatch('ai-suggestion-applied', [
                    'field' => 'motivation',
                    'message' => 'Wygenerowano profesjonalny tekst motywacji',
                ]);
            } else {
                \Log::warning('🔧 AI zwrócił pusty tekst!');
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to generate motivation suggestion', ['error' => $e->getMessage()]);
        } finally {
            $this->isEditingWithAI = false;
        }
    }

    /**
     * Edytuje opis doświadczenia za pomocą AI na podstawie instrukcji użytkownika.
     *
     * Pozwala użytkownikowi na przepisanie, uzupełnienie lub poprawę opisu doświadczenia
     * za pomocą naturalnych poleceń stylistycznych i merytorycznych.
     */
    public function editExperienceWithAI(): void
    {
        \Log::info('editExperienceWithAI wywołana', [
            'aiEditPromptExperience' => $this->aiEditPromptExperience,
            'experienceDescription_length' => strlen($this->experienceDescription),
            'user_id' => Auth::id(),
        ]);

        if (empty($this->aiEditPromptExperience)) {
            \Log::warning('Pusta instrukcja AI dla doświadczenia');

            return;
        }

        $this->isEditingExperienceWithAI = true;

        try {
            $aiAssistant = app(HybridAIAssistant::class);

            // Przygotuj kontekst dla AI
            $context = [
                'action' => 'edit_text',
                'field' => 'experienceDescription',
                'current_text' => $this->experienceDescription,
                'user_instruction' => $this->aiEditPromptExperience,
                'user_data' => [
                    'name' => Auth::user()->name ?? '',
                    'step' => $this->currentStep,
                    'pet_experience' => $this->petExperience,
                    'years_of_experience' => $this->yearsOfExperience,
                ],
                'requirements' => [
                    'min_length' => 100,
                    'max_length' => 1000,
                    'style' => 'professional_detailed',
                    'language' => 'polish',
                    'focus' => 'experience_examples',
                ],
            ];

            // Wywołaj AI do edycji tekstu
            $editedText = $aiAssistant->editText($context);

            if (! empty($editedText)) {
                $this->experienceDescription = $editedText;
                $this->aiEditPromptExperience = '';

                // Wyślij feedback
                $this->dispatch('ai-suggestion-applied', [
                    'field' => 'experienceDescription',
                    'success' => true,
                    'message' => 'Opis doświadczenia został przepisany przez AI',
                ]);
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to edit experience description with AI', [
                'error' => $e->getMessage(),
                'prompt' => $this->aiEditPromptExperience,
            ]);

            $this->dispatch('ai-suggestion-applied', [
                'field' => 'experienceDescription',
                'success' => false,
                'message' => 'Nie udało się przepisać opisu AI',
            ]);
        } finally {
            $this->isEditingExperienceWithAI = false;
        }
    }

    /**
     * Generuje sugestię opisu doświadczenia bazującą na wybranych typach doświadczenia.
     */
    public function generateExperienceSuggestion(): void
    {
        \Log::info('🔧 generateExperienceSuggestion wywołana', [
            'user_id' => Auth::id(),
            'step' => $this->currentStep,
            'current_experienceDescription_length' => strlen($this->experienceDescription),
        ]);

        $this->isEditingExperienceWithAI = true;

        try {
            $aiAssistant = app(HybridAIAssistant::class);

            // Przygotuj kontekst dla AI - PEŁNY kontekst z kroków 1-6
            $wizardData = $this->prepareWizardDataForAI();

            $context = [
                'action' => 'generate_text',
                'field' => 'experienceDescription',
                'user_data' => [
                    'name' => Auth::user()->name ?? '',
                    'pet_experience' => $this->petExperience,
                    'years_of_experience' => $this->yearsOfExperience,
                    'motivation' => $this->motivation, // Dodaj motywację z kroku 6
                ],
                'wizard_context' => [
                    'animal_types' => $wizardData['animal_types'] ?? [],
                    'animal_sizes' => $wizardData['animal_sizes'] ?? [],
                    'service_types' => $wizardData['service_types'] ?? [],
                    'address' => $wizardData['address'] ?? '',
                    'city' => $wizardData['city'] ?? '',
                    'service_radius' => $wizardData['service_radius'] ?? 0,
                    'weekly_availability' => $wizardData['weekly_availability'] ?? [],
                    'home_type' => $wizardData['home_type'] ?? '',
                    'has_garden' => $wizardData['has_garden'] ?? false,
                    'has_other_pets' => $wizardData['has_other_pets'] ?? false,
                    'other_pets' => $wizardData['other_pets'] ?? '',
                ],
                'requirements' => [
                    'min_length' => 100,
                    'max_length' => 1000,
                    'style' => 'professional_detailed',
                    'use_context' => true,
                    'language' => 'polish',
                    'include_examples' => true,
                    'mention_specific_animals' => ! empty($wizardData['animal_types']),
                    'mention_services' => ! empty($wizardData['service_types']),
                    'mention_home_environment' => ! empty($wizardData['home_type']),
                ],
            ];

            // Wywołaj AI do generowania tekstu
            $generatedText = $aiAssistant->generateText($context);

            if (! empty($generatedText)) {
                $this->experienceDescription = $generatedText;

                // Wyślij feedback
                $this->dispatch('ai-suggestion-applied', [
                    'field' => 'experienceDescription',
                    'success' => true,
                    'message' => 'Opis doświadczenia został wygenerowany przez AI',
                ]);
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to generate experience description with AI', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('ai-suggestion-applied', [
                'field' => 'experienceDescription',
                'success' => false,
                'message' => 'Nie udało się wygenerować opisu AI',
            ]);
        } finally {
            $this->isEditingExperienceWithAI = false;
        }
    }

    /**
     * Oblicza procent postępu w wizard'zie.
     */
    public function getProgressPercentage(): float
    {
        return round(($this->currentStep / $this->maxSteps) * 100, 1);
    }

    /**
     * Sprawdza czy można przejść do następnego kroku.
     * Zwraca true jeśli aktualny krok jest zwalidowany.
     */
    public function getCanGoNextProperty(): bool
    {
        try {
            $this->validateCurrentStep();
            \Log::info('canGoNext: validation passed', ['step' => $this->currentStep]);

            return true;
        } catch (\Exception $e) {
            \Log::info('canGoNext: validation failed', [
                'step' => $this->currentStep,
                'error' => $e->getMessage(),
                'motivation_length' => strlen($this->motivation),
            ]);

            return false;
        }
    }

    /**
     * Sprawdza czy krok został ukończony.
     *
     * UWAGA: Kolejność kroków została zmieniona - najpierw zbieramy dane, potem AI generuje opisy.
     */
    public function isStepCompleted(int $step): bool
    {
        // Implementacja logiki sprawdzania kompletności kroków (NOWA KOLEJNOŚĆ)
        $completionChecks = [
            1 => ! empty($this->animalTypes) && (
                ! (in_array('dogs', $this->animalTypes) || in_array('cats', $this->animalTypes))
                || ! empty($this->animalSizes)
            ), // Rodzaje zwierząt
            2 => ! empty($this->serviceTypes), // Usługi
            3 => ! empty($this->address), // Lokalizacja
            4 => ! empty(array_filter($this->weeklyAvailability ?? [], fn ($day) => isset($day['available']) && $day['available'])), // Dostępność
            5 => ! empty($this->homeType), // Dom i ogród
            6 => ! empty($this->motivation), // Motywacja (z AI - po zebraniu kontekstu)
            7 => ! empty($this->petExperience) && ! empty($this->experienceDescription), // Doświadczenie (z AI)
            8 => true, // Zdjęcia opcjonalne
            9 => true, // Weryfikacja opcjonalna
            10 => ! empty($this->servicePricing) && ! empty(array_filter($this->servicePricing, fn ($price) => $price > 0)), // Cennik
            11 => $this->agreedToTerms, // Finalizacja
        ];

        return $completionChecks[$step] ?? false;
    }

    /**
     * Finalizuje rejestrację pet sittera - wywołane z kroku 12 (Preview).
     */
    public function completeSitterRegistration(): void
    {
        // Waliduj krok 11 (zgody)
        if (! $this->agreedToTerms) {
            $this->addError('agreedToTerms', 'Musisz zaakceptować regulamin.');
            $this->goToStep(11);

            return;
        }

        try {
            // Aktualizuj profil użytkownika
            $user = Auth::user();
            $profile = $user->profile;

            $profile->update([
                'role' => $profile->role === 'owner' ? 'both' : 'sitter',
                'bio' => $this->experienceDescription,
                'service_radius' => $this->serviceRadius,
                'pets_experience' => json_encode($this->animalTypes),
                'emergency_available' => $this->emergencyAvailable,
                'sitter_activated_at' => now(),
                'home_type' => $this->homeType,
                'has_garden' => $this->hasGarden,
                'other_pets' => json_encode($this->otherPets),
            ]);

            // Zapisz lokalizację
            if ($this->latitude && $this->longitude) {
                $profile->update([
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'address' => $this->address,
                ]);
            }

            // Utwórz usługi
            $this->createSitterServices();

            // Zapisz dostępność
            $this->saveSitterAvailability();

            // Zapisz zdjęcia
            $this->saveSitterPhotos();

            // Zapisz dokumenty weryfikacyjne
            $this->saveSitterVerificationDocuments();

            // Usuń draft po pomyślnej rejestracji
            $this->deleteDraft();

            session()->flash('success', 'Gratulacje! Twoje konto Pet Sittera zostało pomyślnie utworzone!');

            $this->dispatch('registration-completed');
            $this->deactivateWizard();

            // Przekieruj do dashboardu pet sittera
            $this->redirectRoute('profile.dashboard');

        } catch (\Exception $e) {
            session()->flash('error', 'Wystąpił błąd podczas rejestracji. Spróbuj ponownie.');
            logger()->error('Pet Sitter Registration Error: '.$e->getMessage());
        }
    }

    /**
     * Tworzy domyślne usługi dla pet sittera.
     */
    private function createSitterServices(): void
    {
        $user = Auth::user();

        // Mapowanie kluczy usług na ich nazwy
        $serviceNames = [
            'dog_walking' => 'Spacery z psem',
            'pet_sitting' => 'Opieka w domu właściciela',
            'pet_boarding' => 'Opieka u opiekuna',
            'overnight_care' => 'Opieka nocna',
            'pet_transport' => 'Transport zwierząt',
            'vet_visits' => 'Wizyta u weterynarza',
            'grooming' => 'Pielęgnacja zwierząt',
            'feeding' => 'Karmienie zwierząt',
            // Backward compatibility - stare klucze
            'walking' => 'Spacery z psem',
            'home_care' => 'Opieka w domu właściciela',
            'overnight' => 'Opieka z nocowaniem',
        ];

        foreach ($this->servicePricing as $serviceType => $price) {
            // Nowa struktura: {serviceKey: price}
            // Usługa jest enabled jeśli ma cenę > 0
            if ($price > 0) {
                $user->services()->create([
                    'service_category_id' => $this->getServiceCategoryId($serviceType),
                    'title' => $serviceNames[$serviceType] ?? ucfirst(str_replace('_', ' ', $serviceType)),
                    'description' => "Profesjonalna usługa: {$serviceNames[$serviceType]}",
                    'price_per_hour' => $price,
                    'is_active' => false, // Sitter musi najpierw dokończyć konfigurację
                    'duration_minutes' => 60,
                ]);
            }
        }
    }

    /**
     * Pobiera ID kategorii usługi na podstawie klucza usługi z wizarda.
     *
     * Dynamicznie mapuje klucze usług wizarda na slug'i kategorii w bazie
     * i zwraca odpowiednie ID kategorii.
     *
     * @param  string  $serviceKey  Klucz usługi z wizarda (np. 'dog_walking')
     * @return int ID kategorii usługi lub ID pierwszej kategorii jako fallback
     */
    private function getServiceCategoryId(string $serviceKey): int
    {
        $mapping = $this->getServiceKeyToSlugMapping();
        $slug = $mapping[$serviceKey] ?? null;

        if ($slug) {
            $category = ServiceCategory::where('slug', $slug)->first();
            if ($category) {
                return $category->id;
            }
        }

        // Fallback - zwróć ID pierwszej aktywnej kategorii
        $firstCategory = ServiceCategory::active()->ordered()->first();

        return $firstCategory ? $firstCategory->id : 1;
    }

    /**
     * Zapisuje dostępność pet sittera.
     */
    private function saveSitterAvailability(): void
    {
        $user = Auth::user();

        // Zapisz dostępność tygodniową w tabeli user_profiles jako JSON
        $profile = $user->profile;

        if ($profile) {
            $profile->update([
                'weekly_availability' => json_encode($this->weeklyAvailability),
                'emergency_available' => $this->emergencyAvailable,
                'flexible_schedule' => $this->flexibleSchedule,
            ]);
        }
    }

    /**
     * Zapisuje zdjęcia pet sittera.
     */
    private function saveSitterPhotos(): void
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Zapisz zdjęcie profilowe
        if ($this->profilePhoto && $profile) {
            $avatarPath = $this->profilePhoto->store('avatars', 'public');
            $profile->update(['avatar' => $avatarPath]);
        }

        // Zapisz zdjęcia domu (przechowaj ścieżki jako JSON w profilu)
        if (! empty($this->homePhotos) && $profile) {
            $homePhotosPaths = [];
            foreach ($this->homePhotos as $photo) {
                $path = $photo->store('home-photos', 'public');
                $homePhotosPaths[] = $path;
            }

            $profile->update(['home_photos' => json_encode($homePhotosPaths)]);
        }
    }

    /**
     * Zapisuje dokumenty weryfikacyjne pet sittera.
     */
    private function saveSitterVerificationDocuments(): void
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (! $profile) {
            return;
        }

        $verificationData = [];

        // Zapisz dokument tożsamości
        if ($this->identityDocument) {
            $identityPath = $this->identityDocument->store('verification/identity', 'private');
            $verificationData['identity_document'] = $identityPath;
        }

        // Zapisz zaświadczenie o niekaralności
        if ($this->criminalRecord) {
            $criminalPath = $this->criminalRecord->store('verification/criminal-record', 'private');
            $verificationData['criminal_record'] = $criminalPath;
        }

        // Zapisz referencje
        if (! empty($this->references)) {
            $verificationData['references'] = $this->references;
        }

        // Zaktualizuj profil z danymi weryfikacyjnymi
        if (! empty($verificationData)) {
            $profile->update([
                'verification_documents' => json_encode($verificationData),
                'verification_status' => 'pending', // pending, verified, rejected
            ]);
        }
    }

    /**
     * Usuwa zdjęcie domu o podanym indeksie.
     *
     * @param  int  $index
     */
    /**
     * Usuwa zdjęcie profilowe.
     */
    public function removeProfilePhoto(): void
    {
        \Log::info('📸 removeProfilePhoto() called');

        // Usuń fizyczny plik jeśli istnieje
        if (is_array($this->profilePhoto) && isset($this->profilePhoto['path'])) {
            $path = $this->profilePhoto['path'];
            if (\Storage::disk('public')->exists($path)) {
                \Storage::disk('public')->delete($path);
                \Log::info('📸 Physical file deleted', ['path' => $path]);
            }
        }

        // Wyczyść property
        $this->profilePhoto = null;

        // Zapisz do draftu
        $this->saveDraft();

        \Log::info('📸 Profile photo removed and saved to draft');
    }

    public function removeHomePhoto(int $index): void
    {
        if (isset($this->homePhotos[$index])) {
            // Usuń fizyczny plik jeśli istnieje
            if (isset($this->homePhotos[$index]['path'])) {
                $path = $this->homePhotos[$index]['path'];
                if (\Storage::disk('public')->exists($path)) {
                    \Storage::disk('public')->delete($path);
                    \Log::info('📸 Home photo file deleted', ['path' => $path]);
                }
            }

            // Usuń z tablicy
            $photos = collect($this->homePhotos);
            $photos->forget($index);
            $this->homePhotos = $photos->values()->toArray();

            // Zapisz do draftu
            $this->saveDraft();
        }
    }

    /**
     * Odbiera i zapisuje zdjęcie profilowe permanentnie w jednej operacji.
     */
    public function uploadAndSaveProfilePhoto()
    {
        \Log::info('📸 uploadAndSaveProfilePhoto() called', [
            'hasProfilePhoto' => (bool) $this->profilePhoto,
            'profilePhotoType' => $this->profilePhoto ? (is_object($this->profilePhoto) ? get_class($this->profilePhoto) : gettype($this->profilePhoto)) : 'null',
        ]);

        if ($this->profilePhoto) {
            // Jeśli profilePhoto jest już array, to znaczy że zostało już zapisane
            if (is_array($this->profilePhoto)) {
                \Log::info('📸 Profile photo already saved, returning existing data');

                return $this->profilePhoto;
            }

            try {
                \Log::info('📸 Starting profile photo save process');

                $userId = Auth::id();

                // Wygeneruj unikalną nazwę pliku
                $originalName = $this->profilePhoto->getClientOriginalName();
                $extension = $this->profilePhoto->getClientOriginalExtension();
                $filename = time().'_'.uniqid().'.'.$extension;

                // Użyj PhotoStorageHelper do generowania ścieżki
                $storagePath = PhotoStorageHelper::generateProfilePhotoPath($userId, $filename);

                // Upewnij się że katalog istnieje
                PhotoStorageHelper::ensureDirectoryExists($userId, 'profile');

                // Store the file permanently w strukturze katalogów użytkownika
                $path = $this->profilePhoto->storeAs('', $storagePath, 'public');

                \Log::info('📸 File stored successfully', [
                    'path' => $path,
                    'user_id' => $userId,
                    'group_info' => PhotoStorageHelper::getUserGroupInfo($userId),
                ]);

                // Create a permanent URL object for the frontend
                $photoData = [
                    'url' => \Storage::disk('public')->url($path),
                    'name' => $originalName,
                    'size' => $this->profilePhoto->getSize(),
                    'path' => $path,
                    'user_id' => $userId,
                ];

                \Log::info('📸 Photo data prepared', $photoData);

                // Wyczyść stare zdjęcia profilowe (zachowaj tylko najnowsze)
                $deletedCount = PhotoStorageHelper::cleanupOldPhotos($userId, 'profile', 1);
                if ($deletedCount > 0) {
                    \Log::info('📸 Cleaned up old profile photos', ['deleted' => $deletedCount]);
                }

                // Replace the UploadedFile with permanent data
                $this->profilePhoto = $photoData;

                $this->saveDraft();

                $this->dispatch('photo-saved', ['type' => 'profile', 'data' => $photoData]);

                \Log::info('📸 Profile photo saved successfully');

                return $photoData;
            } catch (\Exception $e) {
                \Log::error('📸 Failed to save profile photo', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->addError('profilePhoto', 'Błąd podczas zapisywania zdjęcia. Spróbuj ponownie.');

                return null;
            }
        } else {
            \Log::warning('📸 uploadAndSaveProfilePhoto() called but profilePhoto is null');
        }

        return null;
    }

    /**
     * Zapisuje zdjęcie profilowe permanentnie (legacy metoda).
     */
    public function saveProfilePhoto()
    {
        return $this->uploadAndSaveProfilePhoto();
    }

    /**
     * Livewire lifecycle hook - automatycznie wywołane po upload profilePhoto
     */
    public function updatedProfilePhoto()
    {
        \Log::info('📸 updatedProfilePhoto() triggered', [
            'alreadyProcessed' => $this->profilePhotoProcessed,
            'isArray' => is_array($this->profilePhoto),
        ]);

        // Jeśli już przetworzone lub to jest array (już zapisane), pomiń
        if ($this->profilePhotoProcessed || is_array($this->profilePhoto)) {
            \Log::info('📸 Skipping duplicate processing');

            return;
        }

        if ($this->profilePhoto) {
            // Ustaw flagę przed przetwarzaniem
            $this->profilePhotoProcessed = true;

            // Natychmiast zapisz permanentnie po upload
            $result = $this->uploadAndSaveProfilePhoto();

            if ($result) {
                \Log::info('📸 Auto-save successful after upload');
                $this->dispatch('photo-uploaded', ['type' => 'profile', 'data' => $result]);
            }

            // Resetuj flagę po pomyślnym przetworzeniu
            $this->profilePhotoProcessed = false;
        }
    }

    /**
     * Livewire lifecycle hook - automatycznie wywołane po upload tempHomePhoto
     */
    public function updatedTempHomePhoto()
    {
        \Log::info('📸 updatedTempHomePhoto() triggered', [
            'hasTempHomePhoto' => (bool) $this->tempHomePhoto,
            'isArray' => is_array($this->tempHomePhoto),
        ]);

        // Jeśli to jest array (już zapisane), pomiń
        if (is_array($this->tempHomePhoto)) {
            \Log::info('📸 Skipping - tempHomePhoto is already processed array');

            return;
        }

        if ($this->tempHomePhoto) {
            // Natychmiast zapisz permanentnie po upload
            $result = $this->saveHomePhoto();

            if ($result) {
                \Log::info('📸 Auto-save successful after home photo upload');
                $this->dispatch('photo-uploaded', ['type' => 'home', 'data' => $result]);
            }
        }
    }

    /**
     * Lifecycle hook wywoływany po update identity document.
     *
     * @return void
     */
    public function updatedIdentityDocument()
    {
        \Log::info('📄 updatedIdentityDocument() triggered', [
            'hasIdentityDocument' => (bool) $this->identityDocument,
            'isArray' => is_array($this->identityDocument),
        ]);

        if (is_array($this->identityDocument)) {
            \Log::info('📄 Skipping - identityDocument is already processed array');

            return;
        }

        if ($this->identityDocument) {
            $result = $this->saveIdentityDocument();
            if ($result) {
                \Log::info('📄 Auto-save successful after identity document upload');
                $this->dispatch('document-uploaded', ['type' => 'identity', 'data' => $result]);
            }
        }
    }

    /**
     * Zapisuje dokument tożsamości permanentnie.
     */
    public function saveIdentityDocument(): ?array
    {
        if ($this->identityDocument) {
            try {
                $userId = Auth::id();

                // Wygeneruj unikalną nazwę pliku
                $originalName = $this->identityDocument->getClientOriginalName();
                $extension = $this->identityDocument->getClientOriginalExtension();
                $filename = time().'_'.uniqid().'.'.$extension;

                // Użyj PhotoStorageHelper do generowania ścieżki
                $storagePath = PhotoStorageHelper::generateUserPhotoPath($userId, 'verification/identity');
                $fullPath = $storagePath.'/'.$filename;

                // Upewnij się że katalog istnieje
                PhotoStorageHelper::ensureDirectoryExists($userId, 'verification/identity');

                // Zapisz plik
                $path = $this->identityDocument->storeAs($storagePath, $filename, 'public');

                \Log::info('📄 Identity document saved successfully', [
                    'path' => $path,
                    'filename' => $filename,
                    'userId' => $userId,
                ]);

                // Cleanup starych plików - zostaw tylko najnowszy
                PhotoStorageHelper::cleanupOldPhotos($userId, 'verification/identity', 1);

                // Zwróć dane dla JS
                $result = [
                    'name' => $originalName,
                    'path' => $path,
                    'url' => \Storage::disk('public')->url($path),
                    'size' => $this->identityDocument->getSize(),
                ];

                // Zastąp Livewire temporary file object array'em
                $this->identityDocument = $result;

                // Automatycznie zapisz draft
                $this->saveDraft();

                return $result;
            } catch (\Exception $e) {
                \Log::error('📄 Identity document save error', [
                    'error' => $e->getMessage(),
                    'userId' => Auth::id(),
                ]);
                $this->addError('identityDocument', 'Błąd podczas zapisywania dokumentu.');

                return null;
            }
        }

        return null;
    }

    /**
     * Zapisuje zdjęcie domu permanentnie.
     */
    public function saveHomePhoto()
    {
        if ($this->tempHomePhoto) {
            try {
                $userId = Auth::id();

                // Wygeneruj unikalną nazwę pliku
                $originalName = $this->tempHomePhoto->getClientOriginalName();
                $extension = $this->tempHomePhoto->getClientOriginalExtension();
                $filename = time().'_'.uniqid().'.'.$extension;

                // Użyj PhotoStorageHelper do generowania ścieżki
                $storagePath = PhotoStorageHelper::generateHomePhotoPath($userId, $filename);

                // Upewnij się że katalog istnieje
                PhotoStorageHelper::ensureDirectoryExists($userId, 'home');

                // Store the file permanently w strukturze katalogów użytkownika
                $path = $this->tempHomePhoto->storeAs('', $storagePath, 'public');

                \Log::info('📸 Home photo stored successfully', [
                    'path' => $path,
                    'user_id' => $userId,
                    'group_info' => PhotoStorageHelper::getUserGroupInfo($userId),
                ]);

                // Create a permanent URL object for the frontend
                $photoData = [
                    'url' => \Storage::disk('public')->url($path),
                    'name' => $originalName,
                    'size' => $this->tempHomePhoto->getSize(),
                    'path' => $path,
                    'user_id' => $userId,
                ];

                // Wyczyść stare zdjęcia domu (zachowaj 5 najnowszych)
                $deletedCount = PhotoStorageHelper::cleanupOldPhotos($userId, 'home', 5);
                if ($deletedCount > 0) {
                    \Log::info('📸 Cleaned up old home photos', ['deleted' => $deletedCount]);
                }

                // Add to home photos array
                $this->homePhotos[] = $photoData;

                $this->saveDraft();

                $this->dispatch('photo-saved', ['type' => 'home', 'data' => $photoData]);

                // Wyczyść tymczasową zmienną
                $this->tempHomePhoto = null;

                return $photoData;
            } catch (\Exception $e) {
                \Log::error('Failed to save home photo', ['error' => $e->getMessage()]);
                $this->addError('homePhotos', 'Błąd podczas zapisywania zdjęcia. Spróbuj ponownie.');

                return null;
            }
        }

        return null;
    }

    /**
     * Dodaje nową referencję do listy.
     */
    public function addReference(): void
    {
        $this->references[] = [
            'name' => '',
            'phone' => '',
            'relation' => '',
        ];
    }

    /**
     * Usuwa referencję o podanym indeksie.
     */
    public function removeReference(int $index): void
    {
        if (isset($this->references[$index])) {
            $references = collect($this->references);
            $references->forget($index);
            $this->references = $references->values()->toArray();
        }
    }

    /**
     * Usuwa dokument tożsamości.
     */
    public function removeIdentityDocument(): void
    {
        \Log::info('📄 removeIdentityDocument() called');

        // Usuń fizyczny plik jeśli istnieje
        if (is_array($this->identityDocument) && isset($this->identityDocument['path'])) {
            $path = $this->identityDocument['path'];
            if (\Storage::disk('public')->exists($path)) {
                \Storage::disk('public')->delete($path);
                \Log::info('📄 Physical file deleted', ['path' => $path]);
            }
        }

        $this->identityDocument = null;
        $this->saveDraft();
        \Log::info('📄 Identity document removed and saved to draft');
    }

    /**
     * Usuwa zaświadczenie o niekaralności.
     */
    public function removeCriminalRecord(): void
    {
        $this->criminalRecord = null;
    }

    /**
     * Ładuje istniejący draft użytkownika.
     */
    public function loadDraft(): void
    {
        // Ładuj draft tylko raz - przy pierwszym załadowaniu komponentu
        if ($this->draftLoaded || ! Auth::check()) {
            return;
        }

        $this->currentDraft = WizardDraft::where('user_id', Auth::id())
            ->where('wizard_type', 'pet_sitter')
            ->first();

        if ($this->currentDraft && $this->currentDraft->isRecent()) {
            // Załaduj dane z draft'u
            $this->hydrateFromDraft($this->currentDraft->wizard_data);
            $this->currentStep = $this->currentDraft->current_step;

            // Automatycznie aktywuj wizard gdy jest zapisany draft
            $this->isActive = true;

            // Przelicz estymację jeśli mamy kompletne dane lokalizacji
            if ($this->latitude != 0 && $this->longitude != 0 && $this->serviceRadius > 0) {
                $this->calculatePotentialClients();
                Log::info('Estymacja przeliczona po załadowaniu draftu', [
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'radius' => $this->serviceRadius,
                    'estimated_clients' => $this->estimatedClients,
                ]);
            }

            // Aktualizuj czas dostępu
            $this->currentDraft->touch();

            session()->flash('info', 'Kontynuujesz swoją rejestrację od kroku '.$this->currentStep);
        }

        // Oznacz że draft został załadowany
        $this->draftLoaded = true;
    }

    /**
     * Zapisuje aktualny postęp jako draft.
     */
    public function saveDraft(): void
    {
        if (! Auth::check()) {
            return;
        }

        try {
            $wizardData = $this->dehydrateForDraft();

            WizardDraft::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'wizard_type' => 'pet_sitter',
                ],
                [
                    'current_step' => $this->currentStep,
                    'wizard_data' => $wizardData,
                    'last_accessed_at' => now(),
                ]
            );

            $this->dispatch('draft-saved');
        } catch (\Exception $e) {
            \Log::error('Failed to save wizard draft', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Usuwa draft użytkownika.
     */
    public function deleteDraft(): void
    {
        if ($this->currentDraft) {
            $this->currentDraft->delete();
            $this->currentDraft = null;
        }
    }

    /**
     * Tworzy tablicę danych do zapisania w draft'cie.
     */
    private function dehydrateForDraft(): array
    {
        return [
            'motivation' => $this->motivation,
            'petExperience' => $this->petExperience,
            'experienceDescription' => $this->experienceDescription,
            'yearsOfExperience' => $this->yearsOfExperience,
            'animalTypes' => $this->animalTypes,
            'animalSizes' => $this->animalSizes,
            'serviceTypes' => $this->serviceTypes,
            'specialServices' => $this->specialServices,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'serviceRadius' => $this->serviceRadius,
            'weeklyAvailability' => $this->weeklyAvailability,
            'emergencyAvailable' => $this->emergencyAvailable,
            'flexibleSchedule' => $this->flexibleSchedule,
            'homeType' => $this->homeType,
            'hasGarden' => $this->hasGarden,
            'isSmoking' => $this->isSmoking,
            'hasOtherPets' => $this->hasOtherPets,
            'otherPets' => $this->otherPets,
            'references' => $this->references,
            'servicePricing' => $this->servicePricing,
            'pricingStrategy' => $this->pricingStrategy,
            'agreedToTerms' => $this->agreedToTerms,
            'marketingConsent' => $this->marketingConsent,
            // Krok 8: Zdjęcia
            'profilePhoto' => $this->profilePhoto,
            'homePhotos' => $this->homePhotos,
            // Krok 9: Weryfikacja
            'identityDocument' => $this->identityDocument,
            'hasCriminalRecordDeclaration' => $this->hasCriminalRecordDeclaration,
        ];
    }

    /**
     * Przywraca dane z draft'u do komponentu.
     */
    private function hydrateFromDraft(array $draftData): void
    {
        $this->motivation = $draftData['motivation'] ?? '';
        $this->petExperience = $draftData['petExperience'] ?? [];
        $this->experienceDescription = $draftData['experienceDescription'] ?? '';
        $this->yearsOfExperience = $draftData['yearsOfExperience'] ?? 0;
        $this->animalTypes = $draftData['animalTypes'] ?? [];
        $this->animalSizes = $draftData['animalSizes'] ?? [];
        $this->serviceTypes = $draftData['serviceTypes'] ?? [];
        $this->specialServices = $draftData['specialServices'] ?? [];
        $this->address = $draftData['address'] ?? '';
        $this->latitude = $draftData['latitude'] ?? 0;
        $this->longitude = $draftData['longitude'] ?? 0;
        $this->serviceRadius = $draftData['serviceRadius'] ?? 10;
        $this->weeklyAvailability = $draftData['weeklyAvailability'] ?? $this->weeklyAvailability;
        $this->emergencyAvailable = $draftData['emergencyAvailable'] ?? false;
        $this->flexibleSchedule = $draftData['flexibleSchedule'] ?? true;
        $this->homeType = $draftData['homeType'] ?? '';
        $this->hasGarden = $draftData['hasGarden'] ?? false;
        $this->isSmoking = $draftData['isSmoking'] ?? false;
        $this->hasOtherPets = $draftData['hasOtherPets'] ?? false;
        $this->otherPets = $draftData['otherPets'] ?? [];
        $this->references = $draftData['references'] ?? [];
        $this->servicePricing = $draftData['servicePricing'] ?? $this->servicePricing;
        $this->pricingStrategy = $draftData['pricingStrategy'] ?? 'competitive';
        $this->agreedToTerms = $draftData['agreedToTerms'] ?? false;
        $this->marketingConsent = $draftData['marketingConsent'] ?? false;
        // Krok 8: Zdjęcia
        $this->profilePhoto = $draftData['profilePhoto'] ?? null;
        $this->homePhotos = $draftData['homePhotos'] ?? [];
        // Krok 9: Weryfikacja
        $this->identityDocument = $draftData['identityDocument'] ?? null;
        $this->hasCriminalRecordDeclaration = $draftData['hasCriminalRecordDeclaration'] ?? false;
    }

    /**
     * Ukrywa feedback sukcesu.
     */
    public function hideSuccessFeedback(): void
    {
        $this->showSuccessFeedback = false;
        $this->lastValidationMessage = '';
    }

    /**
     * Obsługuje kliknięcie w opcję - dodaje animację selection.
     */
    public function selectOption(string $field, string $value): void
    {
        // Animacja kliknięcia
        $this->dispatch('option-selected', [
            'field' => $field,
            'value' => $value,
            'animated' => true,
        ]);

        // Aktualizuj wartość po krótkiej animacji
        $this->dispatch('option-animation-complete', [
            'field' => $field,
            'value' => $value,
        ]);
    }

    /**
     * Obsługuje hover nad opcjami dla preview efektów.
     */
    public function previewOption(string $field, string $value): void
    {
        $this->dispatch('option-preview', [
            'field' => $field,
            'value' => $value,
            'preview' => true,
        ]);
    }

    /**
     * Animacja podczas wprowadzania tekstu + debounced auto-save.
     */
    public function updatedMotivation(): void
    {
        $this->dispatch('field-updated', [
            'field' => 'motivation',
            'value' => $this->motivation,
            'length' => strlen($this->motivation),
            'isValid' => strlen($this->motivation) >= 50,
        ]);

        // Debounced auto-save po 1.5 sekundy bez zmian
        $this->debouncedAutoSave();
    }

    /**
     * Animacja podczas wprowadzania opisu doświadczenia + debounced auto-save.
     */
    public function updatedExperienceDescription(): void
    {
        $this->dispatch('field-updated', [
            'field' => 'experienceDescription',
            'value' => $this->experienceDescription,
            'length' => strlen($this->experienceDescription),
            'isValid' => strlen($this->experienceDescription) >= 100,
        ]);

        // Debounced auto-save po 1.5 sekundy bez zmian
        $this->debouncedAutoSave();
    }

    /**
     * Obsługa debounced auto-save dla wszystkich pól.
     *
     * Zapisuje draft automatycznie po określonym czasie bezczynności,
     * co zapobiega utracie danych podczas wypełniania formularza.
     */
    public function debouncedAutoSave(): void
    {
        // Wyślij event do JavaScript do obsługi debounce
        $this->dispatch('trigger-auto-save', [
            'delay' => 1500, // 1.5 sekundy
            'showIndicator' => true,
        ]);
    }

    /**
     * Metoda wywoływana przez JavaScript po debounce timeout.
     * Zapisuje aktualny stan formularza jako draft.
     */
    public function performAutoSave(): void
    {
        if (! Auth::check()) {
            return;
        }

        try {
            $this->saveDraft();

            // Wyślij event o pomyślnym auto-save
            $this->dispatch('auto-save-success', [
                'timestamp' => now()->format('H:i:s'),
                'step' => $this->currentStep,
            ]);

        } catch (\Exception $e) {
            \Log::error('Auto-save failed', ['error' => $e->getMessage()]);

            // Wyślij event o błędzie auto-save
            $this->dispatch('auto-save-error', [
                'message' => 'Nie udało się automatycznie zapisać postępu',
            ]);
        }
    }

    /**
     * Triggeruje animację pulsowania dla ważnych elementów.
     */
    public function highlightElement(string $elementId): void
    {
        $this->dispatch('highlight-element', [
            'elementId' => $elementId,
            'duration' => 2000,
        ]);
    }

    /**
     * Pokazuje tooltip z dodatkowym kontekstem.
     */
    public function showTooltip(string $content, string $position = 'top'): void
    {
        $this->dispatch('show-tooltip', [
            'content' => $content,
            'position' => $position,
            'animated' => true,
        ]);
    }

    /**
     * Aktualizuje adres i współrzędne z komponentu autocomplete.
     *
     * @param  string  $address  Nowy adres
     * @param  float  $latitude  Szerokość geograficzna
     * @param  float  $longitude  Długość geograficzna
     */
    public function updateAddressWithCoordinates(string $address, float $latitude, float $longitude): void
    {
        $this->address = $address;
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        // Zapisz do draft
        $this->saveDraft();

        // Wyślij event do mapy jeśli istnieje
        $this->dispatch('location-updated', [
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        Log::info('Address updated with coordinates', [
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'step' => $this->currentStep,
        ]);
    }

    /**
     * Aktualizuje pełne strukturalne dane adresowe z reverse geocoding.
     *
     * Metoda wywoływana z JavaScript po reverse geocoding aby zapisać
     * wszystkie pola adresowe w formacie wymaganym przez GUS i wyświetlanie.
     *
     * @param  array  $addressData  Pełne dane adresowe z Nominatim API
     */
    public function updateAddressStructured(array $addressData): void
    {
        // Aktualizuj główny adres (sformatowany string)
        $this->address = $addressData['formatted_address'] ?? '';

        // Aktualizuj strukturalne pola adresowe
        $this->road = $addressData['road'] ?? '';
        $this->house_number = $addressData['house_number'] ?? '';
        $this->postcode = $addressData['postcode'] ?? '';
        $this->city = $addressData['city'] ?? '';
        $this->town = $addressData['town'] ?? '';
        $this->village = $addressData['village'] ?? '';
        $this->municipality = $addressData['municipality'] ?? '';
        $this->county = $addressData['county'] ?? '';
        $this->state = $addressData['state'] ?? '';
        $this->gus_city_name = $addressData['gus_city_name'] ?? '';
        $this->district = $addressData['district'] ?? '';

        // Zapisz do draft
        $this->saveDraft();

        Log::info('Strukturalne dane adresowe zaktualizowane', [
            'formatted_address' => $this->address,
            'road' => $this->road,
            'house_number' => $this->house_number,
            'postcode' => $this->postcode,
            'gus_city_name' => $this->gus_city_name,
            'municipality' => $this->municipality,
            'county' => $this->county,
            'step' => $this->currentStep,
        ]);

        // Wyślij event do frontendu z potwierdzeniem
        $this->dispatch('address-structured-updated', [
            'success' => true,
            'city' => $this->gus_city_name ?: $this->city ?: $this->town,
        ]);
    }

    /**
     * Oblicza potencjalną liczbę klientów na podstawie danych GUS.
     *
     * Wykorzystuje dane demograficzne z API GUS oraz współczynniki:
     * - 37% Polaków ma zwierzęta (dane GUS 2023)
     * - 25% właścicieli zwierząt szuka profesjonalnej opieki
     * - Promień obsługi wpływa na dostępność
     */
    public function calculatePotentialClients(): void
    {
        // Jeśli nie mamy współrzędnych, nie obliczamy
        if ($this->latitude == 0 || $this->longitude == 0) {
            $this->estimatedClients = 0;

            return;
        }

        try {
            // Użyj serwisu GUS do obliczenia potencjalnych klientów
            $gusService = app(GUSApiService::class);

            $this->estimatedClients = $gusService->estimatePotentialClients(
                $this->latitude,
                $this->longitude,
                $this->serviceRadius
            );

            Log::info('Obliczono potencjalnych klientów', [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius' => $this->serviceRadius,
                'estimated_clients' => $this->estimatedClients,
            ]);

            // Wyślij event do frontendu z aktualizacją (dla panelu AI i innych komponentów)
            // Używamy $this->js() aby wysłać event na poziomie window (browser event)
            $this->js('window.dispatchEvent(new CustomEvent("estimation-refreshed", { detail: { count: '.$this->estimatedClients.' } }))');
        } catch (\Exception $e) {
            Log::error('Błąd podczas obliczania potencjalnych klientów', [
                'error' => $e->getMessage(),
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]);

            // Wartość domyślna w przypadku błędu
            $this->estimatedClients = 0;
        }
    }

    /**
     * Odświeża estymację potencjalnych klientów.
     *
     * Wywołuje ponowne obliczenie estymacji na podstawie aktualnych danych
     * (lokalizacja + promień obsługi). Używane przez przycisk "odśwież" w UI.
     */
    public function refreshEstimation(): void
    {
        $this->calculatePotentialClients();

        Log::info('Estymacja klientów odświeżona ręcznie', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->serviceRadius,
            'estimated_clients' => $this->estimatedClients,
        ]);

        // Wyślij event do przeglądarki z potwierdzeniem
        // Używamy $this->js() aby wysłać event na poziomie window (browser event)
        $this->js('window.dispatchEvent(new CustomEvent("estimation-refreshed", { detail: { count: '.$this->estimatedClients.' } }))');
    }

    /**
     * Aktualizuje tylko współrzędne (np. po kliknięciu na mapę).
     *
     * @param  float  $latitude  Szerokość geograficzna
     * @param  float  $longitude  Długość geograficzna
     */
    public function updateCoordinates(float $latitude, float $longitude): void
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        // Zapisz do draft
        $this->saveDraft();

        Log::info('Coordinates updated', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'step' => $this->currentStep,
        ]);
    }

    /**
     * Waliduje współrzędne geograficzne.
     *
     * @param  float  $latitude  Szerokość geograficzna
     * @param  float  $longitude  Długość geograficzna
     * @return bool Czy współrzędne są prawidłowe
     */
    private function validateCoordinates(float $latitude, float $longitude): bool
    {
        return $latitude >= -90 && $latitude <= 90 &&
               $longitude >= -180 && $longitude <= 180;
    }

    /**
     * Pobiera analizę rynku cen na podstawie lokalizacji użytkownika.
     *
     * Wykorzystuje PricingAnalysisService do analizy cen w okolicy.
     * Zwraca statystyki cenowe (min, max, avg) dla każdego typu usługi.
     *
     * @return array Analiza cenowa lub pusta tablica w przypadku błędu
     */
    public function getPricingAnalysis(): array
    {
        try {
            $pricingService = app(\App\Services\PricingAnalysisService::class);

            // Pobierz lokalizację z bieżących danych wizarda lub profilu użytkownika
            $latitude = $this->latitude;
            $longitude = $this->longitude;

            // Jeśli nie ma lokalizacji w wizardzie, spróbuj pobrać z profilu
            if (! $latitude || ! $longitude) {
                $userProfile = Auth::user()?->profile;
                $latitude = $userProfile?->latitude;
                $longitude = $userProfile?->longitude;
            }

            Log::info('📊 Pobieranie analizy cen', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'has_location' => ($latitude && $longitude),
            ]);

            // Pobierz pełne podsumowanie rynku
            $marketSummary = $pricingService->getMarketSummary($latitude, $longitude);

            return [
                'success' => true,
                'data' => $marketSummary['analysis'],
                'metadata' => [
                    'total_samples' => $marketSummary['total_samples'],
                    'reliable_services' => $marketSummary['reliable_services'],
                    'data_quality' => $marketSummary['data_quality'],
                    'has_location' => $marketSummary['has_location'],
                    'location' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ],
                ],
            ];

        } catch (\Exception $e) {
            Log::error('📊 Błąd pobierania analizy cen: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Nie udało się pobrać analizy cen',
                'data' => [],
            ];
        }
    }

    /**
     * Pobiera aktywne kategorie usług z bazy danych.
     *
     * Computed property dla Livewire - cache'owane automatycznie.
     *
     * @return \Illuminate\Database\Eloquent\Collection<ServiceCategory>
     */
    public function getServiceCategoriesProperty()
    {
        return ServiceCategory::active()->ordered()->get();
    }

    /**
     * Pobiera aktywne typy zwierząt z bazy danych.
     *
     * Computed property dla Livewire - cache'owane automatycznie.
     *
     * @return \Illuminate\Database\Eloquent\Collection<PetType>
     */
    public function getPetTypesProperty()
    {
        return PetType::active()->ordered()->get();
    }

    /**
     * Mapowanie kluczy usług wizarda na slug'i kategorii w bazie.
     *
     * @return array<string, string>
     */
    private function getServiceKeyToSlugMapping(): array
    {
        return [
            'dog_walking' => 'spacery',
            'pet_sitting' => 'opieka-w-domu',
            'pet_boarding' => 'opieka-u-opiekuna',
            'overnight_care' => 'opieka-nocna',
            'pet_transport' => 'transport-weterynaryjny',
            'vet_visits' => 'wizyta-kontrolna',
            'grooming' => 'pielegnacja',
            'feeding' => 'karmienie',
        ];
    }

    /**
     * Odwrotne mapowanie - ze slug'ów kategorii na klucze wizarda.
     *
     * @return array<string, string>
     */
    private function getSlugToServiceKeyMapping(): array
    {
        return array_flip($this->getServiceKeyToSlugMapping());
    }

    /**
     * Pobiera usługi sformatowane dla widoku step-4.
     *
     * Konwertuje kategorie z bazy danych na format używany w wizardzie.
     *
     * @return array<string, array{icon: string, title: string, desc: string, slug: string}>
     */
    public function getFormattedServicesProperty(): array
    {
        $categories = $this->serviceCategories;
        $slugToKeyMapping = $this->getSlugToServiceKeyMapping();
        $formatted = [];

        foreach ($categories as $category) {
            $serviceKey = $slugToKeyMapping[$category->slug] ?? null;

            if ($serviceKey) {
                $formatted[$serviceKey] = [
                    'icon' => $category->icon ?: '📋',
                    'title' => $category->name,
                    'desc' => $category->description ?: '',
                    'slug' => $category->slug,
                ];
            }
        }

        return $formatted;
    }

    /**
     * Mapuje emoji zwierząt według slug'a z bazy.
     *
     * @return array<string, string>
     */
    private function getPetTypeEmojiMapping(): array
    {
        return [
            'dog' => '🐕',
            'cat' => '🐱',
            'bird' => '🐦',
            'rabbit' => '🐰',
            'other' => '🐾',
        ];
    }

    /**
     * Pobiera typy zwierząt sformatowane dla widoku step-7.
     *
     * Konwertuje typy zwierząt z bazy danych na format używany w wizardzie.
     *
     * @return array<string, array{icon: string, title: string}>
     */
    public function getFormattedPetTypesProperty(): array
    {
        $petTypes = $this->petTypes;
        $emojiMapping = $this->getPetTypeEmojiMapping();
        $formatted = [];

        foreach ($petTypes as $petType) {
            $formatted[$petType->slug] = [
                'icon' => $emojiMapping[$petType->slug] ?? $petType->icon ?? '🐾',
                'title' => $petType->name,
            ];
        }

        return $formatted;
    }

    /**
     * Pobiera typy zwierząt sformatowane dla widoku step-3 (liczba mnoga).
     *
     * Step-3 używa kluczy w liczbie mnogiej ('dogs', 'cats'), więc mapujemy je odpowiednio.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public function getFormattedAnimalTypesProperty(): array
    {
        $petTypes = $this->petTypes;
        $emojiMapping = $this->getPetTypeEmojiMapping();
        $formatted = [];

        // Mapowanie singular -> plural
        $pluralMapping = [
            'dog' => 'dogs',
            'cat' => 'cats',
            'bird' => 'birds',
            'rabbit' => 'rabbits',
            'other' => 'other',
        ];

        // Mapowanie plural -> liczba mnoga polska
        $polishPlural = [
            'dogs' => 'Psy',
            'cats' => 'Koty',
            'birds' => 'Ptaki',
            'rabbits' => 'Króliki',
            'other' => 'Inne',
        ];

        foreach ($petTypes as $petType) {
            $pluralKey = $pluralMapping[$petType->slug] ?? $petType->slug;
            $polishName = $polishPlural[$pluralKey] ?? $petType->name;

            $formatted[$pluralKey] = [
                $polishName,
                $emojiMapping[$petType->slug] ?? $petType->icon ?? '🐾',
            ];
        }

        return $formatted;
    }

    /**
     * Renderuje komponent.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.pet-sitter-wizard', [
            'progressPercentage' => $this->getProgressPercentage(),
            'aiSuggestions' => $this->showAIPanel ? $this->getAISuggestions() : [],
        ]);
    }
}
