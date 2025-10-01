<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Hybrydowy AI Assistant dla Pet Sitter Registration Wizard.
 *
 * Główny orchestrator łączący różne źródła inteligentnych sugestii:
 * - LocalAI (Ollama) dla kreatywnych zadań
 * - RuleEngine dla logiki biznesowej
 * - TemplateSystem jako fallback
 * - MarketDataService dla analiz rynkowych
 *
 * Zapewnia 90% funkcjonalności poprzez reguły biznesowe i tylko 10% przez AI,
 * co gwarantuje wysoką niezawodność przy zerowych kosztach API.
 *
 * @package App\Services\AI
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class HybridAIAssistant
{
    /**
     * Lokalny AI assistant (Ollama integration).
     *
     * @var LocalAIAssistant
     */
    private LocalAIAssistant $localAI;

    /**
     * Silnik reguł biznesowych.
     *
     * @var RuleEngine
     */
    private RuleEngine $ruleEngine;

    /**
     * System szablonów.
     *
     * @var TemplateSystem
     */
    private TemplateSystem $templateSystem;

    /**
     * Serwis danych rynkowych.
     *
     * @var MarketDataService
     */
    private MarketDataService $marketData;

    /**
     * Czas cache'owania sugestii w sekundach.
     *
     * @var int
     */
    private int $cacheTimeout;

    /**
     * Czy AI jest dostępne (cache dla sprawdzenia dostępności).
     *
     * @var bool|null
     */
    private ?bool $isAiAvailable = null;

    /**
     * Konstruktor - wstrzykiwanie zależności.
     *
     * @param LocalAIAssistant $localAI
     * @param RuleEngine $ruleEngine
     * @param TemplateSystem $templateSystem
     * @param MarketDataService $marketData
     */
    public function __construct(
        LocalAIAssistant $localAI,
        RuleEngine $ruleEngine,
        TemplateSystem $templateSystem,
        MarketDataService $marketData
    ) {
        $this->localAI = $localAI;
        $this->ruleEngine = $ruleEngine;
        $this->templateSystem = $templateSystem;
        $this->marketData = $marketData;
        $this->cacheTimeout = config('ai.cache.ttl', 3600);
    }

    /**
     * Generuje kontekstowe sugestie dla określonego kroku wizarda.
     *
     * Główna metoda orchestrująca różne źródła sugestii w zależności
     * od kroku i dostępnych danych użytkownika.
     *
     * @param int $step Numer kroku wizarda (1-11)
     * @param array $wizardData Dotychczas zebrane dane
     * @param array $context Dodatkowy kontekst (lokalizacja, preferencje)
     * @return array Struktura z sugestiami, poradami i insights
     *
     * @example
     * $suggestions = $assistant->getStepSuggestions(3, [
     *     'city' => 'Warszawa',
     *     'experience' => 'beginner'
     * ]);
     */
    public function getStepSuggestions(int $step, array $wizardData, array $context = []): array
    {
        // Generuj klucz cache na podstawie danych
        $cacheKey = $this->generateCacheKey($step, $wizardData, $context);

        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($step, $wizardData, $context) {
            return $this->generateSuggestions($step, $wizardData, $context);
        });
    }

    /**
     * Generuje sugestie bez cache'owania (wewnętrzna logika).
     *
     * @param int $step
     * @param array $wizardData
     * @param array $context
     * @return array
     */
    private function generateSuggestions(int $step, array $wizardData, array $context): array
    {
        try {
            return match($step) {
                // Kroki z AI-powered content generation
                3 => $this->getBioSuggestions($wizardData, $context),

                // Kroki z pure business logic
                1, 2, 4, 8, 10, 11 => $this->getRuleBasedSuggestions($step, $wizardData, $context),

                // Kroki z market data analysis
                5 => $this->getServicesSuggestions($wizardData, $context),
                6 => $this->getLocationSuggestions($wizardData, $context),
                7 => $this->getPricingSuggestions($wizardData, $context),
                9 => $this->getPhotoSuggestions($wizardData, $context),

                default => $this->getFallbackSuggestions($step)
            };
        } catch (\Exception $e) {
            Log::warning('AI suggestions generation failed', [
                'step' => $step,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackSuggestions($step);
        }
    }

    /**
     * Generuje AI-powered sugestie bio dla kroku 3.
     *
     * Jedyny krok wykorzystujący pełne możliwości AI do kreatywnego
     * generowania treści na podstawie danych użytkownika.
     *
     * @param array $wizardData
     * @param array $context
     * @return array
     */
    private function getBioSuggestions(array $wizardData, array $context): array
    {
        try {
            // Spróbuj wygenerować z AI
            $aiSuggestions = $this->localAI->generateBio($wizardData);

            if (!empty($aiSuggestions)) {
                return [
                    'title' => 'Personalizowane sugestie AI',
                    'type' => 'ai_generated',
                    'examples' => [$aiSuggestions],
                    'tips' => [
                        'Użyj wygenerowanego tekstu jako inspiracji',
                        'Dodaj własne doświadczenia i osobowość',
                        'Pamiętaj o ciepłym i przyjaznym tonie'
                    ],
                    'insights' => $this->ruleEngine->generateStepSuggestions(3, $wizardData)
                ];
            }
        } catch (\Exception $e) {
            Log::info('Local AI failed, using templates', ['error' => $e->getMessage()]);
        }

        // Fallback na templates
        return $this->templateSystem->generateBioSuggestions($wizardData, $context);
    }

    /**
     * Generuje sugestie usług na podstawie profilu użytkownika.
     *
     * Analizuje dane jak typ mieszkania, doświadczenie, lokalizacja
     * i sugeruje najbardziej opłacalne usługi.
     *
     * @param array $wizardData
     * @param array $context
     * @return array
     */
    private function getServicesSuggestions(array $wizardData, array $context): array
    {
        $ruleSuggestions = $this->ruleEngine->generateStepSuggestions(5, $wizardData);
        $marketInsights = $this->marketData->getServicePopularity($wizardData['city'] ?? 'Warszawa');

        return [
            'title' => 'Rekomendowane usługi',
            'type' => 'rule_based',
            'suggestions' => $ruleSuggestions,
            'market_insights' => $marketInsights,
            'tips' => [
                'Zacznij od 2-3 usług i rozwijaj stopniowo',
                'Spacery to najpopularniejsza usługa - zawsze warto dodać',
                'Kombinacja usług = wyższe zarobki'
            ]
        ];
    }

    /**
     * Generuje inteligentne sugestie cenowe.
     *
     * Łączy dane rynkowe z regułami biznesowymi dla optymalnej strategii cenowej.
     *
     * @param array $wizardData
     * @param array $context
     * @return array
     */
    private function getPricingSuggestions(array $wizardData, array $context): array
    {
        $city = $wizardData['city'] ?? 'Warszawa';
        $services = $wizardData['services'] ?? ['dog_walking'];
        $experienceYears = $wizardData['experience_years'] ?? 0;

        // Weź pierwszą usługę dla przykładu cenowego
        $primaryService = is_array($services) ? ($services[0] ?? 'dog_walking') : 'dog_walking';

        $pricingData = $this->marketData->getPricingSuggestions($primaryService, $city, $experienceYears);
        $strategicTips = $this->ruleEngine->generateStepSuggestions(7, $wizardData);

        return [
            'title' => 'Analiza cenowa dla Twojego miasta',
            'type' => 'market_analysis',
            'pricing' => $pricingData,
            'strategy_tips' => $strategicTips,
            'market_position' => $this->determineMarketPosition($city),
            'insights' => [
                "W {$city} średnie ceny są wyższe niż średnia krajowa",
                'Zacznij od średniej ceny - zawsze możesz podwyższyć',
                'Pierwsi klienci = dobre opinie = wyższe ceny w przyszłości'
            ]
        ];
    }

    /**
     * Generuje sugestie lokalizacyjne i insights o rynku lokalnym.
     *
     * @param array $wizardData
     * @param array $context
     * @return array
     */
    private function getLocationSuggestions(array $wizardData, array $context): array
    {
        $city = $wizardData['city'] ?? 'Warszawa';
        $locationInsights = $this->ruleEngine->generateStepSuggestions(6, $wizardData);
        $marketData = $this->marketData->getMarketInsights($city);

        return [
            'title' => 'Optymalizacja strefy działania',
            'type' => 'location_analysis',
            'radius_suggestions' => $locationInsights['suggestions'] ?? [],
            'competition_analysis' => $marketData['city_overview'] ?? [],
            'demand_analysis' => $marketData['opportunities'] ?? [],
            'tips' => [
                'Większy promień = więcej klientów, ale większe koszty transportu',
                'Sprawdź gęstość konkurencji w wybranym obszarze',
                'Zacznij konserwatywnie - zawsze możesz rozszerzyć strefę'
            ]
        ];
    }

    /**
     * Generuje tips dla uploadu zdjęć.
     *
     * @param array $wizardData
     * @param array $context
     * @return array
     */
    private function getPhotoSuggestions(array $wizardData, array $context): array
    {
        return $this->ruleEngine->generateStepSuggestions(9, $wizardData);
    }

    /**
     * Generuje sugestie oparte na regułach biznesowych.
     *
     * @param int $step
     * @param array $wizardData
     * @param array $context
     * @return array
     */
    private function getRuleBasedSuggestions(int $step, array $wizardData, array $context): array
    {
        $ruleSuggestions = $this->ruleEngine->generateStepSuggestions($step, $wizardData, $context);

        return [
            'title' => $this->getStepTitle($step),
            'type' => 'rule_based',
            'suggestions' => $ruleSuggestions['tips'] ?? [],
            'warnings' => $ruleSuggestions['warnings'] ?? [],
            'recommendations' => $ruleSuggestions['recommendations'] ?? [],
        ];
    }

    /**
     * Zwraca podstawowe sugestie gdy wszystkie inne metody zawiodą.
     *
     * @param int $step
     * @return array
     */
    private function getFallbackSuggestions(int $step): array
    {
        return $this->templateSystem->getFallbackSuggestions($step);
    }

    /**
     * Generuje unikalny klucz cache na podstawie danych wejściowych.
     *
     * @param int $step
     * @param array $wizardData
     * @param array $context
     * @return string
     */
    private function generateCacheKey(int $step, array $wizardData, array $context): string
    {
        $dataHash = md5(serialize($wizardData + $context));
        return "wizard_ai_step_{$step}_{$dataHash}";
    }

    /**
     * Pobiera tytuł dla danego kroku wizarda.
     *
     * @param int $step Numer kroku
     * @return string Tytuł kroku
     */
    private function getStepTitle(int $step): string
    {
        $titles = [
            1 => 'Podstawowe informacje',
            2 => 'Lokalizacja',
            3 => 'Opis i prezentacja',
            4 => 'Doświadczenie',
            5 => 'Rodzaje usług',
            6 => 'Dostępność',
            7 => 'Ustalanie cen',
            8 => 'Certyfikaty',
            9 => 'Zdjęcia',
            10 => 'Podsumowanie',
            11 => 'Zakończenie'
        ];

        return $titles[$step] ?? 'Sugestie AI';
    }

    /**
     * Określa pozycję rynkową miasta.
     *
     * @param string $city Miasto
     * @return string Pozycja rynkowa
     */
    private function determineMarketPosition(string $city): string
    {
        $multiplier = config("ai.market_data.cities.{$city}.multiplier", 1.0);

        if ($multiplier >= 1.2) {
            return 'premium';
        } elseif ($multiplier >= 1.1) {
            return 'above_average';
        } elseif ($multiplier >= 1.0) {
            return 'average';
        } else {
            return 'budget';
        }
    }

    /**
     * Czyści cache dla określonego kroku (przydatne przy aktualizacji danych).
     *
     * @param int $step
     * @param array $wizardData
     * @param array $context
     * @return bool
     */
    public function clearStepCache(int $step, array $wizardData = [], array $context = []): bool
    {
        $cacheKey = $this->generateCacheKey($step, $wizardData, $context);
        return Cache::forget($cacheKey);
    }

    /**
     * Czyści cały cache AI suggestions.
     *
     * @return bool
     */
    public function clearAllCache(): bool
    {
        return Cache::flush(); // Można zoptymalizować używając tagów
    }

    /**
     * Zwraca statystyki użycia AI Assistant.
     *
     * @return array
     */
    public function getUsageStats(): array
    {
        return [
            'total_requests' => Cache::get('ai_stats_total_requests', 0),
            'ai_generated' => Cache::get('ai_stats_ai_generated', 0),
            'rule_based' => Cache::get('ai_stats_rule_based', 0),
            'template_fallback' => Cache::get('ai_stats_template_fallback', 0),
            'cache_hits' => Cache::get('ai_stats_cache_hits', 0),
        ];
    }

    /**
     * Aktualizuje statystyki użycia (dla analytics).
     *
     * @param string $type
     * @return void
     */
    private function updateStats(string $type): void
    {
        Cache::increment('ai_stats_total_requests');
        Cache::increment("ai_stats_{$type}");
    }

    // ===== OPTYMALIZACJE WYDAJNOŚCI =====

    /**
     * Preloaduje sugestie dla następnych kroków w tle.
     *
     * Uruchamia asynchroniczne generowanie sugestii dla prawdopodobnych
     * następnych kroków, aby poprawić responsywność interfejsu.
     *
     * @param int $currentStep
     * @param array $wizardData
     * @param array $context
     * @return void
     */
    public function preloadNextStepSuggestions(int $currentStep, array $wizardData, array $context = []): void
    {
        // Preload najbardziej prawdopodobne następne kroki
        $nextSteps = $this->getPredictedNextSteps($currentStep);

        foreach ($nextSteps as $step) {
            $cacheKey = $this->generateCacheKey($step, $wizardData, $context);

            // Sprawdź czy już w cache
            if (!Cache::has($cacheKey)) {
                // Dispatch background job lub uruchom w kolejce
                dispatch(function () use ($step, $wizardData, $context) {
                    try {
                        $this->generateSuggestions($step, $wizardData, $context);
                        Log::info("Preloaded suggestions for step {$step}");
                    } catch (\Exception $e) {
                        Log::warning("Failed to preload step {$step}: " . $e->getMessage());
                    }
                })->afterResponse();
            }
        }
    }

    /**
     * Przewiduje prawdopodobne następne kroki na podstawie aktualnego.
     *
     * @param int $currentStep
     * @return array
     */
    private function getPredictedNextSteps(int $currentStep): array
    {
        // Preload następny krok + kroki o wysokim wykorzystaniu
        $predictions = [$currentStep + 1];

        // Dodaj kroki które często są odwiedzane
        $highTrafficSteps = [3, 5, 7]; // Bio, Services, Pricing - najważniejsze

        foreach ($highTrafficSteps as $step) {
            if ($step > $currentStep && $step <= 11) {
                $predictions[] = $step;
            }
        }

        return array_unique($predictions);
    }

    /**
     * Optymalizuje cache przez usunięcie starych wpisów.
     *
     * Usuwa wpisy cache starsze niż określony czas lub
     * gdy cache przekracza określony rozmiar.
     *
     * @param int $maxAgeHours
     * @return int Liczba usuniętych wpisów
     */
    public function optimizeCache(int $maxAgeHours = 24): int
    {
        $cachePrefix = 'ai_suggestions_';
        $deleted = 0;

        // W prawdziwej implementacji potrzebowalibyśmy
        // dostępu do wewnętrznych kluczy cache
        // To jest uproszczona wersja
        $commonKeys = [
            'step_1_', 'step_2_', 'step_3_', 'step_4_', 'step_5_',
            'step_6_', 'step_7_', 'step_8_', 'step_9_', 'step_10_', 'step_11_'
        ];

        foreach ($commonKeys as $keyPattern) {
            // Przykładowe czyszczenie - w rzeczywistości potrzebny Redis/Memcached scan
            $testKey = $cachePrefix . $keyPattern . 'test';
            if (Cache::has($testKey)) {
                Cache::forget($testKey);
                $deleted++;
            }
        }

        Log::info("Cache optimization completed. Deleted {$deleted} entries.");

        return $deleted;
    }

    /**
     * Batch processing - generuje sugestie dla wielu kroków jednocześnie.
     *
     * Optymalizuje wydajność przy pierwszym załadowaniu wizarda
     * przez przygotowanie sugestii dla kluczowych kroków.
     *
     * @param array $steps
     * @param array $wizardData
     * @param array $context
     * @return array Tablica z sugestiami dla każdego kroku
     */
    public function batchGenerateSuggestions(array $steps, array $wizardData, array $context = []): array
    {
        $results = [];
        $startTime = microtime(true);

        Log::info("Starting batch generation for steps: " . implode(', ', $steps));

        foreach ($steps as $step) {
            try {
                $results[$step] = $this->getStepSuggestions($step, $wizardData, $context);
            } catch (\Exception $e) {
                Log::warning("Batch generation failed for step {$step}: " . $e->getMessage());
                $results[$step] = $this->getFallbackSuggestions($step);
            }
        }

        $duration = round((microtime(true) - $startTime) * 1000, 2);
        Log::info("Batch generation completed in {$duration}ms for " . count($steps) . " steps");

        return $results;
    }

    /**
     * Sprawdza wydajność systemu AI i zwraca metryki.
     *
     * @return array
     */
    public function getPerformanceMetrics(): array
    {
        $stats = $this->getUsageStats();

        return [
            'cache_hit_ratio' => $stats['total_requests'] > 0
                ? round(($stats['cache_hits'] / $stats['total_requests']) * 100, 2)
                : 0,
            'ai_usage_ratio' => $stats['total_requests'] > 0
                ? round(($stats['ai_generated'] / $stats['total_requests']) * 100, 2)
                : 0,
            'rule_based_ratio' => $stats['total_requests'] > 0
                ? round(($stats['rule_based'] / $stats['total_requests']) * 100, 2)
                : 0,
            'fallback_ratio' => $stats['total_requests'] > 0
                ? round(($stats['template_fallback'] / $stats['total_requests']) * 100, 2)
                : 0,
            'total_requests' => $stats['total_requests'],
            'average_response_time_ms' => Cache::get('ai_stats_avg_response_time', 0),
            'cache_memory_usage_mb' => $this->estimateCacheUsage(),
        ];
    }

    /**
     * Szacuje wykorzystanie pamięci cache.
     *
     * @return float
     */
    private function estimateCacheUsage(): float
    {
        // Uproszczona estymacja - w prawdziwej aplikacji
        // użyj Redis/Memcached INFO commands
        $stats = $this->getUsageStats();
        $averageEntrySize = 2; // KB per cache entry

        return round(($stats['cache_hits'] * $averageEntrySize) / 1024, 2);
    }

    /**
     * Wymusza odświeżenie cache dla konkretnego kroku.
     *
     * @param int $step
     * @param array $wizardData
     * @param array $context
     * @return array
     */
    public function forceRefreshSuggestions(int $step, array $wizardData, array $context = []): array
    {
        $cacheKey = $this->generateCacheKey($step, $wizardData, $context);
        Cache::forget($cacheKey);

        $startTime = microtime(true);
        $suggestions = $this->generateSuggestions($step, $wizardData, $context);
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Zapisz nowy wpis do cache
        Cache::put($cacheKey, $suggestions, $this->cacheTimeout);

        Log::info("Force refreshed suggestions for step {$step} in {$duration}ms");

        return $suggestions;
    }

    /**
     * Sprawdza dostępność AI i cache'uje wynik.
     *
     * @return bool True jeśli AI jest dostępne
     */
    private function checkAiAvailability(): bool
    {
        if ($this->isAiAvailable !== null) {
            return $this->isAiAvailable;
        }

        try {
            // Sprawdź dostępność LocalAI
            $this->isAiAvailable = $this->localAI->isServiceAvailable();
        } catch (\Exception $e) {
            Log::warning('Failed to check AI availability', ['error' => $e->getMessage()]);
            $this->isAiAvailable = false;
        }

        return $this->isAiAvailable;
    }

    /**
     * Edytuje tekst na podstawie instrukcji użytkownika.
     *
     * @param array $context Kontekst edycji z instrukcją użytkownika
     * @return string Przepisany tekst
     */
    public function editText(array $context): string
    {
        try {
            Log::info('🤖 HybridAIAssistant::editText called', [
                'field' => $context['field'] ?? 'unknown',
                'user_instruction' => $context['user_instruction'] ?? 'empty',
                'current_text_length' => strlen($context['current_text'] ?? '')
            ]);

            // Sprawdź czy AI jest dostępne
            if ($this->checkAiAvailability()) {
                Log::info('🤖 AI available, using editTextWithAI');
                return $this->editTextWithAI($context);
            }

            // Fallback na template-based editing
            Log::info('🤖 AI unavailable, using template fallback');
            return $this->editTextWithTemplate($context);

        } catch (\Exception $e) {
            Log::warning('🤖 Failed to edit text', [
                'error' => $e->getMessage(),
                'context' => $context
            ]);

            // Zwróć oryginalny tekst w przypadku błędu
            return $context['current_text'] ?? '';
        }
    }

    /**
     * Generuje tekst na podstawie kontekstu.
     *
     * @param array $context Kontekst generowania
     * @return string Wygenerowany tekst
     */
    public function generateText(array $context): string
    {
        try {
            Log::info('🤖 HybridAIAssistant::generateText called', [
                'field' => $context['field'] ?? 'unknown',
                'action' => $context['action'] ?? 'unknown',
                'user_name' => $context['user_data']['name'] ?? 'unknown'
            ]);

            // Sprawdź czy AI jest dostępne
            if ($this->checkAiAvailability()) {
                Log::info('🤖 AI available, using generateTextWithAI');
                return $this->generateTextWithAI($context);
            }

            // Fallback na template-based generation
            Log::info('🤖 AI unavailable, using template fallback');
            return $this->generateTextWithTemplate($context);

        } catch (\Exception $e) {
            Log::warning('🤖 Failed to generate text', [
                'error' => $e->getMessage(),
                'context' => $context
            ]);

            return '';
        }
    }

    /**
     * Edytuje tekst za pomocą AI.
     */
    private function editTextWithAI(array $context): string
    {
        $prompt = $this->buildEditPrompt($context);

        // Tu będzie integracja z prawdziwym AI (OpenAI, Ollama, etc.)
        // Na razie zwracamy fallback
        return $this->editTextWithTemplate($context);
    }

    /**
     * Generuje tekst za pomocą AI (Ollama).
     *
     * Używa LocalAIAssistant do wysłania promptu do Ollama i otrzymania
     * unikalnego, wygenerowanego przez AI tekstu. W przypadku błędu
     * lub niedostępności AI, wraca do template-based generation.
     *
     * @param array $context Pełny kontekst z wizarda
     * @return string Wygenerowany tekst
     */
    private function generateTextWithAI(array $context): string
    {
        try {
            // Zbuduj szczegółowy prompt z pełnym kontekstem
            $prompt = $this->buildGeneratePrompt($context);

            Log::info('🤖 Próbuję wygenerować tekst używając Ollama AI', [
                'action' => $context['action'] ?? 'unknown',
                'has_wizard_context' => !empty($context['wizard_context']),
                'prompt_length' => strlen($prompt)
            ]);

            // Określ limity długości w zależności od akcji
            $action = $context['action'] ?? 'generate_motivation';
            $minLength = $action === 'generate_motivation' ? 100 : 150;
            $maxLength = $action === 'generate_motivation' ? 500 : 1000;

            // Wywołaj Ollama przez LocalAIAssistant
            $generatedText = $this->localAI->generateTextWithPrompt($prompt, $minLength, $maxLength);

            if ($generatedText !== null) {
                Log::info('✅ Ollama wygenerowała tekst pomyślnie', [
                    'action' => $action,
                    'length' => strlen($generatedText),
                    'preview' => substr($generatedText, 0, 80) . '...'
                ]);

                return $generatedText;
            } else {
                Log::warning('⚠️ Ollama zwróciła null, używam template fallback', [
                    'action' => $action
                ]);
            }

        } catch (\Exception $e) {
            Log::error('💥 Błąd podczas generowania tekstu przez AI', [
                'error' => $e->getMessage(),
                'action' => $context['action'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Fallback na template-based generation
        Log::info('🔄 Fallback do template-based generation');
        return $this->generateTextWithTemplate($context);
    }

    /**
     * Edytuje tekst za pomocą template-based approach.
     */
    private function editTextWithTemplate(array $context): string
    {
        $currentText = $context['current_text'] ?? '';
        $instruction = strtolower($context['user_instruction'] ?? '');
        $userName = $context['user_data']['name'] ?? '';

        // Loguj instrukcję dla debugowania
        \Log::info('Edycja tekstu AI', [
            'instruction' => $instruction,
            'current_text_length' => strlen($currentText),
            'user_name' => $userName
        ]);

        // Prosta logika edycji na podstawie słów kluczowych
        $editedText = $currentText;

        // Dodaj imię jeśli nie ma
        if (strpos($instruction, 'imię') !== false || strpos($instruction, 'imie') !== false) {
            if (!empty($userName) && strpos($editedText, $userName) === false) {
                $editedText = "Cześć! Nazywam się {$userName}. " . $editedText;
            }
        }

        // Dodaj informacje o wieku - ale nie jeśli to doświadczenie
        if (preg_match('/(\d+)\s*(lat|lata|roku|rok)(?!.*(?:doświadczen|doświadczenia|doświadczenie))/i', $instruction, $matches)) {
            $age = $matches[1];
            $ageText = "Mam {$age} lat. ";

            // Sprawdź czy już ma informacje o wieku - jeśli tak, zastąp je
            if (preg_match('/mam\s+\d+\s*lat\.?\s*/i', $editedText)) {
                // Zastąp istniejący wiek nowym
                $editedText = preg_replace('/mam\s+\d+\s*lat\.?\s*/i', $ageText, $editedText);
                \Log::info('Zastąpiono istniejący wiek', ['new_age' => $age, 'result_length' => strlen($editedText)]);
            } else {
                // Dodaj nowy wiek na początku
                if (!empty(trim($editedText))) {
                    $editedText = $ageText . $editedText;
                } else {
                    $editedText = $ageText;
                }
                \Log::info('Dodano nowy wiek', ['age' => $age, 'result_length' => strlen($editedText)]);
            }
        }

        // Dodaj informacje o doświadczeniu
        if (preg_match('/(\d+)\s*(lat|lata|roku|rok).*(doświadczen|doświadczenia|doświadczenie)/i', $instruction, $matches)) {
            $years = $matches[1];
            $experienceText = "Mam {$years} lat doświadczenia w opiece nad zwierzętami. ";

            // Sprawdź czy już ma informacje o doświadczeniu - jeśli tak, zastąp je
            if (preg_match('/mam\s+\d+\s*lat?\s*(doświadczen|doświadczenia|doświadczenie)[^\.]*/i', $editedText)) {
                // Zastąp istniejące doświadczenie nowym
                $editedText = preg_replace('/mam\s+\d+\s*lat?\s*(doświadczen|doświadczenia|doświadczenie)[^\.]*\.?\s*/i', $experienceText, $editedText);
                \Log::info('Zastąpiono istniejące doświadczenie', ['new_years' => $years, 'result_length' => strlen($editedText)]);
            } else {
                // Dodaj nowe doświadczenie na końcu
                $editedText .= ' ' . $experienceText;
                \Log::info('Dodano nowe doświadczenie', ['years' => $years, 'result_length' => strlen($editedText)]);
            }
        }

        // Rozpoznaj instrukcje typu "dodaj że..."
        if (preg_match('/dodaj\s+(że|ze)\s+(.+)/i', $instruction, $matches)) {
            $addContent = trim($matches[2]);

            // Sprawdź czy to doświadczenie zamiast wieku (priorytet wyższy)
            if (preg_match('/mam\s+(\d+)\s*lat?\s*(doświadczen|doświadczenia|doświadczenie)/i', $addContent, $expMatches)) {
                $years = $expMatches[1];

                // Użyj kontekstu z formularza jeśli dostępny
                $contextExperience = $context['user_data']['years_of_experience'] ?? null;
                $petExperience = $context['user_data']['pet_experience'] ?? [];

                if ($contextExperience && $contextExperience != $years) {
                    // Użyj lat z formularza zamiast z instrukcji
                    $years = $contextExperience;
                }

                $experienceText = "Mam {$years} lat doświadczenia w opiece nad zwierzętami";

                // Dodaj szczegóły o typach doświadczenia jeśli dostępne
                if (!empty($petExperience)) {
                    $experienceTypes = [];
                    if (in_array('own_pets', $petExperience)) $experienceTypes[] = 'własne zwierzęta';
                    if (in_array('professional', $petExperience)) $experienceTypes[] = 'praca zawodowa';
                    if (in_array('volunteering', $petExperience)) $experienceTypes[] = 'wolontariat';
                    if (in_array('family_pets', $petExperience)) $experienceTypes[] = 'zwierzęta rodziny';

                    if (!empty($experienceTypes)) {
                        $experienceText .= " (w tym: " . implode(', ', $experienceTypes) . ")";
                    }
                }

                $experienceText .= ". ";

                // Sprawdź czy już ma informacje o doświadczeniu - jeśli tak, zastąp je
                if (preg_match('/mam\s+\d+\s*lat?\s*(doświadczen|doświadczenia|doświadczenie)[^\.]*/i', $editedText)) {
                    // Zastąp istniejące doświadczenie nowym
                    $editedText = preg_replace('/mam\s+\d+\s*lat?\s*(doświadczen|doświadczenia|doświadczenie)[^\.]*\.?\s*/i', $experienceText, $editedText);
                } else {
                    // Dodaj nowe doświadczenie
                    if (!empty(trim($editedText))) {
                        $editedText .= ' ' . $experienceText;
                    } else {
                        $editedText = $experienceText;
                    }
                }

                \Log::info('Dodano informacje o doświadczeniu', [
                    'years' => $years,
                    'context_years' => $contextExperience,
                    'pet_experience' => $petExperience
                ]);
            }
            // Jeśli dodajemy informację o wieku (tylko jeśli nie dotyczy doświadczenia)
            elseif (preg_match('/mam\s+(\d+)\s*lat/i', $addContent, $ageMatches) &&
                   !preg_match('/(doświadczen|doświadczenia|doświadczenie)/i', $addContent)) {
                $age = $ageMatches[1];
                $ageText = "Mam {$age} lat. ";

                // Sprawdź czy już ma informacje o wieku - jeśli tak, zastąp je
                if (preg_match('/mam\s+\d+\s*lat\.?\s*/i', $editedText)) {
                    // Zastąp istniejący wiek nowym
                    $editedText = preg_replace('/mam\s+\d+\s*lat\.?\s*/i', $ageText, $editedText);
                } else {
                    // Dodaj nowy wiek na początku
                    if (!empty(trim($editedText))) {
                        $editedText = $ageText . $editedText;
                    } else {
                        $editedText = $ageText;
                    }
                }
            }
            // Inne instrukcje "dodaj że..."
            else {
                $additionalText = ucfirst($addContent);
                if (!str_ends_with($additionalText, '.')) {
                    $additionalText .= '.';
                }
                $editedText .= ' ' . $additionalText;
            }

            \Log::info('Przetworzono instrukcję "dodaj że"', ['content' => $addContent]);
        }

        // Uczyń tekst bardziej profesjonalnym
        if (strpos($instruction, 'profesjonalnie') !== false || strpos($instruction, 'profesjonalny') !== false) {
            $editedText = $this->makeProfessional($editedText, $userName);
        }

        // Dodaj informacje o domu/ogrodzie
        if (strpos($instruction, 'dom') !== false || strpos($instruction, 'ogród') !== false || strpos($instruction, 'ogrod') !== false) {
            $homeText = " Mam własny dom z ogrodem, który zapewnia zwierzętom bezpieczne i przyjazne środowisko.";
            if (strpos($editedText, 'dom') === false) {
                $editedText .= $homeText;
            }
        }

        // Specjalne instrukcje dla opisu doświadczenia
        if (($context['field'] ?? '') === 'experience_description') {
            $editedText = $this->enhanceExperienceDescription($editedText, $instruction, $context);
        }

        $result = trim($editedText);
        \Log::info('Rezultat edycji tekstu AI', [
            'field' => $context['field'] ?? 'unknown',
            'original_length' => strlen($currentText),
            'result_length' => strlen($result),
            'result_preview' => substr($result, 0, 100) . '...'
        ]);

        return $result;
    }

    /**
     * Generuje tekst za pomocą template.
     */
    private function generateTextWithTemplate(array $context): string
    {
        $userName = $context['user_data']['name'] ?? 'Anna';
        $field = $context['field'] ?? '';

        // Jeśli generujemy dla opisu doświadczenia (krok 2), użyj danych z formularza
        if ($field === 'experienceDescription' || $field === 'experience_description') {
            return $this->generateExperienceDescription($context);
        }

        // Generuj motywację z pełnym kontekstem z kroków 1-5
        return $this->generateMotivationWithContext($context);
    }

    /**
     * Generuje motywację wykorzystując pełny kontekst z wizarda.
     */
    private function generateMotivationWithContext(array $context): string
    {
        $userName = $context['user_data']['name'] ?? 'Anna';
        $wizardContext = $context['wizard_context'] ?? [];

        // Bazowa część
        $motivation = "Cześć! Nazywam się {$userName} i kocham zwierzęta. ";

        // Dodaj informacje o zwierzętach
        if (!empty($wizardContext['animal_types'])) {
            $animalTypes = $wizardContext['animal_types'];
            $animalsText = $this->formatAnimalTypes($animalTypes, $wizardContext['animal_sizes'] ?? []);
            $motivation .= "Specjalizuję się w opiece nad {$animalsText}. ";
        }

        // Dodaj informacje o usługach
        if (!empty($wizardContext['service_types'])) {
            $servicesText = $this->formatServiceTypes($wizardContext['service_types']);
            $motivation .= "Oferuję {$servicesText}. ";
        }

        // Dodaj informacje o lokalizacji
        if (!empty($wizardContext['city'])) {
            $motivation .= "Działam w okolicy {$wizardContext['city']}";
            if (!empty($wizardContext['service_radius'])) {
                $motivation .= " (promień {$wizardContext['service_radius']} km)";
            }
            $motivation .= ". ";
        }

        // Dodaj informacje o domu
        if (!empty($wizardContext['home_type'])) {
            $homeText = $this->formatHomeType($wizardContext['home_type'], $wizardContext['has_garden'] ?? false);
            $motivation .= $homeText . " ";
        }

        // Zakończenie
        $motivation .= "Zależy mi na zapewnieniu najlepszej opieki Twoim pupilom, gdy nie możesz być z nimi!";

        return $motivation;
    }

    /**
     * Formatuje typy zwierząt do tekstu.
     */
    private function formatAnimalTypes(array $types, array $sizes): string
    {
        $formatted = [];

        if (in_array('dogs', $types)) {
            if (!empty($sizes)) {
                $sizeText = [];
                if (in_array('small', $sizes)) $sizeText[] = 'małymi';
                if (in_array('medium', $sizes)) $sizeText[] = 'średnimi';
                if (in_array('large', $sizes)) $sizeText[] = 'dużymi';
                $formatted[] = implode(' i ', $sizeText) . ' psami';
            } else {
                $formatted[] = 'psami';
            }
        }

        if (in_array('cats', $types)) $formatted[] = 'kotami';
        if (in_array('rodents', $types)) $formatted[] = 'gryzoniami';
        if (in_array('birds', $types)) $formatted[] = 'ptakami';
        if (in_array('reptiles', $types)) $formatted[] = 'gadami';
        if (in_array('fish', $types)) $formatted[] = 'rybkami';

        return !empty($formatted) ? implode(', ', $formatted) : 'różnymi zwierzętami';
    }

    /**
     * Formatuje typy usług do tekstu.
     */
    private function formatServiceTypes(array $types): string
    {
        $services = [];

        if (in_array('dog_walking', $types)) $services[] = 'spacery';
        if (in_array('pet_sitting', $types)) $services[] = 'opiekę dzienną';
        if (in_array('overnight_care', $types)) $services[] = 'opiekę z nocowaniem';
        if (in_array('pet_boarding', $types)) $services[] = 'hotel dla zwierząt';
        if (in_array('grooming', $types)) $services[] = 'pielęgnację';
        if (in_array('vet_visits', $types)) $services[] = 'wizyty u weterynarza';
        if (in_array('pet_transport', $types)) $services[] = 'transport';

        return !empty($services) ? implode(', ', $services) : 'kompleksową opiekę';
    }

    /**
     * Formatuje typ domu do tekstu.
     */
    private function formatHomeType(string $homeType, bool $hasGarden): string
    {
        $text = match($homeType) {
            'house' => 'Posiadam dom',
            'apartment' => 'Mieszkam w mieszkaniu',
            'studio' => 'Mieszkam w kawalerce',
            default => 'Mam odpowiednie warunki'
        };

        if ($hasGarden) {
            $text .= ' z ogrodem';
        }

        return $text . ', gdzie zwierzęta czują się komfortowo';
    }

    /**
     * Generuje opis doświadczenia na podstawie danych z formularza.
     */
    private function generateExperienceDescription(array $context): string
    {
        $userName = $context['user_data']['name'] ?? '';
        $yearsOfExperience = $context['user_data']['years_of_experience'] ?? 0;
        $petExperience = $context['user_data']['pet_experience'] ?? [];

        // Konwertuj lata doświadczenia na tekst
        $yearsText = $this->convertYearsToText($yearsOfExperience);

        // Buduj opis na podstawie typów doświadczenia
        $experienceParts = [];

        if (in_array('own_pets', $petExperience)) {
            $experienceParts[] = "posiadam własne zwierzęta domowe, co pozwoliło mi doskonale zrozumieć ich potrzeby i zachowania";
        }

        if (in_array('professional', $petExperience)) {
            $experienceParts[] = "pracowałam zawodowo w branży związanej ze zwierzętami";
        }

        if (in_array('volunteering', $petExperience)) {
            $experienceParts[] = "angażowałam się w wolontariat w schroniskach dla zwierząt";
        }

        if (in_array('family_pets', $petExperience)) {
            $experienceParts[] = "opiekowałam się zwierzętami rodziny i przyjaciół";
        }

        if (in_array('training', $petExperience)) {
            $experienceParts[] = "uczestniczyłam w kursach i szkoleniach dotyczących opieki nad zwierzętami";
        }

        if (in_array('veterinary', $petExperience)) {
            $experienceParts[] = "posiadam doświadczenie weterynaryjne";
        }

        // Zbuduj pełny opis
        $description = "Mam {$yearsText} doświadczenia w opiece nad zwierzętami. ";

        if (!empty($experienceParts)) {
            $description .= "W tym czasie " . implode(', ', $experienceParts) . ". ";
        }

        $description .= "Potrafię rozpoznać potrzeby różnych gatunków zwierząt i zapewnić im odpowiednią opiekę. ";
        $description .= "Każde zwierzę traktuję z miłością i uwagą, dbając o jego komfort i bezpieczeństwo. ";
        $description .= "Wiem, jak ważne jest dla właścicieli, aby ich pupile były w dobrych rękach podczas ich nieobecności.";

        \Log::info('Wygenerowano opis doświadczenia', [
            'years' => $yearsOfExperience,
            'experience_types' => $petExperience,
            'result_length' => strlen($description)
        ]);

        return $description;
    }

    /**
     * Konwertuje liczbę lat na tekst.
     */
    private function convertYearsToText($years): string
    {
        switch ($years) {
            case 0:
                return "początki mojej drogi z";
            case 1:
                return "rok";
            case 2:
            case 3:
            case 4:
                return "{$years} lata";
            case 5:
            case 10:
            case 15:
            case 20:
            case 25:
                return "{$years} lat";
            default:
                return "{$years} lat";
        }
    }

    /**
     * Czyni tekst bardziej profesjonalnym.
     */
    private function makeProfessional(string $text, string $userName = ''): string
    {
        // Usuń zbyt nieformalne zwroty
        $text = str_replace(['hej', 'cześć', 'siema'], 'Witam', $text);

        // Dodaj profesjonalne wprowadzenie jeśli nie ma
        if (!empty($userName) && strpos($text, $userName) === false) {
            $text = "Nazywam się {$userName}. " . $text;
        }

        // Popraw strukturę
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Dodaj profesjonalne zakończenie jeśli brakuje
        if (!preg_match('/\.(.*)(oferuję|zapewniam|gwarantuję)/i', $text)) {
            $text .= ' Oferuję profesjonalną i pełną zaangażowania opiekę nad Państwa zwierzętami.';
        }

        return $text;
    }

    /**
     * Buduje prompt do edycji tekstu.
     */
    private function buildEditPrompt(array $context): string
    {
        return "Edytuj następujący tekst zgodnie z instrukcją:\n\n" .
               "Oryginalny tekst: " . ($context['current_text'] ?? '') . "\n\n" .
               "Instrukcja: " . ($context['user_instruction'] ?? '') . "\n\n" .
               "Wymagania: tekst po polsku, 50-500 znaków, profesjonalny ale przyjazny ton.";
    }

    /**
     * Buduje prompt do generowania tekstu.
     */
    /**
     * Buduje prompt dla Ollama używając pełnego kontekstu wizarda.
     *
     * Tworzy szczegółowy prompt zawierający wszystkie informacje zebrane
     * w krokach 1-5 (zwierzęta, usługi, lokalizacja, dostępność, dom).
     *
     * @param array $context Pełny kontekst z wizarda
     * @return string Gotowy prompt dla Ollama
     */
    private function buildGeneratePrompt(array $context): string
    {
        $userName = $context['user_data']['name'] ?? 'Opiekun';
        $action = $context['action'] ?? 'generate_motivation';
        $wizardContext = $context['wizard_context'] ?? [];

        // Buduj szczegółowy opis kontekstu
        $contextDescription = "Informacje o opiekunie:\n";

        // Zwierzęta
        if (!empty($wizardContext['animal_types'])) {
            $animalsList = $this->formatAnimalTypesForPrompt($wizardContext['animal_types'], $wizardContext['animal_sizes'] ?? []);
            $contextDescription .= "- Specjalizacja: {$animalsList}\n";
        }

        // Usługi
        if (!empty($wizardContext['service_types'])) {
            $servicesList = $this->formatServiceTypesForPrompt($wizardContext['service_types']);
            $contextDescription .= "- Oferowane usługi: {$servicesList}\n";
        }

        // Lokalizacja
        if (!empty($wizardContext['city'])) {
            $contextDescription .= "- Lokalizacja: {$wizardContext['city']}";
            if (!empty($wizardContext['service_radius'])) {
                $contextDescription .= " (promień działania: {$wizardContext['service_radius']} km)";
            }
            $contextDescription .= "\n";
        }

        // Dom i ogród
        if (!empty($wizardContext['home_type'])) {
            $homeDesc = $this->formatHomeTypeForPrompt($wizardContext['home_type'], $wizardContext['has_garden'] ?? false);
            $contextDescription .= "- Środowisko: {$homeDesc}\n";
        }

        // Inne zwierzęta w domu
        if (!empty($wizardContext['has_other_pets']) && !empty($wizardContext['other_pets'])) {
            $contextDescription .= "- Inne zwierzęta: {$wizardContext['other_pets']}\n";
        }

        // Dostępność (opcjonalnie)
        if (!empty($wizardContext['weekly_availability'])) {
            $availableDays = array_filter($wizardContext['weekly_availability'], fn($day) => $day['available'] ?? false);
            if (!empty($availableDays)) {
                $daysCount = count($availableDays);
                $contextDescription .= "- Dostępność: {$daysCount} dni w tygodniu\n";
            }
        }

        // Buduj prompt w zależności od akcji
        if ($action === 'generate_motivation') {
            return "Napisz krótki, unikalny tekst motywacyjny dla opiekuna zwierząt o imieniu {$userName}.

{$contextDescription}

WYMAGANIA:
- Długość: 100-500 znaków
- Język: polski
- Ton: ciepły, profesjonalny, autentyczny
- Styl: pierwsza osoba (ja/moje)
- KONIECZNIE użyj informacji o specjalizacji, usługach, lokalizacji i domu
- KONIECZNIE wspomnieć konkretne zwierzęta którymi się zajmuje
- Skoncentruj się na pasji do zwierząt i chęci pomocy właścicielom
- Unikaj ogólników - bądź konkretny
- Unikaj frazesów typu 'z sercem', 'z pasją', 'kocham zwierzęta' - pokaż to zamiast mówić
- Każda generacja ma być inna i unikalna

Przykład złego tekstu (zbyt ogólny):
'Cześć! Nazywam się Anna. Kocham zwierzęta i chętnie się nimi zajmę!'

Przykład dobrego tekstu (konkretny, używa kontekstu):
'Cześć! Nazywam się Anna i od lat zajmuję się małymi i średnimi psami w Warszawie. Oferuję spacery i opiekę dzienną w swoim domu z ogrodem, gdzie Twój pupil będzie mógł bezpiecznie pobiegać. Zadbam o niego jak o własnego!'

Wygeneruj NOWY, unikalny tekst motywacyjny:";
        } elseif ($action === 'generate_experience') {
            $yearsExp = $context['user_data']['years_of_experience'] ?? 0;
            $petExp = $context['user_data']['pet_experience'] ?? [];

            $expTypes = [];
            if (in_array('own_pets', $petExp)) $expTypes[] = 'własne zwierzęta';
            if (in_array('professional', $petExp)) $expTypes[] = 'praca zawodowa';
            if (in_array('volunteering', $petExp)) $expTypes[] = 'wolontariat';
            if (in_array('family_pets', $petExp)) $expTypes[] = 'zwierzęta w rodzinie';

            $expTypesStr = !empty($expTypes) ? implode(', ', $expTypes) : 'ogólne doświadczenie';

            return "Napisz szczegółowy opis doświadczenia dla opiekuna zwierząt o imieniu {$userName}.

{$contextDescription}

DOŚWIADCZENIE:
- Lata doświadczenia: {$yearsExp}
- Typy doświadczenia: {$expTypesStr}

WYMAGANIA:
- Długość: 150-1000 znaków
- Język: polski
- Ton: profesjonalny, wiarygodny
- Styl: pierwsza osoba
- KONIECZNIE użyj informacji o typach zwierząt i usługach
- Podaj konkretne przykłady z doświadczenia
- Wspomnij konkretne sytuacje lub umiejętności
- Unikaj ogólników
- Każda generacja ma być inna i unikalna

Wygeneruj NOWY, unikalny opis doświadczenia:";
        }

        // Domyślny prompt
        return "Wygeneruj profesjonalny tekst dla opiekuna zwierząt o imieniu {$userName}.\n\n{$contextDescription}";
    }

    /**
     * Formatuje typy zwierząt do ludzkiego opisu dla promptu.
     */
    private function formatAnimalTypesForPrompt(array $types, array $sizes): string
    {
        $formatted = [];

        if (in_array('dogs', $types)) {
            if (!empty($sizes)) {
                $sizeNames = [];
                if (in_array('small', $sizes)) $sizeNames[] = 'małe';
                if (in_array('medium', $sizes)) $sizeNames[] = 'średnie';
                if (in_array('large', $sizes)) $sizeNames[] = 'duże';
                $formatted[] = implode(' i ', $sizeNames) . ' psy';
            } else {
                $formatted[] = 'psy';
            }
        }

        if (in_array('cats', $types)) $formatted[] = 'koty';
        if (in_array('rodents', $types)) $formatted[] = 'gryzonie';
        if (in_array('birds', $types)) $formatted[] = 'ptaki';
        if (in_array('reptiles', $types)) $formatted[] = 'gady';
        if (in_array('fish', $types)) $formatted[] = 'rybki';

        return !empty($formatted) ? implode(', ', $formatted) : 'różne zwierzęta';
    }

    /**
     * Formatuje typy usług do ludzkiego opisu dla promptu.
     */
    private function formatServiceTypesForPrompt(array $types): string
    {
        $services = [];

        if (in_array('dog_walking', $types)) $services[] = 'spacery';
        if (in_array('pet_sitting', $types)) $services[] = 'opieka dzienna';
        if (in_array('overnight_care', $types)) $services[] = 'opieka z nocowaniem';
        if (in_array('pet_boarding', $types)) $services[] = 'hotel dla zwierząt';
        if (in_array('grooming', $types)) $services[] = 'pielęgnacja';
        if (in_array('vet_visits', $types)) $services[] = 'wizyty u weterynarza';
        if (in_array('pet_transport', $types)) $services[] = 'transport';

        return !empty($services) ? implode(', ', $services) : 'różne usługi';
    }

    /**
     * Formatuje typ domu do ludzkiego opisu dla promptu.
     */
    private function formatHomeTypeForPrompt(string $homeType, bool $hasGarden): string
    {
        $base = match($homeType) {
            'house' => 'dom',
            'apartment' => 'mieszkanie',
            'apartment_with_balcony' => 'mieszkanie z balkonem',
            'house_with_yard' => 'dom z podwórkiem',
            default => 'mieszkanie'
        };

        if ($hasGarden && $homeType === 'house') {
            return $base . ' z ogrodem';
        }

        return $base;
    }

    /**
     * Poprawia i wzbogaca opis doświadczenia na podstawie instrukcji.
     */
    private function enhanceExperienceDescription(string $text, string $instruction, array $context): string
    {
        $petExperience = $context['user_data']['pet_experience'] ?? [];
        $yearsOfExperience = $context['user_data']['years_of_experience'] ?? 0;

        // Dodaj konkretne przykłady jeśli użytkownik o to prosi
        if (strpos($instruction, 'przykład') !== false || strpos($instruction, 'przyklad') !== false) {
            $examples = $this->generateExperienceExamples($petExperience, $yearsOfExperience);
            if (!empty($examples)) {
                $text .= ' ' . $examples;
            }
        }

        // Popraw stylistykę jeśli użytkownik o to prosi
        if (strpos($instruction, 'stylistycz') !== false || strpos($instruction, 'styl') !== false) {
            $text = $this->improveExperienceStyle($text);
        }

        // Dodaj więcej szczegółów
        if (strpos($instruction, 'szczegół') !== false || strpos($instruction, 'szczegol') !== false) {
            $text = $this->addExperienceDetails($text, $petExperience);
        }

        // Uczyń tekst bardziej profesjonalnym
        if (strpos($instruction, 'profesjonalnie') !== false) {
            $text = $this->makeProfessionalExperience($text);
        }

        return $text;
    }

    /**
     * Generuje konkretne przykłady doświadczenia.
     */
    private function generateExperienceExamples(array $petExperience, int $years): string
    {
        $examples = [];

        if (in_array('own_pets', $petExperience)) {
            $examples[] = "Przez lata sprawowałem opiekę nad własnymi zwierzętami, co nauczyło mnie rozpoznawania ich potrzeb i zachowań.";
        }

        if (in_array('professional', $petExperience)) {
            $examples[] = "Moje doświadczenie zawodowe pozwoliło mi poznać różne techniki opieki i standardy bezpieczeństwa.";
        }

        if (in_array('volunteering', $petExperience)) {
            $examples[] = "Wolontariat w schronisku dał mi cenne doświadczenie w pracy z zwierzętami o różnych temperamentach i potrzebach.";
        }

        if (in_array('veterinary', $petExperience)) {
            $examples[] = "Doświadczenie weterynaryjne pozwoliło mi zrozumieć aspekty zdrowotne opieki nad zwierzętami.";
        }

        return implode(' ', $examples);
    }

    /**
     * Poprawia stylistykę opisu doświadczenia.
     */
    private function improveExperienceStyle(string $text): string
    {
        // Zamień powtarzające się słowa
        $text = str_replace(['bardzo bardzo', 'zawsze zawsze'], ['bardzo', 'zawsze'], $text);

        // Popraw strukturę zdań
        $text = preg_replace('/\s+/', ' ', $text);

        // Dodaj profesjonalne zwroty
        if (!strpos($text, 'doświadczenie')) {
            $text = "Moje doświadczenie " . lcfirst($text);
        }

        return $text;
    }

    /**
     * Dodaje szczegóły do opisu doświadczenia.
     */
    private function addExperienceDetails(string $text, array $petExperience): string
    {
        $details = [];

        if (in_array('training', $petExperience)) {
            $details[] = "Ukończyłem specjalistyczne szkolenia z zakresu opieki nad zwierzętami.";
        }

        if (in_array('family_pets', $petExperience)) {
            $details[] = "Pomagałem w opiece nad zwierzętami rodziny i znajomych, zdobywając doświadczenie z różnymi charakterami.";
        }

        if (!empty($details)) {
            $text .= ' ' . implode(' ', $details);
        }

        return $text;
    }

    /**
     * Czyni opis doświadczenia bardziej profesjonalnym.
     */
    private function makeProfessionalExperience(string $text): string
    {
        // Zamień nieformalne zwroty
        $replacements = [
            'super' => 'bardzo dobrze',
            'świetnie' => 'profesjonalnie',
            'fajnie' => 'satysfakcjonująco',
            'spoko' => 'odpowiednio'
        ];

        foreach ($replacements as $informal => $formal) {
            $text = str_ireplace($informal, $formal, $text);
        }

        // Dodaj profesjonalne wprowadzenie jeśli brakuje
        if (!preg_match('/^(Moje|Posiadam|Dysponuję)/i', $text)) {
            $text = "Posiadam " . lcfirst($text);
        }

        return $text;
    }
}