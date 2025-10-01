{{-- Krok 11: Podgląd profilu i finalizacja - ENHANCED v5 --}}
<div class="max-w-5xl mx-auto px-4" x-data="{
    // Lokalny stan synchronizowany z Livewire
    agreedToTerms: @js($agreedToTerms ?? false),
    marketingConsent: @js($marketingConsent ?? false),

    // Toggle właściwości
    toggleProperty(property) {
        this[property] = !this[property];
        this.updateLivewire(property, this[property]);
    },

    // Programatyczna aktualizacja Livewire bez DOM morphing
    updateLivewire(property, value) {
        if (window.Livewire && this.$wire) {
            this.$wire.set(property, value, false);
        }
    }
}">
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-block mb-4">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                <span class="text-3xl">🎉</span>
            </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-3">Twój profil jest prawie gotowy!</h1>
        <p class="text-gray-600 text-lg">Sprawdź wszystkie informacje i zatwierdź publikację profilu</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Główna sekcja - Podgląd profilu (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Profile Card --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                {{-- Header z gradientem --}}
                <div class="relative bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-8">
                    <div class="flex items-start space-x-6">
                        {{-- Profile Photo --}}
                        <div class="relative">
                            @if($profilePhoto)
                                <div class="w-24 h-24 rounded-full overflow-hidden bg-white border-4 border-white shadow-lg">
                                    @php
                                        $photoUrl = is_object($profilePhoto) ? $profilePhoto->temporaryUrl() : (is_array($profilePhoto) ? ($profilePhoto['url'] ?? '') : '');
                                    @endphp
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="Profile" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                            <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="w-24 h-24 rounded-full bg-white border-4 border-white shadow-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Verification Badge --}}
                            @if($identityDocument)
                                <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Basic Info --}}
                        <div class="flex-1 text-white">
                            <h2 class="text-2xl font-bold mb-1">{{ Auth::user()->name }}</h2>
                            <p class="text-purple-100 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                {{ $address ?: 'Lokalizacja nie podana' }}
                            </p>

                            {{-- Quick Stats --}}
                            <div class="flex flex-wrap items-center gap-4 text-sm">
                                <div class="flex items-center bg-white/20 rounded-lg px-3 py-1">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Nowy profil
                                </div>
                                @if($yearsOfExperience > 0)
                                    <div class="flex items-center bg-white/20 rounded-lg px-3 py-1">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $yearsOfExperience }} {{ $yearsOfExperience == 1 ? 'rok' : 'lata' }}
                                    </div>
                                @endif
                                @if($serviceRadius)
                                    <div class="flex items-center bg-white/20 rounded-lg px-3 py-1">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                                        </svg>
                                        Promień {{ $serviceRadius }}km
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Content Section --}}
                <div class="p-6 space-y-6">
                    {{-- Motywacja --}}
                    @if($motivation)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                <span class="text-2xl mr-2">💭</span>
                                Dlaczego chcę być pet sitterem?
                            </h3>
                            <p class="text-gray-700 leading-relaxed bg-purple-50 border-l-4 border-purple-500 p-4 rounded-r-lg italic">
                                "{{ $motivation }}"
                            </p>
                        </div>
                    @endif

                    {{-- O mnie / Doświadczenie --}}
                    @if($experienceDescription)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                <span class="text-2xl mr-2">📝</span>
                                O mnie
                            </h3>
                            <p class="text-gray-600 leading-relaxed">{{ $experienceDescription }}</p>
                        </div>
                    @endif

                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- Usługi --}}
                        @if(!empty($serviceTypes))
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="text-xl mr-2">🛠️</span>
                                    Moje usługi
                                </h3>
                                <div class="space-y-2">
                                    @php
                                        $serviceNames = [
                                            'dog_walking' => ['name' => 'Spacery z psem', 'icon' => '🐕'],
                                            'pet_sitting' => ['name' => 'Opieka w domu', 'icon' => '🏠'],
                                            'pet_boarding' => ['name' => 'Opieka u opiekuna', 'icon' => '🏡'],
                                            'overnight_care' => ['name' => 'Opieka nocna', 'icon' => '🌙'],
                                            'pet_transport' => ['name' => 'Transport', 'icon' => '🚗'],
                                            'vet_visits' => ['name' => 'Wizyty u weterynarza', 'icon' => '⚕️'],
                                            'grooming' => ['name' => 'Pielęgnacja', 'icon' => '✂️'],
                                            'feeding' => ['name' => 'Karmienie', 'icon' => '🍽️']
                                        ];
                                    @endphp
                                    @foreach($serviceTypes as $service)
                                        <div class="flex items-center justify-between p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
                                            <span class="flex items-center text-sm font-medium text-gray-900">
                                                <span class="mr-2">{{ $serviceNames[$service]['icon'] ?? '📋' }}</span>
                                                {{ $serviceNames[$service]['name'] ?? $service }}
                                            </span>
                                            @if(isset($servicePricing[$service]) && $servicePricing[$service] > 0)
                                                <span class="text-emerald-700 font-bold">{{ $servicePricing[$service] }} PLN</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Zwierzęta --}}
                        @if(!empty($animalTypes))
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="text-xl mr-2">🐾</span>
                                    Zajmuję się
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $animalNames = [
                                            'dogs' => '🐕 Psy',
                                            'cats' => '🐱 Koty',
                                            'rabbits' => '🐰 Króliki',
                                            'birds' => '🦜 Ptaki',
                                            'fish' => '🐠 Ryby',
                                            'hamsters' => '🐹 Chomiki',
                                            'reptiles' => '🦎 Gady'
                                        ];
                                    @endphp
                                    @foreach($animalTypes as $animal)
                                        <span class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">
                                            {{ $animalNames[$animal] ?? $animal }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Środowisko --}}
                    @if($homeType)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                <span class="text-xl mr-2">🏠</span>
                                Moje środowisko
                            </h3>
                            <div class="grid md:grid-cols-2 gap-3">
                                @php
                                    $homeTypes = [
                                        'apartment' => ['icon' => '🏢', 'name' => 'Mieszkanie'],
                                        'house' => ['icon' => '🏠', 'name' => 'Dom jednorodzinny'],
                                        'studio' => ['icon' => '🏡', 'name' => 'Kawalerka/Studio'],
                                        'townhouse' => ['icon' => '🏘️', 'name' => 'Dom szeregowy']
                                    ];
                                @endphp
                                <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <span class="text-2xl mr-2">{{ $homeTypes[$homeType]['icon'] ?? '🏠' }}</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $homeTypes[$homeType]['name'] ?? $homeType }}</span>
                                </div>
                                @if($hasGarden)
                                    <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg text-green-700">
                                        <span class="text-xl mr-2">🌱</span>
                                        <span class="text-sm font-medium">Ogród/balkon</span>
                                    </div>
                                @endif
                                @if(!$isSmoking)
                                    <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg text-green-700">
                                        <span class="text-xl mr-2">🚭</span>
                                        <span class="text-sm font-medium">Bez dymu</span>
                                    </div>
                                @endif
                                @if($hasOtherPets && !empty($otherPets))
                                    <div class="flex items-center p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                        <span class="text-xl mr-2">🐾</span>
                                        <span class="text-sm font-medium text-gray-900">{{ count($otherPets) }} zwierz{{ count($otherPets) > 1 ? 'ęta' : 'ę' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Zdjęcia domu --}}
                    @if(!empty($homePhotos))
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                <span class="text-xl mr-2">📸</span>
                                Zdjęcia mojego domu
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach(array_slice($homePhotos, 0, 4) as $photo)
                                    @php
                                        $homePhotoUrl = is_object($photo) ? $photo->temporaryUrl() : (is_array($photo) ? ($photo['url'] ?? '') : '');
                                    @endphp
                                    @if($homePhotoUrl)
                                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                                            <img src="{{ $homePhotoUrl }}" alt="Home photo" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @if(count($homePhotos) > 4)
                                <p class="text-sm text-gray-500 mt-2">+ {{ count($homePhotos) - 4 }} więcej zdjęć</p>
                            @endif
                        </div>
                    @endif

                    {{-- Referencje --}}
                    @if(!empty($references))
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                <span class="text-xl mr-2">⭐</span>
                                Referencje
                            </h3>
                            <div class="space-y-2">
                                @foreach($references as $reference)
                                    @if(!empty($reference['name']))
                                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                            <div class="font-medium text-gray-900">{{ $reference['name'] }}</div>
                                            @if(!empty($reference['relation']))
                                                <div class="text-sm text-gray-600">{{ $reference['relation'] }}</div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Regulamin i zgody --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="text-xl mr-2">📄</span>
                    Wymagane zgody
                </h3>
                <div class="space-y-4">
                    <label class="flex items-start cursor-pointer group">
                        <input type="checkbox"
                               :checked="agreedToTerms"
                               @change="toggleProperty('agreedToTerms')"
                               class="mt-1 mr-3 w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                        <div class="text-sm">
                            <span class="text-gray-900">
                                Akceptuję
                                <a href="#" class="text-purple-600 hover:text-purple-700 underline">regulamin</a>
                                i
                                <a href="#" class="text-purple-600 hover:text-purple-700 underline">politykę prywatności</a>
                                PetHelp <span class="text-red-500">*</span>
                            </span>
                        </div>
                    </label>
                    @error('agreedToTerms')
                        <p class="text-sm text-red-600 flex items-center ml-8">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <label class="flex items-start cursor-pointer group">
                        <input type="checkbox"
                               :checked="marketingConsent"
                               @change="toggleProperty('marketingConsent')"
                               class="mt-1 mr-3 w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                        <div class="text-sm text-gray-600">
                            Chcę otrzymywać informacje o nowych funkcjach i promocjach (opcjonalne)
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Sticky Sidebar - Statystyki i "Co dalej?" (1/3) --}}
        <div class="lg:col-span-1">
            <div class="sticky top-4 space-y-6">
                {{-- Statystyki --}}
                <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-xl p-6 text-white">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <span class="text-2xl mr-2">📊</span>
                        Twój profil
                    </h3>

                    <div class="space-y-3">
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-sm opacity-90 mb-1">Usługi</div>
                            <div class="text-3xl font-bold">{{ !empty($serviceTypes) ? count($serviceTypes) : 0 }}</div>
                        </div>

                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-sm opacity-90 mb-1">Rodzaje zwierząt</div>
                            <div class="text-3xl font-bold">{{ !empty($animalTypes) ? count($animalTypes) : 0 }}</div>
                        </div>

                        @if($yearsOfExperience > 0)
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                                <div class="text-sm opacity-90 mb-1">Lata doświadczenia</div>
                                <div class="text-3xl font-bold">{{ $yearsOfExperience }}</div>
                            </div>
                        @endif

                        @if(!empty($homePhotos))
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                                <div class="text-sm opacity-90 mb-1">Zdjęcia</div>
                                <div class="text-3xl font-bold">{{ count($homePhotos) }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-4 border-t border-white/20">
                        <p class="text-xs opacity-75 italic flex items-start gap-2">
                            <span>💡</span>
                            <span>Kompletny profil zwiększa szanse na pierwsze zlecenia!</span>
                        </p>
                    </div>
                </div>

                {{-- Co dalej? --}}
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-2xl p-6">
                    <h3 class="font-bold text-emerald-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Co dalej?
                    </h3>
                    <ul class="space-y-3 text-sm text-emerald-800">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center mr-2 text-xs font-bold">1</span>
                            <span>Twoje konto zostanie utworzone</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center mr-2 text-xs font-bold">2</span>
                            <span>Będziesz mógł dokończyć konfigurację usług</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center mr-2 text-xs font-bold">3</span>
                            <span>Twój profil pojawi się w wynikach wyszukiwania</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center mr-2 text-xs font-bold">4</span>
                            <span>Zaczniesz otrzymywać pierwsze zapytania!</span>
                        </li>
                    </ul>
                </div>

                {{-- Przycisk publikacji --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 text-center">
                    <button wire:click="completeSitterRegistration"
                            class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all transform hover:scale-105 shadow-lg text-lg">
                        🎉 Publikuj profil
                    </button>
                    <p class="text-xs text-gray-500 mt-3">
                        Otrzymasz e-mail potwierdzający rejestrację
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Loading State --}}
<div wire:loading wire:target="completeSitterRegistration" class="fixed inset-0 bg-white bg-opacity-95 flex items-center justify-center z-50">
    <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-purple-600 mx-auto mb-4"></div>
        <p class="text-gray-900 font-semibold text-lg">Tworzenie profilu...</p>
        <p class="text-gray-600 text-sm mt-2">To potrwa tylko chwilę</p>
    </div>
</div>

{{--
    ✅ KROK 11 - ENHANCED v5 - Połączenie podglądu i finalizacji

    Zawiera:
    - Pełny podgląd profilu ze wszystkimi danymi
    - Motywacja, doświadczenie, usługi, zwierzęta
    - Środowisko, zdjęcia, referencje
    - Checkboxy regulaminu i zgody marketingowej
    - Sidebar ze statystykami
    - Sekcja "Co dalej?"
    - Max-w-5xl layout dla lepszego wykorzystania przestrzeni
--}}
