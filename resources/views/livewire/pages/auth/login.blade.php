<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <x-ui.input
            wire:model="form.email"
            id="email"
            type="email"
            label="Adres email"
            icon="email"
            required
            autofocus
            autocomplete="username"
            error="{{ $errors->first('form.email') }}"
            hint="Wpisz swój adres email"
        />

        <!-- Password -->
        <x-ui.input
            wire:model="form.password"
            id="password"
            type="password"
            label="Hasło"
            required
            autocomplete="current-password"
            error="{{ $errors->first('form.password') }}"
        />

        <!-- Remember Me -->
        <div class="flex items-center">
            <input
                wire:model="form.remember"
                id="remember"
                type="checkbox"
                class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 focus:ring-2"
                name="remember"
            >
            <label for="remember" class="ml-3 text-sm text-gray-700">
                Zapamiętaj mnie
            </label>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    wire:navigate
                    class="text-sm text-primary-600 hover:text-primary-700 underline-offset-4 hover:underline transition-colors duration-200"
                >
                    Zapomniałeś hasła?
                </a>
            @endif

            <x-ui.button variant="primary" size="lg" type="submit" class="w-full sm:w-auto" wire:loading.attr="disabled">
                <svg wire:loading.remove class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <svg wire:loading.delay class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove>Zaloguj się</span>
                <span wire:loading.delay>Logowanie...</span>
            </x-ui.button>
        </div>
    </form>

    {{-- Dynamiczne przyciski szybkiego logowania --}}
    <livewire:quick-login-buttons />
</div>
