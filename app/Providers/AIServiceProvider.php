<?php

namespace App\Providers;

use App\Services\AI\HybridAIAssistant;
use App\Services\AI\LocalAIAssistant;
use App\Services\AI\MarketDataService;
use App\Services\AI\RuleEngine;
use App\Services\AI\TemplateSystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

/**
 * Service Provider dla systemu AI Assistant.
 *
 * Rejestruje wszystkie serwisy AI w kontenerze dependency injection
 * i konfiguruje ich zależności zgodnie z zasadami hybrydowego AI.
 *
 * @package App\Providers
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class AIServiceProvider extends ServiceProvider
{
    /**
     * Rejestruje serwisy AI w kontenerze aplikacji.
     *
     * Konfiguruje dependency injection dla wszystkich komponentów
     * systemu AI Assistant w odpowiedniej kolejności.
     *
     * @return void
     */
    public function register(): void
    {
        // Rejestracja podstawowych serwisów AI
        $this->registerMarketDataService();
        $this->registerTemplateSystem();
        $this->registerLocalAIAssistant();
        $this->registerRuleEngine();
        $this->registerHybridAIAssistant();

        Log::info('AI Services zostały zarejestrowane w Service Container');
    }

    /**
     * Bootstraps serwisów AI po załadowaniu wszystkich providerów.
     *
     * Wykonuje inicjalizację, walidację konfiguracji i sprawdza
     * dostępność zewnętrznych serwisów AI.
     *
     * @return void
     */
    public function boot(): void
    {
        // Sprawdź czy AI jest włączone w konfiguracji
        if (!config('ai.enabled', true)) {
            Log::info('AI Assistant został wyłączony w konfiguracji');
            return;
        }

        // Walidacja konfiguracji AI
        $this->validateAIConfiguration();

        // Sprawdź dostępność Ollama (jeśli włączone)
        if (config('ai.ollama.enabled', true)) {
            $this->checkOllamaAvailability();
        }

        Log::info('AI Assistant System został pomyślnie zainicjalizowany');
    }

    /**
     * Rejestruje MarketDataService w kontenerze.
     *
     * Serwis analizy danych rynkowych dla sugestii cenowych
     * i insights o konkurencji lokalnej.
     *
     * @return void
     */
    protected function registerMarketDataService(): void
    {
        $this->app->singleton(MarketDataService::class, function ($app) {
            return new MarketDataService();
        });

        // Alias dla łatwiejszego dostępu
        $this->app->alias(MarketDataService::class, 'ai.market_data');
    }

    /**
     * Rejestruje TemplateSystem w kontenerze.
     *
     * System szablonów używany jako fallback gdy AI nie jest
     * dostępne lub zawodzi.
     *
     * @return void
     */
    protected function registerTemplateSystem(): void
    {
        $this->app->singleton(TemplateSystem::class, function ($app) {
            return new TemplateSystem();
        });

        // Alias dla łatwiejszego dostępu
        $this->app->alias(TemplateSystem::class, 'ai.templates');
    }

    /**
     * Rejestruje LocalAIAssistant w kontenerze.
     *
     * Serwis integracji z lokalnym AI (Ollama) dla kreatywnych
     * zadań jak generowanie bio i personalizowanych sugestii.
     *
     * @return void
     */
    protected function registerLocalAIAssistant(): void
    {
        $this->app->singleton(LocalAIAssistant::class, function ($app) {
            return new LocalAIAssistant(
                $app->make(TemplateSystem::class)
            );
        });

        // Alias dla łatwiejszego dostępu
        $this->app->alias(LocalAIAssistant::class, 'ai.local');
    }

    /**
     * Rejestruje RuleEngine w kontenerze.
     *
     * Silnik reguł biznesowych zapewniający 90% funkcjonalności
     * systemu poprzez deterministyczne algorytmy.
     *
     * @return void
     */
    protected function registerRuleEngine(): void
    {
        $this->app->singleton(RuleEngine::class, function ($app) {
            return new RuleEngine(
                $app->make(MarketDataService::class)
            );
        });

        // Alias dla łatwiejszego dostępu
        $this->app->alias(RuleEngine::class, 'ai.rules');
    }

    /**
     * Rejestruje główny HybridAIAssistant w kontenerze.
     *
     * Orchestrator łączący wszystkie komponenty AI w spójny system
     * z automatycznym fallback i cache'owaniem.
     *
     * @return void
     */
    protected function registerHybridAIAssistant(): void
    {
        $this->app->singleton(HybridAIAssistant::class, function ($app) {
            return new HybridAIAssistant(
                $app->make(LocalAIAssistant::class),
                $app->make(RuleEngine::class),
                $app->make(TemplateSystem::class),
                $app->make(MarketDataService::class)
            );
        });

        // Główny alias dla systemu AI
        $this->app->alias(HybridAIAssistant::class, 'ai.assistant');
    }

    /**
     * Waliduje konfigurację AI pod kątem wymaganych parametrów.
     *
     * Sprawdza czy wszystkie niezbędne sekcje konfiguracji są obecne
     * i mają poprawne wartości.
     *
     * @return void
     * @throws \Exception Gdy konfiguracja jest niepoprawna
     */
    protected function validateAIConfiguration(): void
    {
        $requiredConfigKeys = [
            'ai.ollama',
            'ai.cache',
            'ai.services.base_prices',
            'ai.market_data.cities',
        ];

        foreach ($requiredConfigKeys as $key) {
            if (!config($key)) {
                Log::error("Brakuje wymaganej konfiguracji AI: {$key}");
                throw new \Exception("AI Configuration Error: Missing required key '{$key}'");
            }
        }

        // Walidacja specific values
        $basePrices = config('ai.services.base_prices', []);
        if (empty($basePrices) || !is_array($basePrices)) {
            throw new \Exception("AI Configuration Error: 'services.base_prices' must be a non-empty array");
        }

        $cities = config('ai.market_data.cities', []);
        if (empty($cities) || !is_array($cities)) {
            throw new \Exception("AI Configuration Error: 'market_data.cities' must be a non-empty array");
        }

        Log::info('Konfiguracja AI została pomyślnie zwalidowana');
    }

    /**
     * Sprawdza dostępność serwera Ollama.
     *
     * Próbuje połączyć się z lokalnym serwerem AI i loguje rezultat.
     * Nie rzuca wyjątków - system może działać bez AI.
     *
     * @return void
     */
    protected function checkOllamaAvailability(): void
    {
        try {
            $localAI = $this->app->make(LocalAIAssistant::class);

            if ($localAI->isServiceAvailable()) {
                Log::info('Ollama AI Server jest dostępny i gotowy do użycia');
            } else {
                Log::warning('Ollama AI Server nie jest dostępny - używam fallback templates');
            }
        } catch (\Exception $e) {
            Log::warning('Nie udało się sprawdzić dostępności Ollama', [
                'error' => $e->getMessage(),
                'fallback' => 'System będzie używał templates i reguł biznesowych'
            ]);
        }
    }

    /**
     * Lista serwisów udostępnianych przez tego providera.
     *
     * Używane do optymalizacji lazy loading serwisów w kontenerze.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            HybridAIAssistant::class,
            LocalAIAssistant::class,
            RuleEngine::class,
            TemplateSystem::class,
            MarketDataService::class,
            'ai.assistant',
            'ai.local',
            'ai.rules',
            'ai.templates',
            'ai.market_data',
        ];
    }
}