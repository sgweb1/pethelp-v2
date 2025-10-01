<?php

namespace Tests\Feature\AI;

use App\Services\AI\HybridAIAssistant;
use App\Services\AI\LocalAIAssistant;
use App\Services\AI\MarketDataService;
use App\Services\AI\RuleEngine;
use App\Services\AI\TemplateSystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Test integracji systemu AI Assistant.
 *
 * Testuje działanie wszystkich komponentów AI w środowisku Laravel,
 * integrację z Service Container i API endpoints.
 *
 * @package Tests\Feature\AI
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class AIAssistantIntegrationTest extends TestCase
{
    /**
     * Testuje czy wszystkie serwisy AI są poprawnie zarejestrowane w kontenerze.
     *
     * @return void
     */
    public function test_ai_services_are_registered_in_container(): void
    {
        // Test HybridAIAssistant
        $hybridAI = app(HybridAIAssistant::class);
        $this->assertInstanceOf(HybridAIAssistant::class, $hybridAI);

        // Test via alias
        $hybridAIAlias = app('ai.assistant');
        $this->assertInstanceOf(HybridAIAssistant::class, $hybridAIAlias);

        // Test LocalAIAssistant
        $localAI = app(LocalAIAssistant::class);
        $this->assertInstanceOf(LocalAIAssistant::class, $localAI);

        // Test RuleEngine
        $ruleEngine = app(RuleEngine::class);
        $this->assertInstanceOf(RuleEngine::class, $ruleEngine);

        // Test TemplateSystem
        $templateSystem = app(TemplateSystem::class);
        $this->assertInstanceOf(TemplateSystem::class, $templateSystem);

        // Test MarketDataService
        $marketData = app(MarketDataService::class);
        $this->assertInstanceOf(MarketDataService::class, $marketData);
    }

    /**
     * Testuje generowanie sugestii dla kroku 1 (podstawowe informacje).
     *
     * @return void
     */
    public function test_step_1_suggestions_generation(): void
    {
        $hybridAI = app(HybridAIAssistant::class);

        $wizardData = [
            'name' => 'Anna Kowalska',
            'city' => 'Warszawa',
            'experience' => 'beginner'
        ];

        $suggestions = $hybridAI->getStepSuggestions(1, $wizardData);

        $this->assertIsArray($suggestions);
        $this->assertArrayHasKey('title', $suggestions);
        $this->assertArrayHasKey('type', $suggestions);
        $this->assertEquals('rule_based', $suggestions['type']);
    }

    /**
     * Testuje generowanie sugestii dla kroku 3 (bio - AI powered).
     *
     * @return void
     */
    public function test_step_3_bio_suggestions_generation(): void
    {
        $hybridAI = app(HybridAIAssistant::class);

        $wizardData = [
            'name' => 'Jan Nowak',
            'city' => 'Kraków',
            'experience_years' => 2,
            'pet_types' => ['dog', 'cat']
        ];

        $suggestions = $hybridAI->getStepSuggestions(3, $wizardData);

        $this->assertIsArray($suggestions);
        $this->assertArrayHasKey('title', $suggestions);
        $this->assertArrayHasKey('type', $suggestions);

        // Krok 3 powinien próbować AI, ale fallback na templates
        $this->assertContains($suggestions['type'], ['ai_generated', 'template_fallback']);
    }

    /**
     * Testuje generowanie sugestii dla kroku 5 (wybór usług).
     *
     * @return void
     */
    public function test_step_5_services_suggestions_generation(): void
    {
        $hybridAI = app(HybridAIAssistant::class);

        $wizardData = [
            'name' => 'Ewa Wiśniewska',
            'city' => 'Gdańsk',
            'experience_years' => 3,
            'housing_type' => 'apartment'
        ];

        $suggestions = $hybridAI->getStepSuggestions(5, $wizardData);

        $this->assertIsArray($suggestions);
        $this->assertArrayHasKey('title', $suggestions);
        $this->assertArrayHasKey('suggestions', $suggestions);
        $this->assertArrayHasKey('market_insights', $suggestions);
    }

    /**
     * Testuje generowanie sugestii cenowych dla kroku 7.
     *
     * @return void
     */
    public function test_step_7_pricing_suggestions_generation(): void
    {
        $hybridAI = app(HybridAIAssistant::class);

        $wizardData = [
            'city' => 'Warszawa',
            'services' => ['dog_walking', 'pet_sitting'],
            'experience_years' => 1
        ];

        $suggestions = $hybridAI->getStepSuggestions(7, $wizardData);

        $this->assertIsArray($suggestions);
        $this->assertArrayHasKey('title', $suggestions);
        $this->assertArrayHasKey('type', $suggestions);
        $this->assertEquals('market_analysis', $suggestions['type']);
        $this->assertArrayHasKey('pricing', $suggestions);
        $this->assertArrayHasKey('strategy_tips', $suggestions);
    }

    /**
     * Testuje API endpoint dla generowania sugestii.
     *
     * @return void
     */
    public function test_ai_suggestions_api_endpoint(): void
    {
        $response = $this->withoutMiddleware()
                         ->postJson('/api/ai/suggestions/1', [
            'wizard_data' => [
                'name' => 'Test User',
                'city' => 'Warszawa'
            ],
            'context' => []
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'title',
                         'type'
                     ]
                 ]);

        $this->assertTrue($response->json('success'));
    }

    /**
     * Testuje API endpoint dla statystyk AI.
     *
     * @return void
     */
    public function test_ai_stats_api_endpoint(): void
    {
        $response = $this->getJson('/api/ai/stats');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'total_requests',
                         'ai_generated',
                         'rule_based',
                         'template_fallback',
                         'cache_hits'
                     ]
                 ]);

        $this->assertTrue($response->json('success'));
    }

    /**
     * Testuje cache'owanie sugestii AI.
     *
     * @return void
     */
    public function test_ai_suggestions_caching(): void
    {
        Cache::flush(); // Wyczyść cache przed testem

        $hybridAI = app(HybridAIAssistant::class);

        $wizardData = [
            'name' => 'Cache Test',
            'city' => 'Kraków'
        ];

        // Pierwsze wywołanie - powinno generować nowe sugestie
        $start = microtime(true);
        $suggestions1 = $hybridAI->getStepSuggestions(1, $wizardData);
        $time1 = microtime(true) - $start;

        // Drugie wywołanie - powinno być z cache (szybsze)
        $start = microtime(true);
        $suggestions2 = $hybridAI->getStepSuggestions(1, $wizardData);
        $time2 = microtime(true) - $start;

        // Sprawdź czy są identyczne (z cache)
        $this->assertEquals($suggestions1, $suggestions2);

        // Sprawdź czy drugie wywołanie było szybsze (cache hit)
        $this->assertLessThan($time1, $time2);
    }

    /**
     * Testuje MarketDataService dla różnych miast.
     *
     * @return void
     */
    public function test_market_data_service_city_multipliers(): void
    {
        $marketData = app(MarketDataService::class);

        // Test Warszawa (wysokie ceny)
        $pricingWarsaw = $marketData->getPricingSuggestions('dog_walking', 'Warszawa', 2);
        $this->assertArrayHasKey('recommended_price', $pricingWarsaw);
        $this->assertGreaterThan(25, $pricingWarsaw['recommended_price']); // Powyżej base price

        // Test mniejsze miasto (niższe ceny)
        $pricingOther = $marketData->getPricingSuggestions('dog_walking', 'Łódź', 2);
        $this->assertArrayHasKey('recommended_price', $pricingOther);
        $this->assertLessThan($pricingWarsaw['recommended_price'], $pricingOther['recommended_price']);
    }

    /**
     * Testuje TemplateSystem fallback.
     *
     * @return void
     */
    public function test_template_system_fallback(): void
    {
        $templateSystem = app(TemplateSystem::class);

        $wizardData = [
            'name' => 'Template Test',
            'experience_years' => 3,
            'pet_types' => ['dog']
        ];

        $suggestions = $templateSystem->generateStepSuggestions(3, $wizardData);

        $this->assertIsArray($suggestions);
        // TemplateSystem może zwracać różne struktury - sprawdźmy że coś zwraca
        $this->assertNotEmpty($suggestions);
    }

    /**
     * Testuje RuleEngine dla różnych poziomów doświadczenia.
     *
     * @return void
     */
    public function test_rule_engine_experience_based_suggestions(): void
    {
        $ruleEngine = app(RuleEngine::class);

        // Test dla początkującego
        $beginnerData = ['experience_years' => 0, 'city' => 'Warszawa'];
        $beginnerSuggestions = $ruleEngine->generateStepSuggestions(5, $beginnerData);

        // Test dla doświadczonego
        $experiencedData = ['experience_years' => 5, 'city' => 'Warszawa'];
        $experiencedSuggestions = $ruleEngine->generateStepSuggestions(5, $experiencedData);

        $this->assertIsArray($beginnerSuggestions);
        $this->assertIsArray($experiencedSuggestions);

        // Sugestie powinny się różnić w zależności od doświadczenia
        $this->assertNotEquals($beginnerSuggestions, $experiencedSuggestions);
    }
}