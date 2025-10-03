{{-- AI Assistant Panel V4 - zgodny z mockupem (lewe zaokrąglenia) --}}
<div x-show="$wire.showAIPanel"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="transform translate-x-full"
     x-transition:enter-end="transform translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="transform translate-x-0"
     x-transition:leave-end="transform translate-x-full"
     style="border-top-left-radius: 1rem !important; border-bottom-left-radius: 1rem !important; border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important;"
     class="fixed right-0 bottom-0 top-0 lg:top-20 w-full max-w-md sm:max-w-sm bg-white shadow-2xl z-[900] flex flex-col overflow-hidden">

    {{-- Panel Header --}}
    <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%); border-radius: 0 !important;" class="p-6 text-white flex-shrink-0">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-start space-x-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0 text-2xl">
                    🤖
                </div>
                <div>
                    <h2 class="text-xl font-bold mb-1">AI Assistant</h2>
                    <p class="text-emerald-50 text-sm">Wskazówki dla kroku {{ $currentStep }}</p>
                </div>
            </div>
            <button @click="$wire.showAIPanel = false"
                    class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                <span class="text-xl">✕</span>
            </button>
        </div>
    </div>

    {{-- Panel Content (Scrollable) --}}
    <div class="flex-1 overflow-y-auto p-6 space-y-6"
         style="-ms-overflow-style: none; scrollbar-width: none;">

        @if($currentStep == 6)
            {{-- KROK 6: MOTYWACJA (AI z kontekstem) --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📝</span>
                        Co napisać?
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Opisz swoją pasję do zwierząt</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Wspomnij o poprzednim doświadczeniu</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Wyjaśnij jak chcesz pomagać właścicielom</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💡</span>
                        Wskazówki AI
                    </h3>
                    <p class="text-sm text-purple-800 mb-3">
                        Dobry opis motywacji zawiera:
                    </p>
                    <ul class="text-sm text-purple-700 space-y-1">
                        <li>✓ Osobiste doświadczenia (100-200 znaków)</li>
                        <li>✓ Umiejętności i wiedzę (100-150 znaków)</li>
                        <li>✓ Cel i wartości (100-150 znaków)</li>
                    </ul>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⚡</span>
                        Generator AI
                    </h3>
                    <p class="text-sm text-emerald-800 mb-3">
                        Kliknij przycisk aby wygenerować profesjonalny opis na podstawie Twoich danych
                    </p>
                    <button wire:click="generateMotivationWithAI"
                            style="background: linear-gradient(135deg, #10b981, #06b6d4);"
                            class="w-full text-white font-semibold py-2 px-4 rounded-lg text-sm hover:scale-105 transition-transform">
                        ✨ Wygeneruj teraz
                    </button>
                </div>

                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">✏️</span>
                        Edytuj z AI
                    </h3>
                    <p class="text-sm text-indigo-800 mb-3">
                        Napisz jak AI ma zmienić Twój tekst (np. "dodaj że mam 5 lat doświadczenia")
                    </p>
                    <div class="space-y-2">
                        <input
                            type="text"
                            wire:model="aiEditPrompt"
                            placeholder="Np. dodaj że mam 10 lat doświadczenia..."
                            class="w-full px-3 py-2 border border-indigo-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button wire:click="editMotivationWithAI"
                                wire:loading.attr="disabled"
                                wire:target="editMotivationWithAI"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors">
                            <span wire:loading.remove wire:target="editMotivationWithAI">🪄 Edytuj tekstem</span>
                            <span wire:loading wire:target="editMotivationWithAI">⏳ Edytuję...</span>
                        </button>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h3 class="font-bold text-amber-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⏱️</span>
                        Szacowany czas
                    </h3>
                    <p class="text-sm text-amber-800">
                        Ten krok zajmie <strong>2-3 minuty</strong> z pomocą AI lub 5-7 minut samodzielnie
                    </p>
                </div>
            </div>
        @elseif($currentStep == 7)
            {{-- KROK 7: DOŚWIADCZENIE (AI z kontekstem) --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⭐</span>
                        Rodzaje doświadczenia
                    </h3>
                    <p class="text-sm text-blue-800 mb-3">Wybierz wszystkie typy doświadczenia, które posiadasz:</p>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>✓ Własne zwierzęta to najlepszy start</li>
                        <li>✓ Wolontariat pokazuje zaangażowanie</li>
                        <li>✓ Szkolenia = profesjonalizm</li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📚</span>
                        Opis doświadczenia
                    </h3>
                    <p class="text-sm text-purple-800 mb-3">
                        W opisie zawrzyj:
                    </p>
                    <ul class="text-sm text-purple-700 space-y-2">
                        <li class="flex items-start">
                            <span class="text-purple-500 mr-2">•</span>
                            <span>Z jakimi zwierzętami pracowałeś</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-purple-500 mr-2">•</span>
                            <span>Jakie sytuacje trudne rozwiązywałeś</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-purple-500 mr-2">•</span>
                            <span>Ukończone kursy lub certyfikaty</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⚡</span>
                        Generator AI
                    </h3>
                    <p class="text-sm text-emerald-800 mb-3">
                        Wygeneruj opis doświadczenia na podstawie wybranych typów i lat praktyki
                    </p>
                    <button wire:click="generateExperienceWithAI"
                            style="background: linear-gradient(135deg, #10b981, #06b6d4);"
                            class="w-full text-white font-semibold py-2 px-4 rounded-lg text-sm hover:scale-105 transition-transform">
                        ✨ Wygeneruj teraz
                    </button>
                </div>

                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">✏️</span>
                        Edytuj z AI
                    </h3>
                    <p class="text-sm text-indigo-800 mb-3">
                        Napisz jak AI ma zmienić opis doświadczenia
                    </p>
                    <div class="space-y-2">
                        <input
                            type="text"
                            wire:model="aiEditPrompt"
                            placeholder="Np. dodaj że znam behawiorystykę..."
                            class="w-full px-3 py-2 border border-indigo-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button wire:click="editExperienceWithAI"
                                wire:loading.attr="disabled"
                                wire:target="editExperienceWithAI"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors">
                            <span wire:loading.remove wire:target="editExperienceWithAI">🪄 Edytuj tekstem</span>
                            <span wire:loading wire:target="editExperienceWithAI">⏳ Edytuję...</span>
                        </button>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h3 class="font-bold text-amber-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💡</span>
                        Przykład
                    </h3>
                    <p class="text-sm text-amber-800 italic">
                        "Przez 3 lata byłam wolontariuszką w schronisku dla zwierząt, gdzie opiekowałam się psami wszystkich rozmiarów..."
                    </p>
                </div>
            </div>
        @elseif($currentStep == 1)
            {{-- KROK 1: RODZAJE ZWIERZĄT --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🐕</span>
                        Wybór rodzajów zwierząt
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Zaznacz tylko te zwierzęta, z którymi czujesz się komfortowo</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Im więcej rodzajów, tym szerszy krąg potencjalnych klientów</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Psy i koty to najpopularniejsze wybory (80% zapytań)</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💡</span>
                        Wskazówki AI
                    </h3>
                    <p class="text-sm text-purple-800 mb-3">
                        Statystyki zapotrzebowania na opiekunów:
                    </p>
                    <ul class="text-sm text-purple-700 space-y-1">
                        <li>✓ Psy: 60% wszystkich zapytań</li>
                        <li>✓ Koty: 25% wszystkich zapytań</li>
                        <li>✓ Małe zwierzęta: 10% zapytań</li>
                        <li>✓ Egzotyczne: 5% zapytań (ale wyższe stawki!)</li>
                    </ul>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📏</span>
                        Rozmiary zwierząt
                    </h3>
                    <div class="space-y-2 text-sm text-emerald-800">
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1">🐕‍🦺 Małe (do 10kg)</p>
                            <p class="text-xs text-emerald-700">Łatwiejsze w obsłudze, idealne na początek</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1">🐕 Średnie (10-25kg)</p>
                            <p class="text-xs text-emerald-700">Najbardziej popularne, wymaga doświadczenia</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1">🐕‍🦮 Duże (25kg+)</p>
                            <p class="text-xs text-emerald-700">Wymaga siły i doświadczenia, wyższe stawki</p>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🎯</span>
                        Optymalne kombinacje
                    </h3>
                    <div class="text-sm text-indigo-800 space-y-2">
                        <div class="bg-white rounded-lg p-3">
                            <p class="font-bold text-emerald-600 mb-2">💰 Najlepsza opcja dla początkujących:</p>
                            <ul class="text-xs space-y-1">
                                <li>• Psy małe i średnie</li>
                                <li>• Koty</li>
                                <li>• ~70% rynku</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="font-bold text-purple-600 mb-2">⭐ Dla doświadczonych:</p>
                            <ul class="text-xs space-y-1">
                                <li>• Wszystkie rozmiary psów</li>
                                <li>• Koty + małe zwierzęta</li>
                                <li>• ~90% rynku</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4">
                    <h3 class="font-bold text-cyan-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💰</span>
                        Wpływ na zarobki
                    </h3>
                    <p class="text-sm text-cyan-800 mb-2">
                        Średnie miesięczne przychody w zależności od specjalizacji:
                    </p>
                    <div class="bg-white rounded-lg p-3">
                        <div class="text-sm text-cyan-700 space-y-1">
                            <div class="flex justify-between">
                                <span>Tylko małe psy:</span>
                                <span class="font-bold">~1,200 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Psy małe + średnie:</span>
                                <span class="font-bold">~1,800 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Wszystkie rozmiary psów:</span>
                                <span class="font-bold text-emerald-600">~2,500 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Psy + koty + małe:</span>
                                <span class="font-bold text-emerald-600">~3,200 zł</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-cyan-600 italic mt-2">
                        * Wartości dla niepełnego etatu (15-20h/tydzień)
                    </p>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h3 class="font-bold text-amber-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⚠️</span>
                        Ważne uwagi
                    </h3>
                    <ul class="text-sm text-amber-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2">•</span>
                            <span>Duże psy wymagają więcej energii i czasu na spacery</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2">•</span>
                            <span>Koty są bardziej niezależne, ale wymagają cierpliwości</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2">•</span>
                            <span>Egzotyczne zwierzęta = specjalistyczna wiedza = wyższe stawki</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2">•</span>
                            <span>Możesz zawsze dodać więcej rodzajów później</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                    <h3 class="font-bold text-rose-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">❌</span>
                        Najczęstsze błędy
                    </h3>
                    <ul class="text-sm text-rose-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-rose-500 mr-2">✗</span>
                            <span>Zaznaczanie wszystkiego bez doświadczenia</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-rose-500 mr-2">✗</span>
                            <span>Wybór tylko jednego rodzaju (ogranicza klientów)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-rose-500 mr-2">✗</span>
                            <span>Ignorowanie preferencji rozmiarów</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-teal-50 border border-teal-200 rounded-xl p-4">
                    <h3 class="font-bold text-teal-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⏱️</span>
                        Szacowany czas
                    </h3>
                    <p class="text-sm text-teal-800">
                        Ten krok zajmie <strong>1 minutę</strong> - szybki wybór rodzajów
                    </p>
                </div>
            </div>
        @elseif($currentStep == 2)
            {{-- KROK 2: WYBÓR USŁUG --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🛠️</span>
                        Wybór usług
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Zaznacz tylko te usługi, które rzeczywiście możesz zapewnić</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Im więcej usług, tym szerszy krąg klientów</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Możesz dodać więcej usług później</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💡</span>
                        Najpopularniejsze usługi
                    </h3>
                    <p class="text-sm text-purple-800 mb-3">
                        Ranking zapotrzebowania na poszczególne usługi:
                    </p>
                    <div class="space-y-2 text-sm">
                        <div class="bg-white rounded-lg p-2">
                            <div class="flex items-center justify-between">
                                <span class="text-purple-700">🐕 Spacery z psem</span>
                                <span class="font-bold text-emerald-600">45%</span>
                            </div>
                            <p class="text-xs text-purple-600 mt-1">Najpopularniejsza usługa</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <div class="flex items-center justify-between">
                                <span class="text-purple-700">🏠 Opieka w domu właściciela</span>
                                <span class="font-bold text-emerald-600">30%</span>
                            </div>
                            <p class="text-xs text-purple-600 mt-1">Podczas wyjazdów</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <div class="flex items-center justify-between">
                                <span class="text-purple-700">🌙 Opieka nocna</span>
                                <span class="font-bold text-blue-600">15%</span>
                            </div>
                            <p class="text-xs text-purple-600 mt-1">Wyższe stawki</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <div class="flex items-center justify-between">
                                <span class="text-purple-700">🏡 Opieka u opiekuna</span>
                                <span class="font-bold text-blue-600">10%</span>
                            </div>
                            <p class="text-xs text-purple-600 mt-1">Wymaga odpowiednich warunków</p>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💰</span>
                        Stawki za usługi
                    </h3>
                    <p class="text-sm text-emerald-800 mb-2">
                        Średnie stawki w Polsce (2025):
                    </p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center bg-white rounded-lg p-2">
                            <span class="text-emerald-700">Spacer (30 min)</span>
                            <span class="font-bold text-emerald-600">25-35 zł</span>
                        </div>
                        <div class="flex justify-between items-center bg-white rounded-lg p-2">
                            <span class="text-emerald-700">Opieka dzienna (8h)</span>
                            <span class="font-bold text-emerald-600">80-120 zł</span>
                        </div>
                        <div class="flex justify-between items-center bg-white rounded-lg p-2">
                            <span class="text-emerald-700">Opieka nocna (12h)</span>
                            <span class="font-bold text-emerald-600">150-200 zł</span>
                        </div>
                        <div class="flex justify-between items-center bg-white rounded-lg p-2">
                            <span class="text-emerald-700">Transport</span>
                            <span class="font-bold text-emerald-600">40-80 zł</span>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🎯</span>
                        Optymalne kombinacje
                    </h3>
                    <div class="text-sm text-indigo-800 space-y-2">
                        <div class="bg-white rounded-lg p-3">
                            <p class="font-bold text-emerald-600 mb-2">💰 Podstawowy pakiet (dla początkujących):</p>
                            <ul class="text-xs space-y-1 text-indigo-700">
                                <li>• Spacery z psem</li>
                                <li>• Opieka w domu właściciela</li>
                                <li>• ~60% rynku</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="font-bold text-purple-600 mb-2">⭐ Pakiet rozszerzony:</p>
                            <ul class="text-xs space-y-1 text-indigo-700">
                                <li>• Podstawowy + Opieka nocna</li>
                                <li>• + Transport zwierząt</li>
                                <li>• ~85% rynku</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="font-bold text-blue-600 mb-2">🏆 Pełna oferta (dla doświadczonych):</p>
                            <ul class="text-xs space-y-1 text-indigo-700">
                                <li>• Wszystkie podstawowe usługi</li>
                                <li>• + Pielęgnacja + Wizyty weterynaryjne</li>
                                <li>• 100% rynku</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4">
                    <h3 class="font-bold text-cyan-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📊</span>
                        Wpływ na miesięczny przychód
                    </h3>
                    <p class="text-sm text-cyan-800 mb-2">
                        Więcej usług = więcej możliwości zarobku:
                    </p>
                    <div class="bg-white rounded-lg p-3">
                        <div class="text-sm text-cyan-700 space-y-1">
                            <div class="flex justify-between">
                                <span>Tylko spacery:</span>
                                <span class="font-bold">~1,000 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Spacery + opieka dzienna:</span>
                                <span class="font-bold">~1,800 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>+ Opieka nocna:</span>
                                <span class="font-bold text-emerald-600">~2,800 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Pełna oferta:</span>
                                <span class="font-bold text-emerald-600">~3,500+ zł</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-cyan-600 italic mt-2">
                        * Niepełny etat (15-20h/tydzień)
                    </p>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h3 class="font-bold text-amber-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⚠️</span>
                        Ważne uwagi
                    </h3>
                    <ul class="text-sm text-amber-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2">•</span>
                            <span><strong>Opieka nocna</strong> wymaga dodatkowego czasu i elastyczności</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2">•</span>
                            <span><strong>Opieka u opiekuna</strong> wymaga odpowiednich warunków w domu</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2">•</span>
                            <span><strong>Transport</strong> wymaga dostępu do samochodu</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-amber-600 mr-2">•</span>
                            <span><strong>Pielęgnacja</strong> wymaga dodatkowej wiedzy i narzędzi</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                    <h3 class="font-bold text-rose-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">❌</span>
                        Najczęstsze błędy
                    </h3>
                    <ul class="text-sm text-rose-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-rose-500 mr-2">✗</span>
                            <span>Oferowanie wszystkich usług bez doświadczenia</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-rose-500 mr-2">✗</span>
                            <span>Wybór tylko jednej usługi (ogranicza klientów)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-rose-500 mr-2">✗</span>
                            <span>Niedoszacowanie czasu potrzebnego na usługę</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-teal-50 border border-teal-200 rounded-xl p-4">
                    <h3 class="font-bold text-teal-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⏱️</span>
                        Szacowany czas
                    </h3>
                    <p class="text-sm text-teal-800">
                        Ten krok zajmie <strong>1-2 minuty</strong> - przemyślany wybór usług
                    </p>
                </div>
            </div>
        @elseif($currentStep == 4)
            {{-- KROK 4: DOSTĘPNOŚĆ --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📅</span>
                        Planowanie dostępności
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Wybierz dni, w które możesz pracować regularnie</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Ustaw realistyczne godziny - zawsze możesz je później zmienić</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Weekend to najczęstszy czas zapotrzebowania</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💡</span>
                        Wskazówki AI
                    </h3>
                    <p class="text-sm text-purple-800 mb-3">
                        Optymalna dostępność dla opiekuna:
                    </p>
                    <ul class="text-sm text-purple-700 space-y-1">
                        <li>✓ Minimum 3-4 dni w tygodniu</li>
                        <li>✓ Godziny 8:00-18:00 to najpopularniejszy zakres</li>
                        <li>✓ Weekendy zwiększają szanse na rezerwacje o 40%</li>
                        <li>✓ Elastyczność = więcej potencjalnych klientów</li>
                    </ul>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⚡</span>
                        Najlepsze praktyki
                    </h3>
                    <div class="space-y-2 text-sm text-emerald-800">
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1">🌅 Poranne spacery (7:00-9:00)</p>
                            <p class="text-xs text-emerald-700">Bardzo popularne przed pracą właścicieli</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1">🌆 Wieczorne spacery (17:00-19:00)</p>
                            <p class="text-xs text-emerald-700">Duże zapotrzebowanie po pracy</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1">🏠 Opieka dzienna (9:00-17:00)</p>
                            <p class="text-xs text-emerald-700">Idealna dla właścicieli pracujących w biurze</p>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📊</span>
                        Statystyki rynkowe
                    </h3>
                    <div class="text-sm text-indigo-800 space-y-2">
                        <div class="flex justify-between items-center bg-white rounded-lg p-2">
                            <span>Średnia liczba godzin/tydzień:</span>
                            <span class="font-bold text-emerald-600">15-25h</span>
                        </div>
                        <div class="flex justify-between items-center bg-white rounded-lg p-2">
                            <span>Najczęstsze dni rezerwacji:</span>
                            <span class="font-bold text-emerald-600">Pt-Nd</span>
                        </div>
                        <div class="flex justify-between items-center bg-white rounded-lg p-2">
                            <span>Średni czas usługi:</span>
                            <span class="font-bold text-emerald-600">1-2h</span>
                        </div>
                    </div>
                </div>

                <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4">
                    <h3 class="font-bold text-cyan-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💰</span>
                        Wpływ na zarobki
                    </h3>
                    <p class="text-sm text-cyan-800 mb-2">
                        Elastyczna dostępność zwiększa miesięczny przychód:
                    </p>
                    <div class="bg-white rounded-lg p-3">
                        <div class="text-sm text-cyan-700 space-y-1">
                            <div class="flex justify-between">
                                <span>3 dni/tydzień:</span>
                                <span class="font-bold">~800-1,200 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>5 dni/tydzień:</span>
                                <span class="font-bold">~1,500-2,000 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>7 dni + elastyczność:</span>
                                <span class="font-bold text-emerald-600">~2,500-3,500 zł</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-cyan-600 italic mt-2">
                        * Wartości zależą od lokalizacji i usług
                    </p>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h3 class="font-bold text-amber-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⏱️</span>
                        Szacowany czas
                    </h3>
                    <p class="text-sm text-amber-800">
                        Ten krok zajmie <strong>2-3 minuty</strong> - zaznacz dni i ustaw godziny
                    </p>
                </div>
            </div>
        @elseif($currentStep == 3)
            {{-- KROK 3: LOKALIZACJA - dynamiczne dane z WizardState --}}
            <div class="space-y-4"
                 x-data="{
                // Local reactive properties - będą aktualizowane przez event
                _localPopulation: 0,
                _localHouseholds: 0,
                _localPetOwners: 0,
                _localPotentialClients: 0,
                _localAreaType: 'obszar miejski',
                _localServiceRadius: 10,
                _localNotes: null,
                _hasData: false,
                _isLoading: false,

                init() {
                    console.log('🎬 AI Panel Step 5 initialized');
                    this.syncFromWizardState();

                    // Fallback: jeśli WizardState jest pusty, pobierz bezpośrednio z Livewire
                    if (!this._hasData) {
                        this.syncFromLivewire();
                    }
                },

                syncFromWizardState() {
                    const metrics = window.WizardState?.get('location.businessMetrics');
                    const population = window.WizardState?.get('location.estimatedPopulation') || 0;

                    console.log('🔄 Syncing AI Panel from WizardState:', { metrics, population });

                    if (metrics && metrics.population > 0) {
                        this._localPopulation = population;
                        this._localHouseholds = metrics.households || 0;
                        this._localPetOwners = metrics.petOwningHouseholds || 0;
                        this._localPotentialClients = metrics.potentialClients || 0;
                        this._localAreaType = metrics.areaType || 'obszar miejski';
                        this._localNotes = metrics.notes || null;
                        this._hasData = true;

                        console.log('✅ AI Panel synced:', {
                            population: this._localPopulation,
                            households: this._localHouseholds,
                            petOwners: this._localPetOwners,
                            potentialClients: this._localPotentialClients,
                            hasData: this._hasData
                        });
                    } else {
                        console.log('⏳ Brak danych w WizardState');
                        this._hasData = false;
                    }

                    this._localServiceRadius = window.WizardState?.get('location.serviceRadius') || 10;
                },

                syncFromLivewire() {
                    // Pobierz wartość bezpośrednio z Livewire jako fallback
                    const estimatedClients = $wire.estimatedClients || 0;
                    const serviceRadius = $wire.serviceRadius || 10;

                    console.log('🔄 Syncing AI Panel from Livewire:', { estimatedClients, serviceRadius });

                    // Aktualizuj radius
                    this._localServiceRadius = serviceRadius;

                    if (estimatedClients > 0) {
                        this._localPotentialClients = estimatedClients;
                        this._localPopulation = Math.round(estimatedClients / (0.37 * 0.25));
                        this._localHouseholds = Math.round(this._localPopulation / 2.5);
                        this._localPetOwners = Math.round(this._localHouseholds * 0.38);
                        this._hasData = true;

                        console.log('✅ AI Panel synced from Livewire:', {
                            potentialClients: this._localPotentialClients,
                            population: this._localPopulation,
                            serviceRadius: this._localServiceRadius,
                            hasData: this._hasData
                        });
                    }
                },

                refreshEstimation() {
                    console.log('🔄 Odświeżanie estymacji z AI Panel (krok 3)...');

                    // Użyj Livewire.find() do znalezienia komponentu i wywołania metody
                    const livewireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');

                    if (livewireId) {
                        const component = window.Livewire.find(livewireId);
                        if (component) {
                            console.log('✅ Znaleziono komponent Livewire, wywołuję refreshEstimation()');
                            component.call('refreshEstimation');
                        } else {
                            console.warn('⚠️ Nie znaleziono komponentu Livewire o ID:', livewireId);
                        }
                    } else {
                        console.warn('⚠️ Nie znaleziono elementu z wire:id');
                    }
                }
            }"
            @wizard-data-updated.window="syncFromWizardState(); console.log('🔔 AI Panel received wizard-data-updated event')"
            @estimation-calculating.window="
                console.log('🔄 Panel AI: Rozpoczęto przeliczanie estymacji');
                _isLoading = true;
            "
            @estimation-refreshed.window="
                console.log('✅ Estymacja odświeżona:', $event.detail);
                const count = $event.detail.count;
                console.log('📊 Nowa liczba klientów:', count);

                _isLoading = false;

                // Aktualizuj radius z Livewire
                _localServiceRadius = $wire.serviceRadius || 10;

                if (count > 0) {
                    // Zaktualizuj lokalne zmienne AI Panelu
                    _localPotentialClients = count;
                    _localPopulation = Math.round(count / (0.37 * 0.25));
                    _localHouseholds = Math.round(_localPopulation / 2.5);
                    _localPetOwners = Math.round(_localHouseholds * 0.38);
                    _hasData = true;

                    console.log('✅ UI zaktualizowane:', {
                        _localPotentialClients,
                        _localPopulation,
                        _localServiceRadius
                    });
                }
            "
            x-init="
                init();
                // Nasłuchuj na zmiany radius w czasie rzeczywistym
                $watch('$wire.serviceRadius', value => {
                    if (value !== undefined && value !== null) {
                        _localServiceRadius = value;
                        console.log('🔄 Panel AI: radius zmieniony na', value);
                    }
                });
            ">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📍</span>
                        Wybór lokalizacji
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Wybierz lokalizację blisko centrum miasta dla większego zasięgu klientów</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Promień 5-10 km to optymalny zakres dla większości opiekunów</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Większy promień = więcej klientów, ale też więcej czasu na dojazdy</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Przeciągnij marker na mapie aby ustawić dokładną lokalizację</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💡</span>
                        Wskazówki AI
                    </h3>
                    <p class="text-sm text-purple-800 mb-3">
                        Dobra lokalizacja to klucz do sukcesu:
                    </p>
                    <ul class="text-sm text-purple-700 space-y-1">
                        <li>✓ Sprawdź dostępność komunikacji miejskiej</li>
                        <li>✓ Uwzględnij parki i tereny zielone w okolicy</li>
                        <li>✓ Rozważ gęstość zaludnienia w promieniu</li>
                    </ul>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🎯</span>
                        Rekomendacje
                    </h3>
                    <p class="text-sm text-emerald-800">
                        Na podstawie analiz rynku:
                    </p>
                    <ul class="text-sm text-emerald-700 space-y-1 mt-2">
                        <li>📊 Średni promień: 8 km</li>
                        <li>📈 Optymalna liczba klientów: 15-20 w miesiącu</li>
                        <li>⏱️ Średni czas dojazdu: 15-20 minut</li>
                    </ul>
                </div>

                {{-- Analiza zasięgu - DYNAMICZNE DANE Z AI --}}
                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-indigo-900 flex items-center">
                            <span class="text-xl mr-2">📊</span>
                            Analiza zasięgu AI
                        </h3>
                        <button @click="refreshEstimation()"
                                :disabled="_isLoading"
                                class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1">
                            <svg x-show="!_isLoading" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <svg x-show="_isLoading" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="_isLoading ? 'Przeliczanie...' : 'Odśwież'"></span>
                        </button>
                    </div>
                    <p class="text-sm text-indigo-800 mb-2">
                        Statystyki dla promienia <strong x-text="_localServiceRadius + ' km'"></strong>:
                    </p>
                    <div class="bg-white/60 rounded-lg px-2 py-1 mb-2">
                        <p class="text-xs text-indigo-600 italic">
                            📍 Dane z Eurostat 2021
                        </p>
                    </div>

                    {{-- Loading State --}}
                    <div x-show="!_hasData || _isLoading" class="bg-white rounded-lg p-3 mb-2">
                        <div class="flex items-center justify-center space-x-2 text-indigo-600">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm" x-text="_isLoading ? 'Przeliczanie...' : 'Obliczanie estymacji AI...'"></span>
                        </div>
                    </div>

                    {{-- Data Display --}}
                    <div x-show="_hasData && !_isLoading" class="bg-white rounded-lg p-3 mb-2">
                        <div class="text-sm text-indigo-700 space-y-2">
                            <div class="flex justify-between items-center">
                                <span>👥 Liczba mieszkańców:</span>
                                <span class="font-bold text-lg" x-text="_localPopulation ? '~' + _localPopulation.toLocaleString('pl-PL') : '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>🏠 Gospodarstwa domowe:</span>
                                <span class="font-bold text-lg" x-text="_localHouseholds ? _localHouseholds.toLocaleString('pl-PL') : '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>🐾 Z zwierzętami (~38%):</span>
                                <span class="font-bold text-lg text-emerald-600" x-text="_localPetOwners ? _localPetOwners.toLocaleString('pl-PL') : '-'"></span>
                            </div>
                            <div class="border-t border-indigo-200 mt-2 pt-2">
                                <div class="flex justify-between items-center">
                                    <span>🎯 Potencjalni klienci:</span>
                                    <span class="font-bold text-lg text-emerald-600" x-text="_localPotentialClients ? _localPotentialClients.toLocaleString('pl-PL') : '-'"></span>
                                </div>
                                <p class="text-xs text-indigo-600 mt-1">
                                    (~15% gospodarstw z zwierzętami)
                                </p>
                            </div>
                            <div class="border-t border-indigo-200 mt-2 pt-2">
                                <div class="flex justify-between items-center">
                                    <span>🏙️ Typ obszaru:</span>
                                    <span class="font-semibold capitalize" x-text="_localAreaType"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- AI Notes --}}
                    <div x-show="_localNotes" class="bg-white rounded-lg p-2 mt-2">
                        <p class="text-xs text-indigo-600">
                            <span class="font-semibold">💡 AI:</span> <span x-text="_localNotes"></span>
                        </p>
                    </div>
                </div>

                <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4">
                    <h3 class="font-bold text-cyan-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💰</span>
                        Estymacja zarobków
                    </h3>
                    <p class="text-sm text-cyan-800 mb-2">
                        Szacunkowy miesięczny przychód:
                    </p>
                    <div class="bg-white rounded-lg p-3 mb-2">
                        <div class="text-sm text-cyan-700 space-y-1">
                            <div class="flex justify-between">
                                <span>Spacery (15 x 30 zł):</span>
                                <span class="font-bold">450 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Opieka dzienna (8 x 80 zł):</span>
                                <span class="font-bold">640 zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Opieka nocna (4 x 120 zł):</span>
                                <span class="font-bold">480 zł</span>
                            </div>
                            <div class="border-t border-cyan-200 mt-2 pt-2 flex justify-between">
                                <span class="font-bold">Razem:</span>
                                <span class="font-bold text-lg text-emerald-600">1,570 zł</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-cyan-600 italic">
                        * Wartości orientacyjne dla średniego obłożenia
                    </p>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h3 class="font-bold text-amber-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⏱️</span>
                        Szacowany czas
                    </h3>
                    <p class="text-sm text-amber-800">
                        Ten krok zajmie <strong>1-2 minuty</strong> - wystarczy przeciągnąć marker na mapie
                    </p>
                </div>
            </div>
        @elseif($currentStep == 5)
            {{-- KROK 5: DOM I OGRÓD --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🏠</span>
                        Opis domu
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Szczery opis buduje zaufanie - opisz rzeczywiste warunki</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Ogród/balkon = przewaga konkurencyjna (ważne dla psów)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            <span>Środowisko bez dymu jest istotne dla klientów z alergią</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💡</span>
                        Wskazówki AI
                    </h3>
                    <p class="text-sm text-purple-800 mb-3">
                        Klienci zwracają uwagę na:
                    </p>
                    <ul class="text-sm text-purple-700 space-y-1">
                        <li>✓ Przestrzeń - zwierzęta potrzebują miejsca do poruszania</li>
                        <li>✓ Bezpieczeństwo - brak dostępu do niebezpiecznych miejsc</li>
                        <li>✓ Cisza - spokojne otoczenie = mniejszy stres dla zwierząt</li>
                        <li>✓ Inne zwierzęta - ważne dla socjalizacji i kompatybilności</li>
                    </ul>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🌟</span>
                        Atuty do podkreślenia
                    </h3>
                    <div class="space-y-2 text-sm text-emerald-800">
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1">🏡 Dom z ogrodem</p>
                            <p class="text-xs text-emerald-700">Świetne dla psów, które lubią biegać</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1">🏢 Mieszkanie z balkonem</p>
                            <p class="text-xs text-emerald-700">Dodatkowe miejsce do wietrzenia i obserwacji</p>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1">🚭 Bez dymu</p>
                            <p class="text-xs text-emerald-700">Zdrowe środowisko dla zwierząt z problemami oddechowymi</p>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🐾</span>
                        Inne zwierzęta w domu
                    </h3>
                    <p class="text-sm text-indigo-800 mb-2">
                        Transparentność = kluczowa:
                    </p>
                    <div class="text-sm text-indigo-700 space-y-1">
                        <div class="bg-white rounded-lg p-2 mb-1">
                            <p class="font-semibold mb-1">✅ Zalety posiadania innych zwierząt:</p>
                            <ul class="text-xs space-y-0.5">
                                <li>• Doświadczenie w zarządzaniu wieloma zwierzętami</li>
                                <li>• Socjalizacja dla towarzyskich zwierząt</li>
                                <li>• Aktywne środowisko</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <p class="font-semibold mb-1 text-amber-700">⚠️ Ważne informacje:</p>
                            <ul class="text-xs space-y-0.5 text-amber-600">
                                <li>• Zawsze sprawdzaj kompatybilność zwierząt przed przyjęciem</li>
                                <li>• Poinformuj klienta o temperamencie swoich zwierząt</li>
                                <li>• Zaplanuj wprowadzenie w kontrolowany sposób</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4">
                    <h3 class="font-bold text-cyan-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📊</span>
                        Statystyki rynkowe
                    </h3>
                    <div class="text-sm text-cyan-800 space-y-2">
                        <div class="bg-white rounded-lg p-2">
                            <div class="flex justify-between items-center">
                                <span>🏡 Preferowany typ:</span>
                                <span class="font-bold text-emerald-600">Dom z ogrodem</span>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <div class="flex justify-between items-center">
                                <span>🚭 Ceni środowisko bez dymu:</span>
                                <span class="font-bold text-emerald-600">~75%</span>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-2">
                            <div class="flex justify-between items-center">
                                <span>🐾 Akceptuje inne zwierzęta:</span>
                                <span class="font-bold text-emerald-600">~60%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h3 class="font-bold text-amber-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💰</span>
                        Wpływ na ceny
                    </h3>
                    <p class="text-sm text-amber-800 mb-2">
                        Dom/mieszkanie może wpłynąć na stawki:
                    </p>
                    <div class="bg-white rounded-lg p-3">
                        <div class="text-sm text-amber-700 space-y-1">
                            <div class="flex justify-between">
                                <span>Dom z ogrodem:</span>
                                <span class="font-bold text-emerald-600">+15-20%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Balkon/taras:</span>
                                <span class="font-bold text-emerald-600">+5-10%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Bez dymu:</span>
                                <span class="font-bold text-emerald-600">+5%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Doświadczenie z wieloma zwierzętami:</span>
                                <span class="font-bold text-emerald-600">+10%</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-amber-600 italic mt-2">
                        * Wartości orientacyjne zależą od lokalizacji
                    </p>
                </div>

                <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                    <h3 class="font-bold text-rose-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⚡</span>
                        Najczęstsze błędy
                    </h3>
                    <ul class="text-sm text-rose-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-rose-500 mr-2">✗</span>
                            <span>Ukrywanie informacji o innych zwierzętach</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-rose-500 mr-2">✗</span>
                            <span>Przesadzanie z opisem warunków mieszkaniowych</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-rose-500 mr-2">✗</span>
                            <span>Brak informacji o ograniczeniach (np. małe mieszkanie)</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-teal-50 border border-teal-200 rounded-xl p-4">
                    <h3 class="font-bold text-teal-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⏱️</span>
                        Szacowany czas
                    </h3>
                    <p class="text-sm text-teal-800">
                        Ten krok zajmie <strong>1-2 minuty</strong> - szybki wybór opcji
                    </p>
                </div>
            </div>
        @elseif($currentStep == 8)
            {{-- KROK 8: Zdjęcia profilu --}}
            <div class="space-y-4">
                {{-- Wskazówki główne --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📸</span>
                        Dlaczego zdjęcia są ważne?
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-1.5">
                        <li>• Profile ze zdjęciami otrzymują <strong>3x więcej zapytań</strong></li>
                        <li>• Pierwsze wrażenie liczy się najbardziej</li>
                        <li>• Budują zaufanie i wiarygodność</li>
                    </ul>
                </div>

                {{-- Idealne zdjęcie profilowe --}}
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">✨</span>
                        Idealne zdjęcie profilowe
                    </h3>
                    <ul class="text-sm text-purple-800 space-y-1.5">
                        <li>• Dobrze oświetlone (światło naturalne)</li>
                        <li>• Ty na pierwszym planie</li>
                        <li>• Uśmiech i przyjazna mimika</li>
                        <li>• Neutralne tło (bez zbędnych elementów)</li>
                        <li>• Wysokajej jakości (wyraźne, nierozmazane)</li>
                    </ul>
                </div>

                {{-- Zdjęcia domu - co pokazać --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🏠</span>
                        Zdjęcia domu - co pokazać?
                    </h3>
                    <ul class="text-sm text-emerald-800 space-y-1.5">
                        <li>• Ogród/balkon (jeśli masz)</li>
                        <li>• Przestrzeń dla zwierząt</li>
                        <li>• Czyste i uporządkowane pomieszczenia</li>
                        <li>• Bezpieczne ogrodzenie</li>
                        <li>• Miejsce do zabawy</li>
                    </ul>
                </div>

                {{-- Najczęstsze błędy --}}
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                    <h3 class="font-bold text-rose-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⚡</span>
                        Czego unikać?
                    </h3>
                    <ul class="text-sm text-rose-800 space-y-1.5">
                        <li>• Selfie w lustrze</li>
                        <li>• Ciemne, niewyraźne zdjęcia</li>
                        <li>• Zdjęcia z innych osób/dzieci</li>
                        <li>• Bałagan w tle</li>
                        <li>• Filtry Instagram (naturalne lepsze)</li>
                    </ul>
                </div>

                {{-- Szacowany czas --}}
                <div class="bg-teal-50 border border-teal-200 rounded-xl p-4">
                    <h3 class="font-bold text-teal-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⏱️</span>
                        Szacowany czas
                    </h3>
                    <p class="text-sm text-teal-800">
                        <strong>3-5 minut</strong> - wybór i upload zdjęć
                    </p>
                </div>
            </div>
        @elseif($currentStep == 9)
            {{-- KROK 9: Weryfikacja --}}
            <div class="space-y-4">
                {{-- Dlaczego weryfikacja --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🛡️</span>
                        Dlaczego weryfikacja?
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-1.5">
                        <li>• Zweryfikowani opiekunowie dostają <strong>5x więcej zleceń</strong></li>
                        <li>• Buduje zaufanie właścicieli</li>
                        <li>• Chroni Ciebie i klientów</li>
                        <li>• Zwiększa Twojąwiarygodność</li>
                    </ul>
                </div>

                {{-- Dowód osobisty --}}
                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🪪</span>
                        Dowód osobisty
                    </h3>
                    <ul class="text-sm text-indigo-800 space-y-1.5">
                        <li>• Możesz zasłonić numer PESEL</li>
                        <li>• Zaakceptujemy skan lub zdjęcie</li>
                        <li>• Dane są bezpiecznie przechowywane</li>
                        <li>• Weryfikacja zajmuje 24-48h</li>
                    </ul>
                </div>

                {{-- Referencje --}}
                <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4">
                    <h3 class="font-bold text-cyan-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">⭐</span>
                        Referencje (opcjonalne)
                    </h3>
                    <ul class="text-sm text-cyan-800 space-y-1.5">
                        <li>• Dodaj osoby które mogą poświadczyć Twoje umiejętności</li>
                        <li>• Mogą to być właściciele zwierząt, którymi się opiekowałeś</li>
                        <li>• Profile z referencjami są bardziej wiarygodne</li>
                    </ul>
                </div>

                {{-- Bezpieczeństwo danych --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🔒</span>
                        Bezpieczeństwo danych
                    </h3>
                    <p class="text-sm text-emerald-800">
                        Wszystkie dane są szyfrowane i przechowywane zgodnie z RODO.
                        Nie udostępniamy ich osobom trzecim.
                    </p>
                </div>
            </div>
        @elseif($currentStep == 10)
            {{-- KROK 10: Cennik --}}
            <div class="space-y-4">
                {{-- Strategia cenowa --}}
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💰</span>
                        Jak ustalić ceny?
                    </h3>
                    <ul class="text-sm text-purple-800 space-y-1.5">
                        <li>• <strong>Konkurencyjna:</strong> dopasuj do rynku w okolicy</li>
                        <li>• <strong>Premium:</strong> wyższe ceny = jakość i doświadczenie</li>
                        <li>• <strong>Dostępna:</strong> niższe ceny = więcej klientów</li>
                    </ul>
                </div>

                {{-- Analiza rynku --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📊</span>
                        Analiza cen w okolicy
                    </h3>
                    <p class="text-sm text-blue-800">
                        Wykorzystujemy <strong>AI do analizy cen</strong> innych opiekunów w Twojej okolicy.
                        Zobacz rekomendacje poniżej i dostosuj do swoich potrzeb.
                    </p>
                </div>

                {{-- Wskazówki cenowe --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h3 class="font-bold text-amber-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💡</span>
                        Wskazówki cenowe
                    </h3>
                    <ul class="text-sm text-amber-800 space-y-1.5">
                        <li>• Uwzględnij swoje doświadczenie</li>
                        <li>• Dodaj za dodatkowe usługi (leki, transport)</li>
                        <li>• Możesz zwiększyć ceny po zebraniu opinii</li>
                        <li>• Niższe ceny na start = szybszy rozwój</li>
                    </ul>
                </div>

                {{-- Potencjał zarobków --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💸</span>
                        Potencjał zarobków
                    </h3>
                    <p class="text-sm text-emerald-800">
                        Aktywni opiekunowie zarabiają <strong>2000-5000 PLN/mies</strong> (przy niepełnym wymiarze).
                        Zobacz kalkulator zarobków poniżej!
                    </p>
                </div>
            </div>
        @elseif($currentStep == 11)
            {{-- KROK 11: Finalizacja (połączone: Podsumowanie + Podgląd) --}}
            <div class="space-y-4">
                {{-- Sprawdź dane --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">✅</span>
                        Sprawdź swoje dane
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-1.5">
                        <li>• Przejrzyj wszystkie sekcje podsumowania</li>
                        <li>• Możesz wrócić do dowolnego kroku</li>
                        <li>• Upewnij się że wszystko jest aktualne</li>
                    </ul>
                </div>

                {{-- Podgląd profilu --}}
                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">👁️</span>
                        Podgląd profilu
                    </h3>
                    <p class="text-sm text-indigo-800 mb-3">
                        To jest <strong>dokładnie</strong> to co zobaczą klienci przeglądając Twój profil.
                    </p>
                    <ul class="text-sm text-indigo-700 space-y-1">
                        <li>✓ Zdjęcie profilowe jest wyraźne</li>
                        <li>✓ Opis brzmi profesjonalnie</li>
                        <li>✓ Ceny są konkurencyjne</li>
                        <li>✓ Wszystkie usługi są zaznaczone</li>
                        <li>✓ Lokalizacja jest poprawna</li>
                    </ul>
                </div>

                {{-- Co dalej --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🚀</span>
                        Co się stanie po publikacji?
                    </h3>
                    <ul class="text-sm text-emerald-800 space-y-1.5">
                        <li>• <strong>Natychmiast:</strong> Twój profil będzie widoczny</li>
                        <li>• <strong>24-48h:</strong> Weryfikacja dokumentów</li>
                        <li>• <strong>Od razu:</strong> Możesz otrzymywać zapytania</li>
                        <li>• Dostęp do pełnego dashboard'u</li>
                    </ul>
                </div>

                {{-- Regulamin --}}
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">📄</span>
                        Regulamin i polityka
                    </h3>
                    <p class="text-sm text-purple-800">
                        Zapoznaj się z regulaminem i polityką prywatności.
                        Dbamy o bezpieczeństwo zarówno opiekunów jak i właścicieli zwierząt.
                    </p>
                </div>

                {{-- Edycja profilu --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <h3 class="font-bold text-amber-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">✏️</span>
                        Możesz edytować
                    </h3>
                    <p class="text-sm text-amber-800">
                        Jeśli coś wymaga poprawy, możesz wrócić do dowolnego kroku.
                        Profil można też edytować później z dashboard'u.
                    </p>
                </div>

                {{-- Wsparcie --}}
                <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4">
                    <h3 class="font-bold text-cyan-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">💬</span>
                        Pomoc i wsparcie
                    </h3>
                    <p class="text-sm text-cyan-800">
                        Po publikacji otrzymasz e-mail z przewodnikiem dla nowych opiekunów.
                        Jesteśmy tu aby Ci pomóc!
                    </p>
                </div>

                {{-- Gotowy do publikacji --}}
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-300 rounded-xl p-4">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <span class="text-xl mr-2">🎉</span>
                        Gotowy do publikacji?
                    </h3>
                    <p class="text-sm text-emerald-800">
                        Gdy wszystko wygląda dobrze, kliknij <strong>"Publikuj profil"</strong>.
                        Twój profil będzie natychmiast widoczny dla tysięcy właścicieli zwierząt!
                    </p>
                </div>
            </div>
        @else
            {{-- Inne kroki - placeholder --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                    <span class="text-xl mr-2">🚧</span>
                    W budowie
                </h3>
                <p class="text-sm text-blue-800">
                    Wskazówki dla kroku {{ $currentStep }} będą wkrótce dostępne
                </p>
            </div>
        @endif

    </div>

    {{-- Panel Footer --}}
    <div style="border-radius: 0 !important;" class="flex-shrink-0 p-4 bg-gray-50 border-t border-gray-200">
        <button @click="$wire.showAIPanel = false"
                class="w-full py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-colors">
            Zamknij panel
        </button>
    </div>
</div>

{{-- Inline styles zapewniają brak zaokrągleń we wszystkich elementach panelu --}}

<style>
    /* Hide scrollbar but keep functionality */
    .overflow-y-auto::-webkit-scrollbar {
        display: none;
    }
</style>
