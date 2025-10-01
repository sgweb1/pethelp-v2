<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('profile.dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <form wire:submit="updateProfileInformation" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Imię i nazwisko <span class="text-red-500">*</span>
            </label>
            <input wire:model="name"
                   id="name"
                   name="name"
                   type="text"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                   required
                   autofocus
                   autocomplete="name"
                   placeholder="Wpisz swoje imię i nazwisko">
            @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Adres email <span class="text-red-500">*</span>
            </label>
            <input wire:model="email"
                   id="email"
                   name="email"
                   type="email"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                   required
                   autocomplete="username"
                   placeholder="twoj@email.com">
            @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                    <p class="text-sm text-orange-800">
                        Twój adres email nie został jeszcze zweryfikowany.
                    </p>
                    <button wire:click.prevent="sendVerification"
                            class="mt-2 text-sm text-orange-600 hover:text-orange-800 underline focus:outline-none">
                        Kliknij tutaj, aby ponownie wysłać email weryfikacyjny.
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600">
                            Nowy link weryfikacyjny został wysłany na Twój adres email.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Zapisz zmiany
            </button>

            <div x-data="{
                    show: false,
                    showMessage() {
                        this.show = true;
                        setTimeout(() => this.show = false, 3000);
                    }
                 }"
                 x-show="show"
                 x-transition
                 @profile-updated.window="showMessage()"
                 class="text-sm text-green-600 font-medium">
                Zapisano!
            </div>
        </div>
    </form>
</section>
