<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register" class="space-y-6">
        <!-- Name -->
        <x-ui.input
            wire:model="name"
            id="name"
            type="text"
            label="Imię i nazwisko"
            icon="user"
            required
            autofocus
            autocomplete="name"
            error="{{ $errors->first('name') }}"
            hint="Wpisz swoje imię i nazwisko"
        />

        <!-- Email Address -->
        <x-ui.input
            wire:model="email"
            id="email"
            type="email"
            label="Adres email"
            icon="email"
            required
            autocomplete="username"
            error="{{ $errors->first('email') }}"
            hint="Będzie to Twój login do systemu"
        />

        <!-- Password -->
        <x-ui.input
            wire:model="password"
            id="password"
            type="password"
            label="Hasło"
            required
            autocomplete="new-password"
            error="{{ $errors->first('password') }}"
            hint="Minimum 8 znaków, zawierające litery i cyfry"
        />

        <!-- Confirm Password -->
        <x-ui.input
            wire:model="password_confirmation"
            id="password_confirmation"
            type="password"
            label="Potwierdź hasło"
            required
            autocomplete="new-password"
            error="{{ $errors->first('password_confirmation') }}"
            hint="Wpisz hasło ponownie"
        />

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4">
            <a
                href="{{ route('login') }}"
                wire:navigate
                class="text-sm text-primary-600 hover:text-primary-700 underline-offset-4 hover:underline transition-colors duration-200"
            >
                Masz już konto? Zaloguj się
            </a>

            <x-ui.button variant="primary" size="lg" type="submit" class="w-full sm:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Utwórz konto
            </x-ui.button>
        </div>
    </form>
</div>
