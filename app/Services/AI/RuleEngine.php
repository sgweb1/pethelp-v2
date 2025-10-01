<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * Silnik reguł biznesowych dla systemu AI Assistant.
 *
 * Implementuje logikę biznesową dla generowania inteligentnych sugestii
 * w poszczególnych krokach kreatora Pet Sitter. Opiera się na regułach
 * biznesowych, wzorcach zachowań użytkowników i best practices.
 *
 * @package App\Services\AI
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class RuleEngine
{
    /**
     * Konfiguracja silnika reguł.
     *
     * @var array
     */
    protected array $config;

    /**
     * Instancja serwisu danych rynkowych.
     *
     * @var MarketDataService
     */
    protected MarketDataService $marketDataService;

    /**
     * Konstruktor silnika reguł biznesowych.
     *
     * @param MarketDataService $marketDataService Serwis danych rynkowych
     */
    public function __construct(MarketDataService $marketDataService)
    {
        $this->config = config('ai.rules', []);
        $this->marketDataService = $marketDataService;
    }

    /**
     * Generuje sugestie dla konkretnego kroku kreatora.
     *
     * Analizuje dane użytkownika i aktualny krok, aby dostarczyć
     * kontekstowe, inteligentne sugestie biznesowe.
     *
     * @param int $step Numer kroku kreatora (1-10)
     * @param array $userData Dane zebrane od użytkownika
     * @param array $context Dodatkowy kontekst (lokalizacja, pora dnia, etc.)
     * @return array Sugestie dla danego kroku
     *
     * @example
     * $suggestions = $engine->generateStepSuggestions(5, [
     *     'pet_types' => ['dog', 'cat'],
     *     'experience' => 3,
     *     'location' => 'Warsaw'
     * ], ['time_of_day' => 'evening']);
     */
    public function generateStepSuggestions(int $step, array $userData, array $context = []): array
    {
        Log::info('Generowanie sugestii reguł biznesowych', [
            'step' => $step,
            'user_data_keys' => array_keys($userData),
            'context' => $context
        ]);

        return match ($step) {
            1 => $this->generatePersonalInfoSuggestions($userData, $context),
            2 => $this->generateLocationSuggestions($userData, $context),
            3 => $this->generateBioSuggestions($userData, $context),
            4 => $this->generateExperienceSuggestions($userData, $context),
            5 => $this->generateServicesSuggestions($userData, $context),
            6 => $this->generateAvailabilitySuggestions($userData, $context),
            7 => $this->generatePricingSuggestions($userData, $context),
            8 => $this->generateCertificationSuggestions($userData, $context),
            9 => $this->generatePhotoSuggestions($userData, $context),
            10 => $this->generateFinalReviewSuggestions($userData, $context),
            default => $this->generateGenericSuggestions($userData, $context)
        };
    }

    /**
     * Sugestie dla kroku 1 - Informacje osobiste.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generatePersonalInfoSuggestions(array $userData, array $context): array
    {
        $suggestions = [
            'tips' => [
                'Użyj prawdziwego imienia - buduje zaufanie klientów',
                'Dodaj krótkie, przyjazne przedstawienie się',
                'Unikaj pseudonimów - klienci preferują prawdziwe osoby'
            ],
            'warnings' => [],
            'recommendations' => []
        ];

        // Walidacja długości imienia
        if (isset($userData['first_name']) && strlen($userData['first_name']) < 2) {
            $suggestions['warnings'][] = 'Imię powinno mieć przynajmniej 2 znaki';
        }

        // Sugestie bazujące na kompletności danych
        if (empty($userData['phone']) && empty($userData['email'])) {
            $suggestions['recommendations'][] = 'Dodanie numeru telefonu zwiększy zaufanie klientów o 40%';
        }

        return $suggestions;
    }

    /**
     * Sugestie dla kroku 2 - Lokalizacja.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generateLocationSuggestions(array $userData, array $context): array
    {
        $city = $userData['city'] ?? $context['city'] ?? 'Warsaw';
        $marketInsights = $this->marketDataService->getMarketInsights($city);

        return [
            'tips' => [
                'Określ realny zasięg działania - klienci cenią transparentność',
                'Rozważ transport publiczny przy ustalaniu obszaru',
                'Większy zasięg = więcej klientów, ale wyższe koszty'
            ],
            'market_insights' => [
                "Rynek w {$city}: " . $marketInsights['city_overview']['market_size'] ?? 'średni',
                "Potencjał wzrostu: " . $marketInsights['city_overview']['growth_potential'] ?? 'średni'
            ],
            'recommendations' => [
                'Zacznij od mniejszego obszaru i rozszerzaj sukcesywnie',
                'Uwzględnij czas dojazdu w kalkulacjach cenowych'
            ]
        ];
    }

    /**
     * Sugestie dla kroku 3 - Bio/Opis.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generateBioSuggestions(array $userData, array $context): array
    {
        $experience = $userData['experience_years'] ?? 0;
        $petTypes = $userData['pet_types'] ?? [];

        $suggestions = [
            'structure_tips' => [
                'Zacznij od swojej pasji do zwierząt',
                'Podkreśl doświadczenie i kwalifikacje',
                'Opisz swoje podejście do opieki',
                'Zakończ informacjami o dostępności'
            ],
            'content_suggestions' => [],
            'length_guide' => [
                'minimum' => config('ai.templates.bio_length.min', 100),
                'recommended' => config('ai.templates.bio_length.recommended', 250),
                'maximum' => config('ai.templates.bio_length.max', 500)
            ]
        ];

        // Spersonalizowane sugestie treści
        if ($experience > 0) {
            $suggestions['content_suggestions'][] = "Podkreśl swoje {$experience} lat doświadczenia";
        }

        if (!empty($petTypes)) {
            $petTypesStr = implode(', ', $petTypes);
            $suggestions['content_suggestions'][] = "Wspomnieć o specjalizacji w opiece nad: {$petTypesStr}";
        }

        // Sugestie bazujące na popularnych frazach
        $suggestions['popular_phrases'] = [
            'Zwierzęta to moja pasja od dzieciństwa',
            'Gwarantuję profesjonalną i odpowiedzialną opiekę',
            'Każde zwierzę traktuję jak własne',
            'Zapewniam regularne aktualizacje dla właścicieli'
        ];

        return $suggestions;
    }

    /**
     * Sugestie dla kroku 4 - Doświadczenie.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generateExperienceSuggestions(array $userData, array $context): array
    {
        $experience = $userData['experience_years'] ?? 0;

        $suggestions = [
            'tips' => [
                'Bądź szczery co do swojego doświadczenia',
                'Uwzględnij nieformalną opiekę nad zwierzętami',
                'Mentorowanie też się liczy jako doświadczenie'
            ],
            'experience_levels' => [
                0 => 'Początkujący - podkreśl entuzjazm i chęć nauki',
                1 => 'Podstawowe doświadczenie - wspomnieć o pierwszych sukcesach',
                2 => 'Rozwijające się umiejętności - podkreśl różnorodność przypadków',
                3 => 'Doświadczony - wymienić specjalizacje',
                5 => 'Ekspert - pokazać portfolio i referencje'
            ]
        ];

        // Spersonalizowane porady bazujące na poziomie doświadczenia
        if ($experience == 0) {
            $suggestions['beginner_tips'] = [
                'Zacznij od prostszych usług jak karmienie czy spacery',
                'Oferuj niższe ceny aby zdobyć pierwsze opinie',
                'Rozważ wolontariat w schronisku'
            ];
        } elseif ($experience >= 3) {
            $suggestions['expert_tips'] = [
                'Podkreśl specjalne umiejętności (pierwsza pomoc, trening)',
                'Opowiedz o trudnych przypadkach które rozwiązałeś',
                'Rozważ prowadzenie szkoleń dla innych'
            ];
        }

        return $suggestions;
    }

    /**
     * Sugestie dla kroku 5 - Usługi.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generateServicesSuggestions(array $userData, array $context): array
    {
        $experience = $userData['experience_years'] ?? 0;
        $city = $userData['city'] ?? $context['city'] ?? 'Warsaw';
        $popularity = $this->marketDataService->getServicePopularity($city);

        $maxServices = $this->config['max_services_suggestion'] ?? 5;

        // Filtrowanie usług bazujące na popularności i doświadczeniu
        $recommendedServices = $this->filterServicesByExperience($popularity, $experience);
        $topServices = array_slice($recommendedServices, 0, $maxServices);

        $suggestions = [
            'recommended_services' => $topServices,
            'market_analysis' => [
                'most_popular' => array_keys(array_slice($popularity, 0, 3)),
                'growing_trends' => $this->getGrowingServices($popularity),
                'city_specific' => "Analiza dla {$city}"
            ],
            'experience_based_tips' => $this->getExperienceBasedServiceTips($experience),
            'combination_suggestions' => $this->suggestServiceCombinations($topServices)
        ];

        // Ostrzeżenia dla początkujących
        if ($experience < 2) {
            $suggestions['warnings'] = [
                'Zacznij od 2-3 podstawowych usług',
                'Unikaj skomplikowanych usług bez doświadczenia',
                'Dodawaj nowe usługi stopniowo'
            ];
        }

        return $suggestions;
    }

    /**
     * Sugestie dla kroku 6 - Dostępność.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generateAvailabilitySuggestions(array $userData, array $context): array
    {
        return [
            'timing_tips' => [
                'Weekendy i wieczory są najbardziej opłacalne',
                'Świąteczne okresy = wyższa stawka',
                'Regularna dostępność buduje zaufanie'
            ],
            'flexibility_benefits' => [
                'Elastyczność = 30% więcej rezerwacji',
                'Usługi awaryjne pozwalają na premium pricing',
                'Wcześnie rano i późno wieczorem mniejsza konkurencja'
            ],
            'scheduling_advice' => [
                'Blokuj czas na transport między klientami',
                'Ostaw bufor czasowy na nieprzewidziane sytuacje',
                'Grupuj usługi geograficznie'
            ],
            'seasonal_insights' => [
                'Lato: wysokie zapotrzebowanie na spacery',
                'Zima: większe zapotrzebowanie na opiekę domową',
                'Święta: premium pricing dla opieki całodobowej'
            ]
        ];
    }

    /**
     * Sugestie dla kroku 7 - Wycena.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generatePricingSuggestions(array $userData, array $context): array
    {
        $services = $userData['services'] ?? [];
        $experience = $userData['experience_years'] ?? 0;
        $city = $userData['city'] ?? $context['city'] ?? 'Warsaw';

        $pricingSuggestions = [];
        $marketAnalysis = [];

        // Generowanie sugestii cenowych dla każdej usługi
        foreach ($services as $service) {
            $pricing = $this->marketDataService->getPricingSuggestions($service, $city, $experience);
            $pricingSuggestions[$service] = $pricing;

            $competition = $this->marketDataService->getCompetitionAnalysis($service, $city);
            $marketAnalysis[$service] = $competition;
        }

        return [
            'pricing_suggestions' => $pricingSuggestions,
            'market_analysis' => $marketAnalysis,
            'pricing_strategies' => [
                'competitive' => 'Ceny na poziomie rynku - bezpieczna strategia',
                'penetration' => 'Niższe ceny na start - zdobycie udziału rynku',
                'premium' => 'Wyższe ceny - wymaga wyjątkowej jakości',
                'value_based' => 'Ceny oparte na wartości dla klienta'
            ],
            'general_tips' => [
                'Zacznij niżej i podnoś ceny wraz z opiniami',
                'Oferuj pakiety usług za lepszą cenę',
                'Regularne klienci = możliwość rabatów lojalnościowych',
                'Przegląd cen co 6 miesięcy'
            ]
        ];
    }

    /**
     * Sugestie dla kroku 8 - Certyfikaty.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generateCertificationSuggestions(array $userData, array $context): array
    {
        $experience = $userData['experience_years'] ?? 0;
        $services = $userData['services'] ?? [];

        return [
            'value_proposition' => [
                'Certyfikaty zwiększają zaufanie klientów o 60%',
                'Pozwalają na 15-25% wyższe ceny',
                'Wyróżniają w konkurencji'
            ],
            'recommended_certifications' => $this->getRecommendedCertifications($services, $experience),
            'priorities' => [
                'high' => ['Pierwsza pomoc dla zwierząt', 'Ubezpieczenie OC'],
                'medium' => ['Kurs trenerski', 'Certyfikat groomer\'a'],
                'low' => ['Kursy specjalistyczne', 'Certyfikaty branżowe']
            ],
            'investment_analysis' => [
                'ROI certyfikatów: 200-400% w pierwszym roku',
                'Koszt vs korzyść różni się w zależności od usług',
                'Inwestuj najpierw w najważniejsze dla Twoich usług'
            ]
        ];
    }

    /**
     * Sugestie dla kroku 9 - Zdjęcia.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generatePhotoSuggestions(array $userData, array $context): array
    {
        return [
            'photo_types' => [
                'profile' => [
                    'Profesjonalne zdjęcie portretowe',
                    'Uśmiech i przyjazna mina',
                    'Czyste tło, dobra jakość'
                ],
                'action_shots' => [
                    'Zdjęcia z zwierzętami',
                    'W naturalnym środowisku',
                    'Pokazujące Twoją pracę'
                ],
                'workspace' => [
                    'Twoje narzędzia pracy',
                    'Przygotowane miejsce opieki',
                    'Certyfikaty i nagrody'
                ]
            ],
            'technical_tips' => [
                'Naturalne światło zawsze lepsze niż flash',
                'Rozdzielczość min. 1200x800 px',
                'Format JPG lub PNG',
                'Unikaj zdjęć rozmytych lub ciemnych'
            ],
            'psychology_tips' => [
                'Zdjęcia zwiększają konwersję o 80%',
                'Ludzie z zwierzętami wywołują zaufanie',
                'Unikaj selfie - wyglądają nieprofesjonalnie',
                'Maksymalnie 5-7 zdjęć - więcej przytłacza'
            ],
            'content_ideas' => [
                'Ty z różnymi zwierzętami',
                'Podczas spaceru w parku',
                'Karmiąc czy bawiąc się ze zwierzęciem',
                'Z nagrodami lub certyfikatami'
            ]
        ];
    }

    /**
     * Sugestie dla kroku 10 - Przegląd końcowy.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function generateFinalReviewSuggestions(array $userData, array $context): array
    {
        return [
            'checklist' => [
                'completeness' => $this->checkProfileCompleteness($userData),
                'consistency' => $this->checkDataConsistency($userData),
                'optimization' => $this->checkOptimizationOpportunities($userData)
            ],
            'launch_tips' => [
                'Przygotuj się na pierwsze 48h - najważniejsze dla algorytmu',
                'Zaproś znajomych do wystawienia pierwszych opinii',
                'Aktywnie odpowiadaj na wiadomości',
                'Monitoruj konkurencję przez pierwsze tygodnie'
            ],
            'success_metrics' => [
                'Cel: pierwsze 5 opinii w 30 dni',
                'Docelowa ocena: min. 4.5/5 gwiazdek',
                'Response rate: ponad 90%',
                'Booking rate: 15-25% z zapytań'
            ],
            'next_steps' => [
                'Przygotuj standardowe odpowiedzi na częste pytania',
                'Stwórz system śledzenia rezerwacji',
                'Zaplanuj strategię zdobywania opinii',
                'Rozważ promocję uruchomieniową'
            ]
        ];
    }

    /**
     * Generuje generyczne sugestie dla nierozpoznanych kroków.
     *
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Generyczne sugestie
     */
    protected function generateGenericSuggestions(array $userData, array $context): array
    {
        return [
            'tips' => [
                'Wypełnij wszystkie sekcje dokładnie',
                'Bądź szczery i autentyczny',
                'Myśl z perspektywy klienta'
            ],
            'recommendations' => [
                'Poświęć czas na każdy krok',
                'Poproś znajomych o feedback',
                'Regularnie aktualizuj profil'
            ]
        ];
    }

    /**
     * Filtruje usługi bazujące na poziomie doświadczenia.
     *
     * @param array $popularity Dane popularności usług
     * @param int $experience Lata doświadczenia
     * @return array Przefiltrowane usługi
     */
    protected function filterServicesByExperience(array $popularity, int $experience): array
    {
        $filtered = [];

        foreach ($popularity as $service => $data) {
            $difficulty = $this->getServiceDifficulty($service);

            // Filtrowanie bazujące na doświadczeniu
            if ($experience >= $difficulty['min_experience']) {
                $filtered[$service] = $data;
                $filtered[$service]['difficulty'] = $difficulty;
            }
        }

        // Sortowanie po popularności i trudności
        uasort($filtered, function ($a, $b) {
            // Najpierw popularne, potem łatwiejsze
            $popularityDiff = ($b['demand'] === 'high' ? 1 : 0) - ($a['demand'] === 'high' ? 1 : 0);
            if ($popularityDiff !== 0) {
                return $popularityDiff;
            }

            return $a['difficulty']['level'] <=> $b['difficulty']['level'];
        });

        return $filtered;
    }

    /**
     * Pobiera usługi z rosnącym trendem.
     *
     * @param array $popularity Dane popularności
     * @return array Rosnące usługi
     */
    protected function getGrowingServices(array $popularity): array
    {
        return array_keys(array_filter($popularity, function ($data) {
            return $data['trend'] === 'up';
        }));
    }

    /**
     * Generuje wskazówki bazujące na doświadczeniu dla usług.
     *
     * @param int $experience Lata doświadczenia
     * @return array Wskazówki
     */
    protected function getExperienceBasedServiceTips(int $experience): array
    {
        if ($experience == 0) {
            return [
                'Zacznij od spacerów - najmniejsze ryzyko',
                'Karmienie to dobry sposób na pierwsze doświadczenie',
                'Unikaj nocnej opieki bez doświadczenia'
            ];
        } elseif ($experience <= 2) {
            return [
                'Rozszerz ofertę o podstawową opiekę domową',
                'Rozważ specjalizację w konkretnym typie zwierząt',
                'Dodaj usługi transportowe'
            ];
        } else {
            return [
                'Możesz oferować pełną gamę usług',
                'Rozważ usługi premium i specjalistyczne',
                'Dodaj konsultacje behawioralne'
            ];
        }
    }

    /**
     * Sugeruje kombinacje usług.
     *
     * @param array $services Lista usług
     * @return array Sugerowane kombinacje
     */
    protected function suggestServiceCombinations(array $services): array
    {
        $combinations = [
            'basic_package' => ['dog_walking', 'feeding'],
            'care_package' => ['pet_sitting', 'feeding', 'overnight_care'],
            'premium_package' => ['grooming', 'vet_transport', 'pet_sitting'],
            'weekend_package' => ['overnight_care', 'dog_walking', 'feeding']
        ];

        $suggested = [];
        foreach ($combinations as $name => $combo) {
            $intersection = array_intersect($combo, array_keys($services));
            if (count($intersection) >= 2) {
                $suggested[$name] = $combo;
            }
        }

        return $suggested;
    }

    /**
     * Pobiera zalecane certyfikaty dla usług.
     *
     * @param array $services Lista usług
     * @param int $experience Doświadczenie
     * @return array Zalecane certyfikaty
     */
    protected function getRecommendedCertifications(array $services, int $experience): array
    {
        $certifications = [];

        if (in_array('dog_walking', $services)) {
            $certifications[] = 'Podstawy psiej psychologii';
            $certifications[] = 'Pierwsza pomoc dla psów';
        }

        if (in_array('grooming', $services)) {
            $certifications[] = 'Certyfikat groomera';
            $certifications[] = 'Higiena i bezpieczeństwo';
        }

        if (in_array('overnight_care', $services)) {
            $certifications[] = 'Opieka całodobowa nad zwierzętami';
            $certifications[] = 'Rozpoznawanie stanów nagłych';
        }

        if ($experience >= 2) {
            $certifications[] = 'Trening psów - podstawy';
            $certifications[] = 'Behawior zwierząt domowych';
        }

        return array_unique($certifications);
    }

    /**
     * Pobiera poziom trudności usługi.
     *
     * @param string $service Nazwa usługi
     * @return array Dane o trudności
     */
    protected function getServiceDifficulty(string $service): array
    {
        $difficulties = [
            'feeding' => ['level' => 1, 'min_experience' => 0],
            'dog_walking' => ['level' => 2, 'min_experience' => 0],
            'pet_sitting' => ['level' => 3, 'min_experience' => 1],
            'vet_transport' => ['level' => 3, 'min_experience' => 1],
            'overnight_care' => ['level' => 4, 'min_experience' => 2],
            'grooming' => ['level' => 4, 'min_experience' => 2],
            'behavioral_training' => ['level' => 5, 'min_experience' => 3]
        ];

        return $difficulties[$service] ?? ['level' => 3, 'min_experience' => 1];
    }

    /**
     * Sprawdza kompletność profilu.
     *
     * @param array $userData Dane użytkownika
     * @return array Status kompletności
     */
    protected function checkProfileCompleteness(array $userData): array
    {
        $required = ['first_name', 'city', 'bio', 'services', 'availability'];
        $optional = ['phone', 'certifications', 'photos', 'experience_years'];

        $completeness = [
            'required_completed' => 0,
            'optional_completed' => 0,
            'missing_required' => [],
            'missing_optional' => []
        ];

        foreach ($required as $field) {
            if (!empty($userData[$field])) {
                $completeness['required_completed']++;
            } else {
                $completeness['missing_required'][] = $field;
            }
        }

        foreach ($optional as $field) {
            if (!empty($userData[$field])) {
                $completeness['optional_completed']++;
            } else {
                $completeness['missing_optional'][] = $field;
            }
        }

        $completeness['score'] = ($completeness['required_completed'] / count($required)) * 100;

        return $completeness;
    }

    /**
     * Sprawdza spójność danych.
     *
     * @param array $userData Dane użytkownika
     * @return array Problemy ze spójnością
     */
    protected function checkDataConsistency(array $userData): array
    {
        $issues = [];

        // Sprawdzenie spójności usług z doświadczeniem
        $experience = $userData['experience_years'] ?? 0;
        $services = $userData['services'] ?? [];

        foreach ($services as $service) {
            $difficulty = $this->getServiceDifficulty($service);
            if ($experience < $difficulty['min_experience']) {
                $issues[] = "Usługa {$service} wymaga więcej doświadczenia";
            }
        }

        // Sprawdzenie spójności cen z rynkiem
        if (isset($userData['pricing'])) {
            // Tutaj można dodać logikę sprawdzania cen
        }

        return $issues;
    }

    /**
     * Sprawdza możliwości optymalizacji.
     *
     * @param array $userData Dane użytkownika
     * @return array Możliwości optymalizacji
     */
    protected function checkOptimizationOpportunities(array $userData): array
    {
        $opportunities = [];

        // Sprawdzenie długości bio
        if (isset($userData['bio'])) {
            $bioLength = strlen($userData['bio']);
            $recommendedLength = config('ai.templates.bio_length.recommended', 250);

            if ($bioLength < $recommendedLength * 0.8) {
                $opportunities[] = 'Bio można rozszerzyć dla lepszego SEO';
            }
        }

        // Sprawdzenie liczby usług
        $servicesCount = count($userData['services'] ?? []);
        if ($servicesCount < 3) {
            $opportunities[] = 'Więcej usług = więcej klientów';
        }

        // Sprawdzenie zdjęć
        $photosCount = count($userData['photos'] ?? []);
        if ($photosCount < 3) {
            $opportunities[] = 'Dodanie więcej zdjęć zwiększy konwersję';
        }

        return $opportunities;
    }
}