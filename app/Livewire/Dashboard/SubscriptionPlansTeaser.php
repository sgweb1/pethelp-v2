<?php

namespace App\Livewire\Dashboard;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Livewire\Component;

/**
 * Komponent teasera planów subskrypcji w panelu użytkownika.
 *
 * Wyświetla dostępne plany subskrypcji z możliwością upgrade'u dla zalogowanych
 * użytkowników. Pokazuje aktualny plan użytkownika oraz rekomendacje.
 *
 * @package App\Livewire\Dashboard
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class SubscriptionPlansTeaser extends Component
{
    /**
     * Aktualny użytkownik.
     *
     * @var User
     */
    public User $user;

    /**
     * Czy pokazywać wszystkie plany czy tylko upgrade.
     *
     * @var bool
     */
    public bool $showAll = false;

    /**
     * Czy użytkownik może zostać opiekunem (sitter).
     *
     * @var bool
     */
    public bool $canBecomeSitter = false;

    /**
     * Inicjalizuje komponent z danymi użytkownika.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->user = auth()->user();
        $this->canBecomeSitter = $this->user->profile?->role === 'owner' || $this->user->profile?->role === 'both';
    }

    /**
     * Przełącza widok między pokazywaniem wszystkich planów a tylko upgrade.
     *
     * @return void
     */
    public function toggleShowAll(): void
    {
        $this->showAll = !$this->showAll;
    }

    /**
     * Przekierowuje do strony wyboru planu subskrypcji.
     *
     * @param string $planSlug Slug wybranego planu
     * @return void
     */
    public function selectPlan(string $planSlug): void
    {
        // Zapisz wybrany plan w sesji dla procesu checkout
        session(['selected_plan' => $planSlug]);

        // Przekieruj do strony płatności
        $this->redirect(route('subscription.checkout', ['plan' => $planSlug]));
    }

    /**
     * Pobiera aktualny plan użytkownika.
     *
     * @return SubscriptionPlan|null
     */
    public function getCurrentPlan(): ?SubscriptionPlan
    {
        return $this->user->currentSubscriptionPlan();
    }

    /**
     * Pobiera dostępne plany subskrypcji.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailablePlans()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();

        if (!$this->showAll) {
            $currentPlan = $this->getCurrentPlan();

            if ($currentPlan) {
                // Pokaż tylko plany wyższe niż aktualny
                $plans = $plans->where('price', '>', $currentPlan->price);
            }
        }

        return $plans;
    }

    /**
     * Sprawdza czy użytkownik ma aktualnie dany plan.
     *
     * @param string $planSlug Slug planu do sprawdzenia
     * @return bool
     */
    public function hasCurrentPlan(string $planSlug): bool
    {
        $currentPlan = $this->getCurrentPlan();
        return $currentPlan && $currentPlan->slug === $planSlug;
    }

    /**
     * Pobiera rekomendowany plan dla użytkownika.
     *
     * @return SubscriptionPlan|null
     */
    public function getRecommendedPlan(): ?SubscriptionPlan
    {
        $currentPlan = $this->getCurrentPlan();

        // Jeśli użytkownik nie ma planu, rekomenduj Pro
        if (!$currentPlan) {
            return SubscriptionPlan::where('slug', 'pro')->first();
        }

        // Jeśli ma Basic, rekomenduj Pro
        if ($currentPlan->slug === 'basic') {
            return SubscriptionPlan::where('slug', 'pro')->first();
        }

        // Jeśli ma Pro, rekomenduj Premium
        if ($currentPlan->slug === 'pro') {
            return SubscriptionPlan::where('slug', 'premium')->first();
        }

        // Jeśli ma Premium, nie rekomenduj niczego
        return null;
    }

    /**
     * Renderuje widok komponentu.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.dashboard.subscription-plans-teaser', [
            'availablePlans' => $this->getAvailablePlans(),
            'currentPlan' => $this->getCurrentPlan(),
            'recommendedPlan' => $this->getRecommendedPlan(),
        ]);
    }
}
