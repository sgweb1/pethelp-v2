{{-- Krok 8: Zdjęcia profilu - Architektura v3.0 + UI v4 --}}
<div class="max-w-2xl mx-auto px-4" x-data="wizardStep8()" x-init="init()" x-cloak>

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Dodaj swoje zdjęcia</h1>
        <p class="text-gray-600 text-lg">Zdjęcia zwiększają zaufanie i szanse na zdobycie klientów</p>
    </div>

    <div class="space-y-6">
        {{-- Zdjęcie profilowe Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">
                    Zdjęcie profilowe <span class="text-red-500">*</span>
                </h3>
                <button type="button"
                        @click="$wire.showAIPanel = !$wire.showAIPanel"
                        class="flex items-center text-xs cursor-pointer hover:scale-105 transition-transform duration-200 px-3 py-1.5 rounded-lg hover:bg-emerald-50">
                    <div class="w-5 h-5 bg-gradient-to-r from-emerald-500 to-green-500 rounded-full flex items-center justify-center mr-2">
                        <span class="text-white text-xs">💡</span>
                    </div>
                    <span class="font-medium text-gray-700" x-text="$wire.showAIPanel ? 'Ukryj wskazówki' : 'Wskazówki AI'"></span>
                </button>
            </div>

            <div x-show="typeof hasProfilePhoto !== 'undefined' && !hasProfilePhoto">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors duration-200">
                    <div class="text-4xl mb-4">👤</div>
                    <div class="mb-4">
                        <p class="text-gray-600 mb-2">Przeciągnij zdjęcie tutaj lub kliknij, aby wybrać</p>
                        <p class="text-sm text-gray-500">JPG, PNG lub WebP (maks. 5MB)</p>
                    </div>
                    <input
                        type="file"
                        accept="image/*"
                        wire:model="profilePhoto"
                        class="hidden"
                        id="profilePhotoInput">
                    <label
                        for="profilePhotoInput"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 cursor-pointer transition-colors duration-200">
                        <span data-svg-icon="upload" data-svg-size="16x16" data-svg-classes="text-white mr-2"></span>
                        Wybierz zdjęcie
                    </label>
                </div>
            </div>

            <div x-show="typeof hasProfilePhoto !== 'undefined' && hasProfilePhoto && profilePhoto?.url" class="relative">
                <div class="border-2 border-emerald-500 bg-emerald-50 rounded-lg p-4">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <img :src="(typeof profilePhoto !== 'undefined' && profilePhoto?.url) || ''"
                                 class="w-20 h-20 rounded-lg object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900" x-text="(typeof profilePhoto !== 'undefined' && profilePhoto?.name) || ''"></p>
                            <p x-show="(typeof profilePhoto !== 'undefined') && profilePhoto?.size > 0" class="text-sm text-gray-500" x-text="(typeof formatFileSize === 'function') ? formatFileSize(profilePhoto?.size || 0) : ''"></p>
                            <div class="mt-2 flex space-x-2">
                                <label for="profilePhotoInput"
                                       class="text-sm text-emerald-600 hover:text-emerald-500 cursor-pointer">
                                    Zmień zdjęcie
                                </label>
                                <button type="button"
                                        @click="(typeof removeProfilePhoto === 'function') && removeProfilePhoto()"
                                        class="text-sm text-red-600 hover:text-red-500">
                                    Usuń
                                </button>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span data-svg-icon="check" data-svg-size="20x20" data-svg-classes="text-emerald-600"></span>
                        </div>
                    </div>
                </div>
            </div>

            @error('profilePhoto')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Zdjęcia domu/otoczenia Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">
                    Zdjęcia Twojego domu/otoczenia
                </h3>
                <span class="text-sm text-gray-500" x-text="`${typeof homePhotosCount !== 'undefined' ? homePhotosCount : 0}/${typeof maxHomePhotos !== 'undefined' ? maxHomePhotos : 5}`"></span>
            </div>

            <p class="text-gray-600 mb-4">
                Dodaj zdjęcia miejsca, gdzie będziesz opiekować się zwierzętami (opcjonalne)
            </p>

            {{-- Upload Area for Home Photos --}}
            <div x-show="typeof canAddMoreHomePhotos !== 'undefined' && canAddMoreHomePhotos" class="mb-4">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors duration-200">
                    <div class="text-3xl mb-4">🏠</div>
                    <div class="mb-4">
                        <p class="text-gray-600 mb-2">Dodaj zdjęcia swojego domu</p>
                        <p class="text-sm text-gray-500">JPG, PNG lub WebP (maks. 5MB każde)</p>
                        <p class="text-xs text-gray-400 mt-1" x-text="`Możesz dodać do ${typeof maxHomePhotos !== 'undefined' ? maxHomePhotos : 5} zdjęć (zostało ${typeof maxHomePhotos !== 'undefined' && typeof homePhotosCount !== 'undefined' ? maxHomePhotos - homePhotosCount : 5})`"></p>
                    </div>
                    <input
                        type="file"
                        accept="image/*"
                        multiple
                        @change="(typeof handleHomePhotosUpload === 'function') && handleHomePhotosUpload($event)"
                        class="hidden"
                        id="homePhotosInput">
                    <label
                        for="homePhotosInput"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer transition-colors duration-200">
                        <span data-svg-icon="upload" data-svg-size="16x16" data-svg-classes="text-white mr-2"></span>
                        Wybierz zdjęcia
                    </label>

                    {{-- Loading indicator --}}
                    <div x-show="typeof uploadingHomePhotos !== 'undefined' && uploadingHomePhotos" class="mt-3">
                        <div class="flex items-center justify-center text-blue-600">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm" x-text="`Przesyłanie zdjęć... ${(typeof uploadProgress !== 'undefined') ? uploadProgress : '0%'}`"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Home Photos Grid --}}
            <div x-show="typeof hasHomePhotos !== 'undefined' && hasHomePhotos" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <template x-for="(photo, index) in ((typeof homePhotos !== 'undefined') ? homePhotos : [])" :key="index">
                    <div class="relative group">
                        <img :src="photo.url"
                             class="w-full h-32 object-cover rounded-lg border-2 border-gray-200">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                            <button
                                type="button"
                                @click="(typeof removeHomePhoto === 'function') && removeHomePhoto(index)"
                                class="opacity-0 group-hover:opacity-100 bg-red-600 text-white p-3 rounded-full hover:bg-red-700 transition-all duration-200"
                                aria-label="Usuń zdjęcie">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            @error('homePhotos')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Photos Summary Card --}}
        <div x-show="(typeof hasProfilePhoto !== 'undefined' && hasProfilePhoto) || (typeof hasHomePhotos !== 'undefined' && hasHomePhotos)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                <span class="text-xl mr-2">✅</span>
                Twoje zdjęcia
            </h4>
            <div class="text-gray-700 space-y-2">
                <div x-show="typeof hasProfilePhoto !== 'undefined' && hasProfilePhoto" class="text-sm flex items-center">
                    <span class="text-xl mr-2">📸</span>
                    <span>Zdjęcie profilowe dodane</span>
                </div>
                <div x-show="typeof hasHomePhotos !== 'undefined' && hasHomePhotos" class="text-sm flex items-center">
                    <span class="text-xl mr-2">🏠</span>
                    <span x-text="`${(typeof homePhotosCount !== 'undefined') ? homePhotosCount : 0} zdjęć domu`"></span>
                </div>
            </div>
        </div>
    </div>
