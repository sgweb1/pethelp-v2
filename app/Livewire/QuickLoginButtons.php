<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

/**
 * Komponent wyświetlający przyciski szybkiego logowania.
 *
 * Dynamicznie pobiera aktualnych użytkowników z bazy danych
 * i wyświetla ich jako przyciski do szybkiego logowania.
 * Dostępny tylko w środowisku lokalnym.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class QuickLoginButtons extends Component
{
    /**
     * Lista aktualnych użytkowników do wyświetlenia.
     */
    public array $users = [];

    /**
     * Czy pokazać rozszerzoną listę użytkowników.
     */
    public bool $showExpanded = false;

    /**
     * Inicjalizacja komponentu - pobiera użytkowników z bazy.
     */
    public function mount(): void
    {
        // Sprawdź czy jesteśmy w środowisku lokalnym
        if (config('app.env') !== 'local') {
            return;
        }

        $this->loadUsers();
    }

    /**
     * Przełącza widok rozszerzony/zwykły.
     */
    public function toggleExpanded(): void
    {
        $this->showExpanded = ! $this->showExpanded;
        $this->loadUsers();
    }

    /**
     * Pobiera użytkowników z bazy danych.
     */
    private function loadUsers(): void
    {
        $this->users = [
            'owners' => $this->getOwners(),
            'sitters' => $this->getSitters(),
            'regular' => $this->getRegularUsers(),
        ];
    }

    /**
     * Pobiera właścicieli zwierząt.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getOwners()
    {
        return User::whereHas('pets')
            ->with(['pets:id,owner_id,name'])
            ->limit($this->showExpanded ? 5 : 1)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => 'owner',
                    'description' => 'Właściciel '.$user->pets->count().' zwierząt',
                    'pets_names' => $user->pets->pluck('name')->implode(', '),
                ];
            });
    }

    /**
     * Pobiera opiekunów zwierząt.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getSitters()
    {
        return User::whereHas('services')
            ->with(['services:id,sitter_id,title'])
            ->limit($this->showExpanded ? 5 : 1)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => 'sitter',
                    'description' => 'Opiekun z '.$user->services->count().' usługami',
                    'services' => $user->services->pluck('title')->unique()->implode(', '),
                ];
            });
    }

    /**
     * Pobiera zwykłych użytkowników.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getRegularUsers()
    {
        return User::whereDoesntHave('pets')
            ->whereDoesntHave('services')
            ->limit($this->showExpanded ? 3 : 1)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => 'regular',
                    'description' => 'Nowy użytkownik',
                ];
            });
    }

    /**
     * Renderuje komponent.
     */
    public function render()
    {
        // Nie pokazuj w środowisku produkcyjnym
        if (config('app.env') !== 'local') {
            return view('livewire.quick-login-buttons', [
                'localEnvironment' => false,
            ]);
        }

        return view('livewire.quick-login-buttons', [
            'localEnvironment' => true,
            'users' => $this->users,
            'showExpanded' => $this->showExpanded,
        ]);
    }
}
