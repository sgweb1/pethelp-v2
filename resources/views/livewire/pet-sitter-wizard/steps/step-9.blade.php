{{-- Krok 9: Weryfikacja i dokumenty - Architektura v3.0 + UI v4 --}}
<div class="max-w-2xl mx-auto px-4" x-data="wizardStep9()" x-init="init()" x-cloak>

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Weryfikacja tożsamości</h1>
        <p class="text-gray-600 text-lg">Pomóż nam zweryfikować Twoją tożsamość i buduj zaufanie wśród właścicieli zwierząt</p>
    </div>

    <div class="space-y-6">
        {{-- Dowód osobisty Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">
                    Dowód osobisty <span class="text-red-500">*</span>
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

            <p class="text-gray-600 mb-4">
                Prześlij skan lub zdjęcie swojego dowodu osobistego (możesz zasłonić numer PESEL)
            </p>

            {{-- Upload Area for Identity Document --}}
            <div x-show="typeof hasIdentityDocument !== 'undefined' && !hasIdentityDocument">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors duration-200">
                    <div class="text-4xl mb-4">🪪</div>
                    <div class="mb-4">
                        <p class="text-gray-600 mb-2">Przeciągnij plik tutaj lub kliknij, aby wybrać</p>
                        <p class="text-sm text-gray-500">JPG, PNG lub PDF (maks. 10MB)</p>
                    </div>
                    <input
                        type="file"
                        accept="image/*,application/pdf"
                        wire:model="identityDocument"
                        class="hidden"
                        id="identityDocumentInput">
                    <label
                        for="identityDocumentInput"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 cursor-pointer transition-colors duration-200">
                        <span data-svg-icon="upload" data-svg-size="16x16" data-svg-classes="text-white mr-2"></span>
                        Wybierz plik
                    </label>
                </div>
            </div>

            {{-- Identity Document Preview --}}
            <div x-show="typeof hasIdentityDocument !== 'undefined' && hasIdentityDocument && identityDocument?.url" class="relative">
                <div class="border-2 border-emerald-500 bg-emerald-50 rounded-lg p-4">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <span class="text-3xl">🪪</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900" x-text="identityDocument?.name || ''"></p>
                            <p x-show="identityDocument?.size > 0" class="text-sm text-gray-500" x-text="formatFileSize(identityDocument?.size || 0)"></p>
                            <div class="mt-2 flex space-x-2">
                                <label for="identityDocumentInput"
                                       class="text-sm text-emerald-600 hover:text-emerald-500 cursor-pointer">
                                    Zmień plik
                                </label>
                                <button type="button"
                                        @click="removeIdentityDocument()"
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

            @error('identityDocument')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Oświadczenie o niekaralności Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Oświadczenie o niekaralności
                </h3>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded bg-blue-100 text-blue-800">
                    Opcjonalne
                </span>
            </div>

            <p class="text-gray-600 mb-4">
                Złóż oświadczenie, że nie byłeś/aś karany/a za przestępstwa
            </p>

            {{-- Switch Toggle --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        Oświadczam, że nie byłem/am karany/a za przestępstwa
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        To oświadczenie zwiększa zaufanie właścicieli zwierząt
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer ml-4">
                    <input
                        type="checkbox"
                        wire:model.live="hasCriminalRecordDeclaration"
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            {{-- Declaration Confirmed Message --}}
            <div x-show="typeof hasCriminalRecordDeclaration !== 'undefined' && hasCriminalRecordDeclaration"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mt-3 p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
                <div class="flex items-center">
                    <span data-svg-icon="check" data-svg-size="20x20" data-svg-classes="text-emerald-600 mr-2"></span>
                    <p class="text-sm text-emerald-800">
                        Oświadczenie zostało złożone
                    </p>
                </div>
            </div>

            @error('hasCriminalRecordDeclaration')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span data-svg-icon="exclamation" data-svg-size="16x16" data-svg-classes="text-red-600 mr-1"></span>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Referencje Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Referencje
                </h3>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded bg-blue-100 text-blue-800">
                    Opcjonalne
                </span>
            </div>

            <p class="text-gray-600 mb-4">
                Dodaj kontakt do osób, które mogą potwierdzić Twoje doświadczenie w opiece nad zwierzętami
            </p>

            {{-- References List --}}
            <div class="space-y-4">
                <template x-for="(reference, index) in ((typeof references !== 'undefined') ? references : [])" :key="index">
                    <div class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Imię i nazwisko *
                                    </label>
                                    <input
                                        type="text"
                                        x-model="reference.name"
                                        @input="updateReference(index, 'name', $event.target.value)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Jan Kowalski">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Telefon lub inna forma kontaktu <span class="text-gray-400 text-xs">(opcjonalnie)</span>
                                    </label>
                                    <input
                                        type="text"
                                        x-model="reference.phone"
                                        @input="updateReference(index, 'phone', $event.target.value)"
                                        maxlength="255"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="np. +48 123 456 789, email@example.com">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Relacja / Skąd się znacie *
                                    </label>
                                    <input
                                        type="text"
                                        x-model="reference.relation"
                                        @input="updateReference(index, 'relation', $event.target.value)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="np. Opiekowałem się jego psem przez rok">
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="removeReference(index)"
                                class="ml-4 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                aria-label="Usuń referencję">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Add Reference Button --}}
                <button
                    type="button"
                    @click="addReference()"
                    x-show="typeof canAddMoreReferences !== 'undefined' && canAddMoreReferences"
                    class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-colors duration-200 flex items-center justify-center">
                    <span data-svg-icon="plus" data-svg-size="20x20" data-svg-classes="mr-2"></span>
                    Dodaj referencję (maks. 3)
                </button>

                {{-- No References Message --}}
                <div x-show="typeof hasReferences !== 'undefined' && !hasReferences"
                     class="text-center py-6 text-gray-500 text-sm">
                    Nie dodano jeszcze żadnych referencji
                </div>
            </div>

            @error('references')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span data-svg-icon="exclamation" data-svg-size="16x16" data-svg-classes="text-red-600 mr-1"></span>
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>
</div> {{-- Koniec głównego wrappera z max-w-2xl --}}

{{--
    ✅ ARCHITEKTURA v3.0 - EXTERNAL COMPONENT + UI v4

    Komponent wizardStep9 został przeniesiony do zewnętrznego pliku:
    📁 resources/js/components/wizard-step-9-v3.js

    Nowa architektura v3.0:
    - ✅ Stateless components (brak lokalnego state)
    - ✅ Single Source of Truth przez window.WizardState
    - ✅ Eliminacja duplikacji zmiennych między krokami
    - ✅ Centralized state management

    Import w app.js: import './components/wizard-step-9-v3';

    Wszystkie zmienne i metody są teraz computed properties z globalnego state:
    - identityDocument, criminalRecord, references
    - hasIdentityDocument, hasCriminalRecordDeclaration, hasReferences
    - referencesCount, maxReferences, canAddMoreReferences
    - updateIdentityDocument(), removeIdentityDocument()
    - toggleCriminalRecordDeclaration()
    - addReference(), removeReference(), updateReference()
    - handleIdentityDocumentUpload(), formatFileSize()
--}}
