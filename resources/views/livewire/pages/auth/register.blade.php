<?php

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $withWizard = false;
    public string $turnstileToken = '';

    /**
     * Obsługuje rejestrację użytkownika.
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

        // Rozdziel imię i nazwisko z pola name
        $nameParts = explode(' ', trim($this->name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Utwórz podstawowy profil
        // Zawsze zaczynamy jako 'owner', zmiana na 'sitter' nastąpi po ukończeniu wizarda
        UserProfile::create([
            'user_id' => $user->id,
            'role' => 'owner',
            'first_name' => $firstName,
            'last_name' => $lastName ?: $firstName, // Jeśli nie ma nazwiska, użyj imienia
        ]);

        Auth::login($user);

        // Jeśli wybrano rejestrację z kreatorem
        if ($this->withWizard) {
            $this->redirect(route('profile.become-sitter'), navigate: true);
        } else {
            $this->redirect(route('profile.dashboard', absolute: false), navigate: true);
        }
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-3">
                Dołącz do PetHelp! 🐾
            </h1>
            <p class="text-lg text-gray-600">
                Wybierz sposób rejestracji i zacznij swoją przygodę
            </p>
        </div>

        {{-- Wybór ścieżki rejestracji --}}
        <div class="grid md:grid-cols-2 gap-8 mb-12" x-data="{ selectedPath: @entangle('withWizard') }">

            {{-- OPCJA 1: Szybka rejestracja --}}
            <div @click="selectedPath = false"
                 :class="!selectedPath ? 'border-emerald-500 bg-white shadow-xl scale-105' : 'border-gray-200 bg-white/80'"
                 class="relative border-2 rounded-3xl p-8 cursor-pointer transition-all duration-300 hover:shadow-lg group">

                {{-- Checkmark badge --}}
                <div x-show="!selectedPath"
                     x-transition
                     class="absolute -top-4 -right-4 w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center">
                        <span class="text-4xl">⚡</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Szybka rejestracja</h3>
                    <p class="text-gray-600 mb-6">
                        Podstawowe konto w 2 minuty
                    </p>

                    <ul class="text-left space-y-3 mb-6">
                        <li class="flex items-start text-sm text-gray-700">
                            <svg class="w-5 h-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Natychmiastowy dostęp do platformy
                        </li>
                        <li class="flex items-start text-sm text-gray-700">
                            <svg class="w-5 h-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Uzupełnisz profil później
                        </li>
                        <li class="flex items-start text-sm text-gray-700">
                            <svg class="w-5 h-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Idealne dla właścicieli zwierząt
                        </li>
                    </ul>

                    <div class="text-xs text-gray-500">
                        ⏱️ Zajmie to tylko 2 minuty
                    </div>
                </div>
            </div>

            {{-- OPCJA 2: Rejestracja z kreatorem --}}
            <div @click="selectedPath = true"
                 :class="selectedPath ? 'border-emerald-500 bg-white shadow-xl scale-105' : 'border-gray-200 bg-white/80'"
                 class="relative border-2 rounded-3xl p-8 cursor-pointer transition-all duration-300 hover:shadow-lg group">

                {{-- Checkmark badge --}}
                <div x-show="selectedPath"
                     x-transition
                     class="absolute -top-4 -right-4 w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>

                {{-- AI Chmurka --}}
                <div x-show="!selectedPath"
                     x-transition
                     class="absolute -top-16 left-1/2 -translate-x-1/2 z-10">
                    <div class="relative">
                        <div class="bg-gradient-to-r from-emerald-500 to-cyan-500 text-white rounded-2xl px-6 py-3 shadow-2xl whitespace-nowrap">
                            <p class="text-sm font-semibold flex items-center">
                                <span class="text-lg mr-2">🤖</span>
                                Nasz AI asystent pomoże Ci w 11 krokach!
                            </p>
                            {{-- Arrow --}}
                            <div class="absolute top-full left-1/2 -translate-x-1/2">
                                <div class="w-0 h-0 border-l-8 border-l-transparent border-r-8 border-r-transparent border-t-8 border-t-emerald-500"></div>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-emerald-400 opacity-20 rounded-2xl animate-ping"></div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-emerald-500 to-green-500 rounded-2xl flex items-center justify-center">
                        <span class="text-4xl">🚀</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Zostań Pet Sitterem</h3>
                    <p class="text-gray-600 mb-6">
                        Profesjonalne konto z pomocą AI
                    </p>

                    <ul class="text-left space-y-3 mb-6">
                        <li class="flex items-start text-sm text-gray-700">
                            <svg class="w-5 h-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <strong class="text-emerald-600">AI asystent</strong>&nbsp;krok po kroku
                        </li>
                        <li class="flex items-start text-sm text-gray-700">
                            <svg class="w-5 h-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Kompletny profil od razu
                        </li>
                        <li class="flex items-start text-sm text-gray-700">
                            <svg class="w-5 h-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Wyższe szanse na klientów
                        </li>
                        <li class="flex items-start text-sm text-gray-700">
                            <svg class="w-5 h-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <strong class="text-purple-600">Wskazówki i analiza rynku</strong>
                        </li>
                    </ul>

                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl p-3 text-xs">
                        <p class="text-emerald-700 font-semibold">⏱️ 15-20 minut</p>
                        <p class="text-emerald-600">11 kroków z AI asystentem</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formularz rejestracji --}}
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">

                {{-- Social Login --}}
                <div class="mb-6">
                    <p class="text-sm text-gray-600 text-center mb-4">Szybka rejestracja przez:</p>
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Google --}}
                        <button type="button"
                                class="flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Google</span>
                        </button>

                        {{-- Facebook --}}
                        <button type="button"
                                class="flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all duration-200 group">
                            <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Facebook</span>
                        </button>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">lub przez email</span>
                    </div>
                </div>

                {{-- Form --}}
                <form wire:submit="register" class="space-y-5">

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Imię i nazwisko
                        </label>
                        <input wire:model="name"
                               id="name"
                               type="text"
                               required
                               autofocus
                               autocomplete="name"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Adres email
                        </label>
                        <input wire:model="email"
                               id="email"
                               type="email"
                               required
                               autocomplete="username"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Hasło
                        </label>
                        <input wire:model="password"
                               id="password"
                               type="password"
                               required
                               autocomplete="new-password"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 @error('password') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 znaków, litery i cyfry</p>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Potwierdź hasło
                        </label>
                        <input wire:model="password_confirmation"
                               id="password_confirmation"
                               type="password"
                               required
                               autocomplete="new-password"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200">
                    </div>

                    {{-- Cloudflare Turnstile placeholder --}}
                    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-500">
                            🔒 Cloudflare Turnstile będzie tutaj
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            (captcha działająca lokalnie)
                        </p>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full py-4 px-6 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105"
                            style="background: linear-gradient(135deg, #10b981, #06b6d4);">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Zarejestruj</span>
                        </span>
                    </button>

                    {{-- Login Link --}}
                    <div class="text-center pt-4">
                        <a href="{{ route('login') }}"
                           wire:navigate
                           class="text-sm text-emerald-600 hover:text-emerald-700 font-medium underline-offset-4 hover:underline transition-colors duration-200">
                            Masz już konto? Zaloguj się
                        </a>
                    </div>
                </form>
            </div>

            {{-- Info box --}}
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>
                    Rejestrując się akceptujesz naszą
                    <a href="#" class="text-emerald-600 hover:text-emerald-700 underline">Politykę Prywatności</a>
                    i
                    <a href="#" class="text-emerald-600 hover:text-emerald-700 underline">Regulamin</a>
                </p>
            </div>
        </div>

    </div>
</div>
