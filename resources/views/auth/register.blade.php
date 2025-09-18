<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Role Selection -->
        <div class="mb-6">
            <x-input-label for="role" value="Typ konta" />
            <div class="mt-2 grid grid-cols-2 gap-3">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer {{ $role === 'owner' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}">
                    <input type="radio" name="role" value="owner" {{ $role === 'owner' ? 'checked' : '' }} class="text-indigo-600">
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">Właściciel</div>
                        <div class="text-xs text-gray-500">Szukam opiekuna dla pupila</div>
                    </div>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer {{ $role === 'sitter' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}">
                    <input type="radio" name="role" value="sitter" {{ $role === 'sitter' ? 'checked' : '' }} class="text-indigo-600">
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">Opiekun</div>
                        <div class="text-xs text-gray-500">Opiekuję się zwierzętami</div>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" value="Imię" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" value="Nazwisko" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Name (Username) -->
        <div class="mt-4">
            <x-input-label for="name" value="Nazwa użytkownika" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Adres email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" value="Telefon (opcjonalnie)" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Hasło" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Powtórz hasło" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                Masz już konto?
            </a>

            <x-primary-button class="ms-4">
                Zarejestruj się
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
