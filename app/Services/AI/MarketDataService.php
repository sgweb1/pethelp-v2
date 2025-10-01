<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * Serwis analizy danych rynkowych dla usług opieki nad zwierzętami.
 *
 * Dostarcza inteligentne sugestie cenowe, analizy konkurencyjności
 * i insighty rynkowe oparte na lokalizacji, typie usługi i doświadczeniu.
 *
 * @package App\Services\AI
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class MarketDataService
{
    /**
     * Dane konfiguracyjne serwisu.
     *
     * @var array
     */
    protected array $config;

    /**
     * Cache prefix dla kluczy cache.
     *
     * @var string
     */
    protected string $cachePrefix;

    /**
     * Konstruktor serwisu analizy danych rynkowych.
     *
     * @param array $config Konfiguracja serwisu
     */
    public function __construct()
    {
        $this->config = config('ai.market_data', []);
        $this->cachePrefix = config('ai.cache.prefix', 'ai_assistant:') . 'market:';
    }

    /**
     * Generuje sugestie cenowe dla konkretnej usługi.
     *
     * Analizuje bazową cenę usługi, lokalizację, doświadczenie opiekuna
     * i warunki rynkowe, aby zaproponować optymalny przedział cenowy.
     *
     * @param string $serviceType Typ usługi (dog_walking, pet_sitting, etc.)
     * @param string $city Miasto świadczenia usługi
     * @param int $experienceYears Lata doświadczenia opiekuna
     * @param array $additionalFactors Dodatkowe czynniki wpływające na cenę
     * @return array Sugestie cenowe z uzasadnieniem
     *
     * @example
     * $pricing = $service->getPricingSuggestions('dog_walking', 'Warsaw', 3, [
     *     'has_certification' => true,
     *     'offers_weekend' => true
     * ]);
     */
    public function getPricingSuggestions(
        string $serviceType,
        string $city,
        int $experienceYears,
        array $additionalFactors = []
    ): array {
        $cacheKey = $this->cachePrefix . "pricing:{$serviceType}:{$city}:{$experienceYears}:" . md5(serialize($additionalFactors));

        return Cache::remember($cacheKey, $this->config['cache_ttl'] ?? 3600, function () use (
            $serviceType,
            $city,
            $experienceYears,
            $additionalFactors
        ) {
            return $this->calculatePricingSuggestions($serviceType, $city, $experienceYears, $additionalFactors);
        });
    }

    /**
     * Pobiera dane o popularności usług w konkretnym mieście.
     *
     * Zwraca ranking najpopularniejszych usług wraz ze statystykami
     * zapotrzebowania i trendem cenowym.
     *
     * @param string $city Miasto do analizy
     * @return array Dane o popularności usług
     *
     * @example
     * $popularity = $service->getServicePopularity('Warsaw');
     * // Zwraca: ['dog_walking' => ['demand' => 'high', 'trend' => 'up'], ...]
     */
    public function getServicePopularity(string $city): array
    {
        $cacheKey = $this->cachePrefix . "popularity:{$city}";

        return Cache::remember($cacheKey, $this->config['cache_ttl'] ?? 3600, function () use ($city) {
            return $this->calculateServicePopularity($city);
        });
    }

    /**
     * Analizuje konkurencję w danym mieście dla określonej usługi.
     *
     * Ocenia poziom konkurencji, średnie ceny rynkowe i możliwości
     * pozycjonowania cenowego dla nowych opiekunów.
     *
     * @param string $serviceType Typ usługi
     * @param string $city Miasto analizy
     * @return array Analiza konkurencji
     *
     * @example
     * $competition = $service->getCompetitionAnalysis('pet_sitting', 'Krakow');
     */
    public function getCompetitionAnalysis(string $serviceType, string $city): array
    {
        $cacheKey = $this->cachePrefix . "competition:{$serviceType}:{$city}";

        return Cache::remember($cacheKey, $this->config['cache_ttl'] ?? 3600, function () use ($serviceType, $city) {
            return $this->calculateCompetitionAnalysis($serviceType, $city);
        });
    }

    /**
     * Generuje insights rynkowe dla konkretnego regionu.
     *
     * Dostarcza kompleksowe informacje o trendach rynkowych,
     * sezonowości i okazjach biznesowych.
     *
     * @param string $city Miasto do analizy
     * @return array Insights rynkowe
     */
    public function getMarketInsights(string $city): array
    {
        $cacheKey = $this->cachePrefix . "insights:{$city}";

        return Cache::remember($cacheKey, $this->config['cache_ttl'] ?? 3600, function () use ($city) {
            return $this->generateMarketInsights($city);
        });
    }

    /**
     * Oblicza sugestie cenowe na podstawie czynników rynkowych.
     *
     * @param string $serviceType Typ usługi
     * @param string $city Miasto
     * @param int $experienceYears Lata doświadczenia
     * @param array $additionalFactors Dodatkowe czynniki
     * @return array Kalkulacje cenowe
     */
    protected function calculatePricingSuggestions(
        string $serviceType,
        string $city,
        int $experienceYears,
        array $additionalFactors
    ): array {
        $basePrice = $this->getBasePrice($serviceType);
        $cityMultiplier = $this->getCityMultiplier($city);
        $experienceBonus = $this->calculateExperienceBonus($experienceYears);
        $factorsBonus = $this->calculateAdditionalFactorsBonus($additionalFactors);

        $adjustedPrice = $basePrice * $cityMultiplier * (1 + $experienceBonus + $factorsBonus);

        $minVariance = config('ai.rules.min_price_variance', 0.8);
        $maxVariance = config('ai.rules.max_price_variance', 1.2);

        $minPrice = round($adjustedPrice * $minVariance, 2);
        $maxPrice = round($adjustedPrice * $maxVariance, 2);
        $recommendedPrice = round($adjustedPrice, 2);

        Log::info('Wygenerowano sugestie cenowe', [
            'service' => $serviceType,
            'city' => $city,
            'experience' => $experienceYears,
            'base_price' => $basePrice,
            'adjusted_price' => $adjustedPrice,
            'recommended' => $recommendedPrice
        ]);

        return [
            'recommended_price' => $recommendedPrice,
            'price_range' => [
                'min' => $minPrice,
                'max' => $maxPrice
            ],
            'factors' => [
                'base_price' => $basePrice,
                'city_multiplier' => $cityMultiplier,
                'experience_bonus' => $experienceBonus,
                'additional_bonus' => $factorsBonus
            ],
            'market_position' => $this->determineMarketPosition($recommendedPrice, $serviceType, $city),
            'suggestions' => $this->generatePricingSuggestionText($recommendedPrice, $serviceType, $city)
        ];
    }

    /**
     * Oblicza popularność usług w mieście.
     *
     * @param string $city Miasto analizy
     * @return array Dane popularności
     */
    protected function calculateServicePopularity(string $city): array
    {
        $popularServices = config('ai.services.popular_services', []);
        $allServices = array_keys(config('ai.services.base_prices', []));

        $popularity = [];

        foreach ($allServices as $service) {
            $isPopular = in_array($service, $popularServices);
            $demandLevel = $isPopular ? 'high' : 'medium';

            // Symulacja trendów na podstawie typu usługi i miasta
            $trend = $this->calculateServiceTrend($service, $city);

            $popularity[$service] = [
                'demand' => $demandLevel,
                'trend' => $trend,
                'market_share' => $isPopular ? rand(15, 25) : rand(5, 15),
                'growth_rate' => rand(-5, 15) // procent wzrostu rok do roku
            ];
        }

        return $popularity;
    }

    /**
     * Analizuje konkurencję dla usługi w mieście.
     *
     * @param string $serviceType Typ usługi
     * @param string $city Miasto
     * @return array Analiza konkurencji
     */
    protected function calculateCompetitionAnalysis(string $serviceType, string $city): array
    {
        $basePrice = $this->getBasePrice($serviceType);
        $cityMultiplier = $this->getCityMultiplier($city);
        $marketPrice = $basePrice * $cityMultiplier;

        // Symulacja danych konkurencji
        $competitorsCount = $this->estimateCompetitorsCount($serviceType, $city);
        $competitionLevel = $this->determineCompetitionLevel($competitorsCount);

        return [
            'competitors_count' => $competitorsCount,
            'competition_level' => $competitionLevel,
            'average_market_price' => round($marketPrice, 2),
            'price_distribution' => [
                'low_range' => round($marketPrice * 0.7, 2),
                'mid_range' => round($marketPrice, 2),
                'high_range' => round($marketPrice * 1.3, 2)
            ],
            'market_gaps' => $this->identifyMarketGaps($serviceType, $city),
            'recommendations' => $this->generateCompetitionRecommendations($competitionLevel, $serviceType)
        ];
    }

    /**
     * Generuje kompleksowe insights rynkowe.
     *
     * @param string $city Miasto analizy
     * @return array Insights rynkowe
     */
    protected function generateMarketInsights(string $city): array
    {
        return [
            'city_overview' => [
                'name' => $city,
                'market_size' => $this->estimateMarketSize($city),
                'growth_potential' => $this->assessGrowthPotential($city),
                'pet_ownership_rate' => $this->estimatePetOwnershipRate($city)
            ],
            'seasonal_trends' => [
                'peak_months' => ['June', 'July', 'August', 'December'],
                'low_months' => ['January', 'February', 'March'],
                'holiday_demand' => 'Very High'
            ],
            'opportunities' => $this->identifyBusinessOpportunities($city),
            'challenges' => $this->identifyMarketChallenges($city),
            'recommendations' => $this->generateMarketRecommendations($city)
        ];
    }

    /**
     * Pobiera bazową cenę dla typu usługi.
     *
     * @param string $serviceType Typ usługi
     * @return float Bazowa cena
     */
    protected function getBasePrice(string $serviceType): float
    {
        return config("ai.services.base_prices.{$serviceType}", 50.0);
    }

    /**
     * Pobiera mnożnik cenowy dla miasta.
     *
     * @param string $city Miasto
     * @return float Mnożnik cenowy
     */
    protected function getCityMultiplier(string $city): float
    {
        return config("ai.market_data.cities.{$city}.multiplier") ??
               config("ai.market_data.cities.Other.multiplier", 1.0);
    }

    /**
     * Oblicza bonus za doświadczenie.
     *
     * @param int $experienceYears Lata doświadczenia
     * @return float Współczynnik bonusu
     */
    protected function calculateExperienceBonus(int $experienceYears): float
    {
        $bonusPerYear = config('ai.rules.experience_bonus_multiplier', 0.1);
        $maxBonus = config('ai.rules.max_experience_bonus', 0.5);

        return min($experienceYears * $bonusPerYear, $maxBonus);
    }

    /**
     * Oblicza bonus za dodatkowe czynniki.
     *
     * @param array $factors Dodatkowe czynniki
     * @return float Współczynnik bonusu
     */
    protected function calculateAdditionalFactorsBonus(array $factors): float
    {
        $totalBonus = 0.0;

        if (!empty($factors['has_certification'])) {
            $totalBonus += 0.15; // 15% bonus za certyfikat
        }

        if (!empty($factors['offers_weekend'])) {
            $totalBonus += 0.10; // 10% bonus za weekendy
        }

        if (!empty($factors['emergency_availability'])) {
            $totalBonus += 0.20; // 20% bonus za dyspozycyjność awaryjną
        }

        if (!empty($factors['multiple_pets'])) {
            $totalBonus += 0.12; // 12% bonus za opiekę nad wieloma zwierzętami
        }

        return min($totalBonus, 0.5); // maksymalnie 50% bonusu
    }

    /**
     * Określa pozycję rynkową na podstawie ceny.
     *
     * @param float $price Cena usługi
     * @param string $serviceType Typ usługi
     * @param string $city Miasto
     * @return string Pozycja rynkowa
     */
    protected function determineMarketPosition(float $price, string $serviceType, string $city): string
    {
        $basePrice = $this->getBasePrice($serviceType) * $this->getCityMultiplier($city);

        if ($price <= $basePrice * 0.8) {
            return 'budget';
        } elseif ($price <= $basePrice * 1.1) {
            return 'competitive';
        } elseif ($price <= $basePrice * 1.3) {
            return 'premium';
        } else {
            return 'luxury';
        }
    }

    /**
     * Generuje tekstowe sugestie cenowe.
     *
     * @param float $price Zalecana cena
     * @param string $serviceType Typ usługi
     * @param string $city Miasto
     * @return array Tekstowe sugestie
     */
    protected function generatePricingSuggestionText(float $price, string $serviceType, string $city): array
    {
        $position = $this->determineMarketPosition($price, $serviceType, $city);

        $suggestions = [
            'budget' => "Ta cena pozycjonuje Cię jako opcja budżetowa. Świetne dla rozpoczęcia działalności.",
            'competitive' => "Cena jest konkurencyjna dla rynku w {$city}. Dobry balans między dostępnością a zyskiem.",
            'premium' => "To pozycjonowanie premium. Upewnij się, że oferujesz wyjątkową jakość usług.",
            'luxury' => "Bardzo wysokie pozycjonowanie. Wymaga doskonałej reputacji i wyjątkowych usług."
        ];

        return [
            'main_suggestion' => $suggestions[$position],
            'additional_tips' => $this->generateAdditionalPricingTips($serviceType, $city, $position)
        ];
    }

    /**
     * Generuje dodatkowe wskazówki cenowe.
     *
     * @param string $serviceType Typ usługi
     * @param string $city Miasto
     * @param string $position Pozycja rynkowa
     * @return array Dodatkowe wskazówki
     */
    protected function generateAdditionalPricingTips(string $serviceType, string $city, string $position): array
    {
        $tips = [];

        if ($position === 'budget') {
            $tips[] = "Rozważ pakiety usług aby zwiększyć średnią wartość zamówienia";
            $tips[] = "Zbieraj pozytywne opinie aby móc podnieść ceny w przyszłości";
        } elseif ($position === 'premium' || $position === 'luxury') {
            $tips[] = "Podkreśl swoje kwalifikacje i doświadczenie w opisie";
            $tips[] = "Oferuj dodatkowe usługi w cenie (zdjęcia, raporty)";
        }

        if ($serviceType === 'dog_walking') {
            $tips[] = "Rozważ różne ceny za spacery grupowe vs indywidualne";
        }

        return $tips;
    }

    /**
     * Oblicza trend dla usługi w mieście.
     *
     * @param string $service Usługa
     * @param string $city Miasto
     * @return string Trend (up/stable/down)
     */
    protected function calculateServiceTrend(string $service, string $city): string
    {
        // Symulacja trendów - w rzeczywistej implementacji
        // byłyby to dane z bazy danych lub API
        $trends = ['up', 'stable', 'down'];
        $weights = [60, 30, 10]; // większe prawdopodobieństwo trendu wzrostowego

        // Wybierz losowy trend z uwzględnieniem wag
        $randomValue = rand(1, 100);
        if ($randomValue <= 60) {
            return 'up';
        } elseif ($randomValue <= 90) {
            return 'stable';
        } else {
            return 'down';
        }
    }

    /**
     * Estymuje liczbę konkurentów w mieście.
     *
     * @param string $serviceType Typ usługi
     * @param string $city Miasto
     * @return int Liczba konkurentów
     */
    protected function estimateCompetitorsCount(string $serviceType, string $city): int
    {
        $cityMultiplier = $this->getCityMultiplier($city);
        $baseCompetitors = 50; // bazowa liczba dla średniego miasta

        return round($baseCompetitors * $cityMultiplier * rand(80, 120) / 100);
    }

    /**
     * Określa poziom konkurencji.
     *
     * @param int $competitorsCount Liczba konkurentów
     * @return string Poziom konkurencji
     */
    protected function determineCompetitionLevel(int $competitorsCount): string
    {
        if ($competitorsCount < 20) {
            return 'low';
        } elseif ($competitorsCount < 50) {
            return 'medium';
        } elseif ($competitorsCount < 100) {
            return 'high';
        } else {
            return 'very_high';
        }
    }

    /**
     * Identyfikuje luki rynkowe.
     *
     * @param string $serviceType Typ usługi
     * @param string $city Miasto
     * @return array Luki rynkowe
     */
    protected function identifyMarketGaps(string $serviceType, string $city): array
    {
        return [
            'emergency_services' => 'Mało opiekunów oferuje usługi awaryjne',
            'specialized_care' => 'Brak specjalistów dla starszych zwierząt',
            'group_services' => 'Możliwość spacerów grupowych'
        ];
    }

    /**
     * Generuje rekomendacje konkurencyjne.
     *
     * @param string $competitionLevel Poziom konkurencji
     * @param string $serviceType Typ usługi
     * @return array Rekomendacje
     */
    protected function generateCompetitionRecommendations(string $competitionLevel, string $serviceType): array
    {
        $recommendations = [
            'low' => [
                'Doskonała okazja do wejścia na rynek',
                'Możliwość ustanowienia się jako lider lokalny',
                'Inwestuj w marketing lokalny'
            ],
            'medium' => [
                'Znajdź swoją niszę specjalizacyjną',
                'Skup się na wyjątkowej obsłudze klienta',
                'Buduj silną bazę stałych klientów'
            ],
            'high' => [
                'Wyróżnij się dodatkowymi usługami',
                'Konkuruj jakością, nie ceną',
                'Rozważ współpracę z lokalnymi weterynarami'
            ],
            'very_high' => [
                'Znajdź unikalną propozycję wartości',
                'Specializuj się w konkretnym typie opieki',
                'Rozważ niszowe usługi premium'
            ]
        ];

        return $recommendations[$competitionLevel] ?? [];
    }

    /**
     * Estymuje wielkość rynku w mieście.
     *
     * @param string $city Miasto
     * @return string Wielkość rynku
     */
    protected function estimateMarketSize(string $city): string
    {
        $multiplier = $this->getCityMultiplier($city);

        if ($multiplier >= 1.2) {
            return 'large';
        } elseif ($multiplier >= 1.1) {
            return 'medium-large';
        } elseif ($multiplier >= 1.0) {
            return 'medium';
        } else {
            return 'small';
        }
    }

    /**
     * Ocenia potencjał wzrostu dla miasta.
     *
     * @param string $city Miasto
     * @return string Potencjał wzrostu
     */
    protected function assessGrowthPotential(string $city): string
    {
        // Symulacja - w rzeczywistości bazowałoby na danych demograficznych
        $potentials = ['high', 'medium', 'low'];
        return $potentials[array_rand($potentials)];
    }

    /**
     * Estymuje wskaźnik posiadania zwierząt w mieście.
     *
     * @param string $city Miasto
     * @return string Wskaźnik posiadania zwierząt
     */
    protected function estimatePetOwnershipRate(string $city): string
    {
        $multiplier = $this->getCityMultiplier($city);
        $rate = round(25 + ($multiplier - 1) * 20); // bazowe 25% + bonus za miasto

        return "{$rate}%";
    }

    /**
     * Identyfikuje możliwości biznesowe.
     *
     * @param string $city Miasto
     * @return array Możliwości biznesowe
     */
    protected function identifyBusinessOpportunities(string $city): array
    {
        return [
            'Współpraca z lokalnymi sklepami zoologicznymi',
            'Usługi dla biur - opieka nad zwierzętami pracowników',
            'Specjalizacja w konkretnych rasach',
            'Usługi fotograficzne dla zwierząt'
        ];
    }

    /**
     * Identyfikuje wyzwania rynkowe.
     *
     * @param string $city Miasto
     * @return array Wyzwania rynkowe
     */
    protected function identifyMarketChallenges(string $city): array
    {
        return [
            'Sezonowość biznesu (spadek zimą)',
            'Konkurencja cenowa',
            'Budowanie zaufania klientów',
            'Zarządzanie harmonogramem'
        ];
    }

    /**
     * Generuje rekomendacje rynkowe.
     *
     * @param string $city Miasto
     * @return array Rekomendacje rynkowe
     */
    protected function generateMarketRecommendations(string $city): array
    {
        return [
            'Skup się na budowaniu długoterminowych relacji z klientami',
            'Wykorzystaj media społecznościowe do promocji',
            'Rozważ ubezpieczenie odpowiedzialności cywilnej',
            'Inwestuj w aplikację mobilną dla klientów'
        ];
    }
}