</div> {{-- Koniec głównego wrappera z max-w-2xl --}}

{{--
    ✅ ARCHITEKTURA v3.0 - EXTERNAL COMPONENT + UI v4

    Komponent wizardStep8 został przeniesiony do zewnętrznego pliku:
    📁 resources/js/components/wizard-step-8-v3.js

    Nowa architektura v3.0:
    - ✅ Stateless components (brak lokalnego state)
    - ✅ Single Source of Truth przez window.WizardState
    - ✅ Eliminacja duplikacji zmiennych między krokami
    - ✅ Centralized state management

    UI v4:
    - ✅ max-w-2xl mx-auto px-4 wrapper
    - ✅ bg-white rounded-2xl shadow-lg cards
    - ✅ Przycisk AI Panel w nagłówku
    - ✅ Spójny design z krokami 5-7

    Import w app.js: import './components/wizard-step-8-v3';

    Wszystkie zmienne i metody są teraz computed properties z globalnego state:
    - profilePhoto, homePhotos, hasProfilePhoto, hasHomePhotos
    - homePhotosCount, maxHomePhotos, canAddMoreHomePhotos
    - updateProfilePhoto(), removeProfilePhoto()
    - addHomePhoto(), removeHomePhoto()
    - handleProfilePhotoUpload(), handleHomePhotosUpload()
    - validateImageFile(), formatFileSize()
--}}
