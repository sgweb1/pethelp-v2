<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * System inteligentnych szablonów dla AI Assistant.
 *
 * Zapewnia fallback mechanism gdy system AI nie jest dostępny,
 * generując kontekstowe sugestie bazujące na statycznych szablonach
 * z dynamiczną substitucją zmiennych.
 *
 * @package App\Services\AI
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class TemplateSystem
{
    /**
     * Konfiguracja systemu szablonów.
     *
     * @var array
     */
    protected array $config;

    /**
     * Cache prefix dla szablonów.
     *
     * @var string
     */
    protected string $cachePrefix;

    /**
     * Biblioteka szablonów bio.
     *
     * @var array
     */
    protected array $bioTemplates;

    /**
     * Biblioteka szablonów sugestii.
     *
     * @var array
     */
    protected array $suggestionTemplates;

    /**
     * Konstruktor systemu szablonów.
     */
    public function __construct()
    {
        $this->config = config('ai.templates', []);
        $this->cachePrefix = config('ai.cache.prefix', 'ai_assistant:') . 'templates:';
        $this->initializeTemplates();
    }

    /**
     * Generuje spersonalizowane bio bazujące na danych użytkownika.
     *
     * Wykorzystuje bibliotekę szablonów z dynamiczną substitucją zmiennych
     * aby stworzyć unikalne, angażujące bio dla pet sittera.
     *
     * @param array $userData Dane użytkownika (imię, doświadczenie, usługi, etc.)
     * @param array $preferences Preferencje stylu (formalny/nieformalny, długość)
     * @return string Wygenerowane bio
     *
     * @example
     * $bio = $templateSystem->generateBio([
     *     'first_name' => 'Anna',
     *     'experience_years' => 3,
     *     'pet_types' => ['dog', 'cat'],
     *     'services' => ['walking', 'sitting'],
     *     'location' => 'Warsaw'
     * ], ['style' => 'friendly', 'length' => 'medium']);
     */
    public function generateBio(array $userData, array $preferences = []): string
    {
        $cacheKey = $this->cachePrefix . 'bio:' . md5(serialize($userData) . serialize($preferences));

        return Cache::remember($cacheKey, 3600, function () use ($userData, $preferences) {
            return $this->buildBioFromTemplate($userData, $preferences);
        });
    }

    /**
     * Generuje kontekstowe sugestie dla konkretnego kroku kreatora.
     *
     * Dostarcza spersonalizowane porady i wskazówki bazujące na danych
     * użytkownika i aktualnym kroku procesu.
     *
     * @param int $step Numer kroku kreatora
     * @param array $userData Zebrane dane użytkownika
     * @param array $context Dodatkowy kontekst
     * @return array Generowane sugestie
     *
     * @example
     * $suggestions = $templateSystem->generateStepSuggestions(5, [
     *     'experience_years' => 2,
     *     'pet_types' => ['dog']
     * ]);
     */
    public function generateStepSuggestions(int $step, array $userData, array $context = []): array
    {
        $cacheKey = $this->cachePrefix . "suggestions:{$step}:" . md5(serialize($userData));

        return Cache::remember($cacheKey, 1800, function () use ($step, $userData, $context) {
            return $this->buildSuggestionsFromTemplate($step, $userData, $context);
        });
    }

    /**
     * Generuje personalizowane komunikaty motywujące.
     *
     * Tworzy pozytywne, wspierające komunikaty dostosowane do aktualnego
     * etapu procesu i profilu użytkownika.
     *
     * @param array $userData Dane użytkownika
     * @param string $context Kontekst (completion, encouragement, achievement)
     * @return string Komunikat motywujący
     */
    public function generateMotivationalMessage(array $userData, string $context = 'encouragement'): string
    {
        $templates = $this->getMotivationalTemplates($context);
        $selectedTemplate = $templates[array_rand($templates)];

        return $this->substituteVariables($selectedTemplate, $userData);
    }

    /**
     * Generuje sugestie uzupełnienia profilu.
     *
     * Analizuje kompletność profilu i sugeruje konkretne kroki
     * do jego usprawnienia.
     *
     * @param array $userData Dane użytkownika
     * @return array Sugestie uzupełnienia
     */
    public function generateCompletionSuggestions(array $userData): array
    {
        $completeness = $this->analyzeProfileCompleteness($userData);
        $suggestions = [];

        foreach ($completeness['missing_fields'] as $field) {
            $suggestions[] = $this->getFieldCompletionSuggestion($field, $userData);
        }

        return [
            'suggestions' => $suggestions,
            'priority_order' => $this->prioritizeCompletionTasks($completeness['missing_fields']),
            'impact_analysis' => $this->analyzeCompletionImpact($completeness['missing_fields'])
        ];
    }

    /**
     * Pobiera szablon bazujący na typie i kontekście.
     *
     * @param string $type Typ szablonu (bio, suggestion, message)
     * @param array $filters Filtry dla szablonów
     * @return array Pasujące szablony
     */
    public function getTemplatesByType(string $type, array $filters = []): array
    {
        $allTemplates = $this->getAllTemplates($type);

        if (empty($filters)) {
            return $allTemplates;
        }

        return array_filter($allTemplates, function ($template) use ($filters) {
            return $this->templateMatchesFilters($template, $filters);
        });
    }

    /**
     * Inicjalizuje biblioteki szablonów.
     *
     * @return void
     */
    protected function initializeTemplates(): void
    {
        $this->bioTemplates = $this->loadBioTemplates();
        $this->suggestionTemplates = $this->loadSuggestionTemplates();
    }

    /**
     * Buduje bio z szablonu z substitucją zmiennych.
     *
     * @param array $userData Dane użytkownika
     * @param array $preferences Preferencje
     * @return string Wygenerowane bio
     */
    protected function buildBioFromTemplate(array $userData, array $preferences): string
    {
        $style = $preferences['style'] ?? 'friendly';
        $length = $preferences['length'] ?? 'medium';

        // Wybór odpowiedniego szablonu
        $templateCategory = $this->selectBioTemplateCategory($userData, $style, $length);
        $template = $this->bioTemplates[$templateCategory][array_rand($this->bioTemplates[$templateCategory])];

        // Substitucja zmiennych
        $bio = $this->substituteVariables($template, $userData);

        // Dostosowanie długości
        $bio = $this->adjustBioLength($bio, $length);

        Log::info('Wygenerowano bio z szablonu', [
            'template_category' => $templateCategory,
            'style' => $style,
            'length' => $length,
            'bio_length' => strlen($bio)
        ]);

        return $bio;
    }

    /**
     * Buduje sugestie z szablonów.
     *
     * @param int $step Numer kroku
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Sugestie
     */
    protected function buildSuggestionsFromTemplate(int $step, array $userData, array $context): array
    {
        $stepTemplates = $this->suggestionTemplates["step_{$step}"] ?? $this->suggestionTemplates['generic'];

        $suggestions = [
            'tips' => [],
            'warnings' => [],
            'recommendations' => []
        ];

        foreach ($stepTemplates as $category => $templates) {
            if (isset($suggestions[$category])) {
                $selectedTemplates = $this->selectRelevantTemplates($templates, $userData, $context);
                $suggestions[$category] = array_map(function ($template) use ($userData) {
                    $templateText = is_array($template) ? ($template['content'] ?? '') : $template;
                    return $this->substituteVariables($templateText, $userData);
                }, $selectedTemplates);
            }
        }

        return $suggestions;
    }

    /**
     * Wykonuje substitucję zmiennych w szablonie.
     *
     * @param string $template Szablon do przetworzenia
     * @param array $userData Dane użytkownika
     * @return string Szablon po substitucji
     */
    protected function substituteVariables(string $template, array $userData): string
    {
        $variables = $this->config['variables'] ?? [];
        $processed = $template;

        foreach ($variables as $variable) {
            $placeholder = "{{$variable}}";
            $value = $this->getVariableValue($variable, $userData);

            $processed = str_replace($placeholder, $value, $processed);
        }

        // Substitucja dodatkowych zmiennych
        $processed = $this->substituteSpecialVariables($processed, $userData);

        return $processed;
    }

    /**
     * Pobiera wartość zmiennej z danych użytkownika.
     *
     * @param string $variable Nazwa zmiennej
     * @param array $userData Dane użytkownika
     * @return string Wartość zmiennej
     */
    protected function getVariableValue(string $variable, array $userData): string
    {
        return match ($variable) {
            'name' => $userData['first_name'] ?? 'Anonim',
            'experience_years' => $this->formatExperience($userData['experience_years'] ?? 0),
            'pet_types' => $this->formatPetTypes($userData['pet_types'] ?? []),
            'services' => $this->formatServices($userData['services'] ?? []),
            'location' => $userData['city'] ?? 'Twojej okolicy',
            'availability' => $this->formatAvailability($userData['availability'] ?? []),
            'special_skills' => $this->formatSpecialSkills($userData['certifications'] ?? []),
            default => $userData[$variable] ?? ''
        };
    }

    /**
     * Wykonuje substitucję specjalnych zmiennych.
     *
     * @param string $template Szablon
     * @param array $userData Dane użytkownika
     * @return string Przetworzony szablon
     */
    protected function substituteSpecialVariables(string $template, array $userData): string
    {
        // Conditional statements
        $template = $this->processConditionalStatements($template, $userData);

        // Random choices
        $template = $this->processRandomChoices($template);

        // Dynamic pluralization
        $template = $this->processPluralizations($template, $userData);

        return $template;
    }

    /**
     * Wybiera kategorię szablonu bio.
     *
     * @param array $userData Dane użytkownika
     * @param string $style Styl
     * @param string $length Długość
     * @return string Kategoria szablonu
     */
    protected function selectBioTemplateCategory(array $userData, string $style, string $length): string
    {
        $experience = $userData['experience_years'] ?? 0;

        if ($experience == 0) {
            return $style === 'professional' ? 'beginner_professional' : 'beginner_friendly';
        } elseif ($experience <= 2) {
            return $style === 'professional' ? 'intermediate_professional' : 'intermediate_friendly';
        } else {
            return $style === 'professional' ? 'expert_professional' : 'expert_friendly';
        }
    }

    /**
     * Dostosowuje długość bio.
     *
     * @param string $bio Bio do dostosowania
     * @param string $targetLength Docelowa długość
     * @return string Dostosowane bio
     */
    protected function adjustBioLength(string $bio, string $targetLength): string
    {
        $currentLength = strlen($bio);
        $targets = [
            'short' => 150,
            'medium' => 250,
            'long' => 400
        ];

        $target = $targets[$targetLength] ?? $targets['medium'];

        if ($currentLength > $target * 1.2) {
            // Skróć bio
            return $this->shortenBio($bio, $target);
        } elseif ($currentLength < $target * 0.8) {
            // Wydłuż bio
            return $this->extendBio($bio, $target);
        }

        return $bio;
    }

    /**
     * Wybiera relevantne szablony bazujące na danych użytkownika.
     *
     * @param array $templates Dostępne szablony
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Wybrane szablony
     */
    protected function selectRelevantTemplates(array $templates, array $userData, array $context): array
    {
        $relevant = [];
        $maxSuggestions = 3;

        foreach ($templates as $template) {
            // Sprawdź czy template jest array (a nie string)
            if (is_array($template) && $this->isTemplateRelevant($template, $userData, $context)) {
                $relevant[] = $template;
            } elseif (is_string($template)) {
                // Jeśli to string, dodaj jako prosty template
                $relevant[] = ['content' => $template];
            }
        }

        return array_slice($relevant, 0, $maxSuggestions);
    }

    /**
     * Sprawdza czy szablon jest relevantny dla użytkownika.
     *
     * @param array $template Szablon
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return bool Czy relevantny
     */
    protected function isTemplateRelevant(array $template, array $userData, array $context): bool
    {
        if (isset($template['conditions'])) {
            return $this->evaluateTemplateConditions($template['conditions'], $userData, $context);
        }

        return true;
    }

    /**
     * Ewaluuje warunki szablonu.
     *
     * @param array $conditions Warunki
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return bool Czy warunki są spełnione
     */
    protected function evaluateTemplateConditions(array $conditions, array $userData, array $context): bool
    {
        foreach ($conditions as $field => $expectedValue) {
            $actualValue = $userData[$field] ?? null;

            if (!$this->conditionMatches($actualValue, $expectedValue)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sprawdza czy warunek jest spełniony.
     *
     * @param mixed $actual Aktualna wartość
     * @param mixed $expected Oczekiwana wartość
     * @return bool Czy warunek spełniony
     */
    protected function conditionMatches($actual, $expected): bool
    {
        if (is_array($expected)) {
            return in_array($actual, $expected);
        }

        if (is_string($expected) && str_starts_with($expected, '>')) {
            $threshold = (int) substr($expected, 1);
            return (int) $actual > $threshold;
        }

        if (is_string($expected) && str_starts_with($expected, '<')) {
            $threshold = (int) substr($expected, 1);
            return (int) $actual < $threshold;
        }

        return $actual === $expected;
    }

    /**
     * Ładuje szablony bio.
     *
     * @return array Szablony bio
     */
    protected function loadBioTemplates(): array
    {
        return [
            'beginner_friendly' => [
                'Cześć! Jesam {name} i uwielbiamy spędzać czas ze zwierzętami! {experience_years} Jestem pełna entuzjazmu i chęci nauki. Oferuję {services} dla {pet_types} w {location}. Gwarantuję, że Twoje zwierzaki będą w dobrych rękach! {availability}',
                'Hej! To {name} - wielka miłośniczka zwierząt! {experience_years} Z radością zajmę się {pet_types} oferując {services}. Mieszkam w {location} i dostępna jestem {availability}. Każde zwierzę traktuję jak swojego najlepszego przyjaciela!',
                'Witaj! Jestem {name} i zwierzęta to moja pasja od dzieciństwa! {experience_years} Specjalizuję się w {services} dla {pet_types}. W {location} oferuję profesjonalną opiekę z sercem. {availability}'
            ],
            'beginner_professional' => [
                'Dzień dobry, jestem {name}. {experience_years} Jako początkująca opiekunka zwierząt oferuję {services} dla {pet_types} w {location}. Podchodzę do każdego zadania z pełnym zaangażowaniem i odpowiedzialnością. {availability}',
                'Nazywam się {name} i rozpoczynam swoją przygodę z profesjonalną opieką nad zwierzętami. {experience_years} Oferuję {services} w {location}. Gwarantuję rzetelność i pełne zaangażowanie. {availability}',
                'Jestem {name}, {experience_years} Specjalizuję się w {services} dla {pet_types}. Działam w {location} zapewniając profesjonalną opiekę na najwyższym poziomie. {availability}'
            ],
            'intermediate_friendly' => [
                'Cześć! Tu {name} - Twoja zaufana opiekunka zwierząt! {experience_years} Przez ten czas pokochałam każdego podopiecznego. Oferuję {services} dla {pet_types} w {location}. {special_skills} {availability}',
                'Hej! Jestem {name} i mam {experience_years}. W tym czasie nauczyłam się, że każde zwierzę ma swoją unikalną osobowość. Oferuję {services} w {location} z pełną pasją i zaangażowaniem! {availability}',
                'Witaj! To {name} - doświadczona opiekunka z {experience_years}. Uwielbiaam pracować z {pet_types} oferując {services}. W {location} zapewniam opiekę pełną miłości i profesjonalizmu. {availability}'
            ],
            'intermediate_professional' => [
                'Dzień dobry, jestem {name} z {experience_years} w opiece nad zwierzętami. Oferuję {services} dla {pet_types} w {location}. {special_skills} Każdemu podopiecznemu zapewniam opiekę dostosowaną do jego indywidualnych potrzeb. {availability}',
                'Nazywam się {name} i posiadam {experience_years} w profesjonalnej opiece nad zwierzętami. Specjalizuję się w {services} w {location}. Moje doświadczenie pozwala mi zapewnić opiekę najwyższej jakości. {availability}',
                'Jestem {name}, certyfikowaną opiekunką z {experience_years}. Oferuję {services} dla {pet_types} w {location}. {special_skills} Gwarantuję profesjonalną obsługę i pełne bezpieczeństwo. {availability}'
            ],
            'expert_friendly' => [
                'Cześć! Jestem {name} - ekspertką z {experience_years}! Przez lata pracy z {pet_types} zebrałam ogromne doświadczenie w {services}. W {location} tworzę drugi dom dla każdego zwierzaka. {special_skills} {availability}',
                'Hej! Tu {name} - weteran opieki nad zwierzętami z {experience_years}! Moja pasja do {pet_types} zaowocowała ekspertyzą w {services}. W {location} każdy podopieczny to członek rodziny! {availability}',
                'Witaj! Jestem {name}, doświadczoną opiekunką z {experience_years}. Przez lata specjalizowałam się w {services} dla {pet_types}. W {location} oferuję opiekę na ekspertowym poziomie. {special_skills} {availability}'
            ],
            'expert_professional' => [
                'Dzień dobry, jestem {name}, certyfikowaną specjalistką z {experience_years} w branży opieki nad zwierzętami. Oferuję {services} dla {pet_types} w {location}. {special_skills} Moja ekspertyza gwarantuje najwyższy standard opieki. {availability}',
                'Nazywam się {name} i posiadam {experience_years} oraz liczne certyfikaty w opiece nad zwierzętami. Specjalizuję się w {services} w {location}. Moje doświadczenie obejmuje pracę z {pet_types} o różnorodnych potrzebach. {availability}',
                'Jestem {name}, ekspertką z {experience_years} i wieloletnim stażem w profesjonalnej opiece nad zwierzętami. W {location} oferuję {services} na poziomie eksperckim. {special_skills} Gwarantuję bezpieczeństwo i komfort. {availability}'
            ]
        ];
    }

    /**
     * Ładuje szablony sugestii.
     *
     * @return array Szablony sugestii
     */
    protected function loadSuggestionTemplates(): array
    {
        return [
            'step_1' => [
                'tips' => [
                    'Używaj prawdziwego imienia - buduje to zaufanie',
                    'Dodaj krótkie, przyjazne przedstawienie',
                    'Podaj numer telefonu dla większej wiarygodności'
                ],
                'warnings' => [
                    [
                        'text' => 'Unikaj pseudonimów w polu imienia',
                        'conditions' => ['first_name' => '']
                    ]
                ]
            ],
            'step_3' => [
                'tips' => [
                    'Bio powinno mieć 150-400 znaków',
                    'Zacznij od swojej pasji do zwierząt',
                    'Podkreśl swoje doświadczenie: {experience_years}',
                    'Wspomnieć o typach zwierząt: {pet_types}'
                ],
                'recommendations' => [
                    'Użyj ciepłego, przyjaznego tonu',
                    'Unikaj zbyt formalnego języka',
                    'Dodaj informację o dostępności'
                ]
            ],
            'step_5' => [
                'tips' => [
                    'Zacznij od 2-3 podstawowych usług',
                    'Spacery z psami to najłatwiejszy start',
                    'Dodawaj nowe usługi stopniowo'
                ],
                'warnings' => [
                    [
                        'text' => 'Za dużo usług może przytłoczyć klientów',
                        'conditions' => ['services_count' => '>5']
                    ],
                    [
                        'text' => 'Nocna opieka wymaga doświadczenia',
                        'conditions' => ['experience_years' => '<2']
                    ]
                ]
            ],
            'step_7' => [
                'tips' => [
                    'Sprawdź ceny konkurencji w {location}',
                    'Zacznij niżej i podnoś ceny z opiniami',
                    'Oferuj pakiety usług za lepszą cenę'
                ],
                'recommendations' => [
                    'Uwzględnij swoje doświadczenie: {experience_years}',
                    'Dodaj bonus za certyfikaty',
                    'Weekendy mogą być droższe'
                ]
            ],
            'generic' => [
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
            ]
        ];
    }

    /**
     * Formatuje doświadczenie w czytelny sposób.
     *
     * @param int $years Lata doświadczenia
     * @return string Sformatowane doświadczenie
     */
    protected function formatExperience(int $years): string
    {
        if ($years == 0) {
            return 'Jestem początkującą opiekunką, ale bardzo zmotywowaną!';
        } elseif ($years == 1) {
            return 'Mam rok doświadczenia w opiece nad zwierzętami.';
        } else {
            return "Mam {$years} lat doświadczenia w opiece nad zwierzętami.";
        }
    }

    /**
     * Formatuje typy zwierząt.
     *
     * @param array $petTypes Typy zwierząt
     * @return string Sformatowane typy
     */
    protected function formatPetTypes(array $petTypes): string
    {
        if (empty($petTypes)) {
            return 'wszystkie zwierzęta';
        }

        $translations = [
            'dog' => 'psy',
            'cat' => 'koty',
            'bird' => 'ptaki',
            'rabbit' => 'króliki',
            'fish' => 'ryby',
            'reptile' => 'gady'
        ];

        $translated = array_map(function ($type) use ($translations) {
            return $translations[$type] ?? $type;
        }, $petTypes);

        if (count($translated) == 1) {
            return $translated[0];
        } elseif (count($translated) == 2) {
            return implode(' i ', $translated);
        } else {
            $last = array_pop($translated);
            return implode(', ', $translated) . ' i ' . $last;
        }
    }

    /**
     * Formatuje usługi.
     *
     * @param array $services Lista usług
     * @return string Sformatowane usługi
     */
    protected function formatServices(array $services): string
    {
        if (empty($services)) {
            return 'różne usługi opieki';
        }

        $translations = [
            'dog_walking' => 'spacery',
            'pet_sitting' => 'opiekę domową',
            'overnight_care' => 'opiekę nocną',
            'grooming' => 'pielęgnację',
            'feeding' => 'karmienie',
            'vet_transport' => 'transport weterynaryjny'
        ];

        $translated = array_map(function ($service) use ($translations) {
            return $translations[$service] ?? $service;
        }, $services);

        if (count($translated) == 1) {
            return $translated[0];
        } elseif (count($translated) == 2) {
            return implode(' i ', $translated);
        } else {
            $last = array_pop($translated);
            return implode(', ', $translated) . ' i ' . $last;
        }
    }

    /**
     * Formatuje dostępność.
     *
     * @param array $availability Dostępność
     * @return string Sformatowana dostępność
     */
    protected function formatAvailability(array $availability): string
    {
        if (empty($availability)) {
            return 'Skontaktuj się ze mną w sprawie dostępności.';
        }

        return 'Dostępna jestem zgodnie z ustalonym harmonogramem.';
    }

    /**
     * Formatuje specjalne umiejętności.
     *
     * @param array $skills Umiejętności/certyfikaty
     * @return string Sformatowane umiejętności
     */
    protected function formatSpecialSkills(array $skills): string
    {
        if (empty($skills)) {
            return '';
        }

        return 'Posiadam certyfikaty: ' . implode(', ', $skills) . '.';
    }

    /**
     * Przetwarza warunkowe instrukcje w szablonie.
     *
     * @param string $template Szablon
     * @param array $userData Dane użytkownika
     * @return string Przetworzony szablon
     */
    protected function processConditionalStatements(string $template, array $userData): string
    {
        // Pattern: {if:field:value}text{/if}
        $pattern = '/\{if:([^:]+):([^}]+)\}(.*?)\{\/if\}/s';

        return preg_replace_callback($pattern, function ($matches) use ($userData) {
            $field = $matches[1];
            $expectedValue = $matches[2];
            $content = $matches[3];

            $actualValue = $userData[$field] ?? null;

            if ($this->conditionMatches($actualValue, $expectedValue)) {
                return $content;
            }

            return '';
        }, $template);
    }

    /**
     * Przetwarza losowe wybory w szablonie.
     *
     * @param string $template Szablon
     * @return string Przetworzony szablon
     */
    protected function processRandomChoices(string $template): string
    {
        // Pattern: {random:option1|option2|option3}
        $pattern = '/\{random:([^}]+)\}/';

        return preg_replace_callback($pattern, function ($matches) {
            $options = explode('|', $matches[1]);
            return $options[array_rand($options)];
        }, $template);
    }

    /**
     * Przetwarza pluralizację w szablonie.
     *
     * @param string $template Szablon
     * @param array $userData Dane użytkownika
     * @return string Przetworzony szablon
     */
    protected function processPluralizations(string $template, array $userData): string
    {
        // Pattern: {plural:field:singular|plural}
        $pattern = '/\{plural:([^:]+):([^|]+)\|([^}]+)\}/';

        return preg_replace_callback($pattern, function ($matches) use ($userData) {
            $field = $matches[1];
            $singular = $matches[2];
            $plural = $matches[3];

            $value = $userData[$field] ?? 0;
            $count = is_array($value) ? count($value) : (int) $value;

            return $count === 1 ? $singular : $plural;
        }, $template);
    }

    /**
     * Analizuje kompletność profilu.
     *
     * @param array $userData Dane użytkownika
     * @return array Analiza kompletności
     */
    protected function analyzeProfileCompleteness(array $userData): array
    {
        $requiredFields = ['first_name', 'city', 'bio', 'services'];
        $optionalFields = ['phone', 'experience_years', 'certifications', 'photos'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                $missingFields[] = $field;
            }
        }

        foreach ($optionalFields as $field) {
            if (empty($userData[$field])) {
                $missingFields[] = $field;
            }
        }

        return [
            'missing_fields' => $missingFields,
            'completion_percentage' => $this->calculateCompletionPercentage($userData),
            'required_missing' => array_intersect($missingFields, $requiredFields),
            'optional_missing' => array_intersect($missingFields, $optionalFields)
        ];
    }

    /**
     * Generuje sugestię uzupełnienia dla pola.
     *
     * @param string $field Pole do uzupełnienia
     * @param array $userData Dane użytkownika
     * @return array Sugestia uzupełnienia
     */
    protected function getFieldCompletionSuggestion(string $field, array $userData): array
    {
        $suggestions = [
            'phone' => [
                'title' => 'Dodaj numer telefonu',
                'description' => 'Zwiększy to zaufanie klientów o 40%',
                'priority' => 'high'
            ],
            'bio' => [
                'title' => 'Napisz swoje bio',
                'description' => 'Opowiedz klientom o swojej pasji do zwierząt',
                'priority' => 'critical'
            ],
            'experience_years' => [
                'title' => 'Podaj lata doświadczenia',
                'description' => 'Pomaga w wycenie usług i budowaniu zaufania',
                'priority' => 'medium'
            ],
            'certifications' => [
                'title' => 'Dodaj certyfikaty',
                'description' => 'Pozwala na wyższe ceny i większe zaufanie',
                'priority' => 'medium'
            ],
            'photos' => [
                'title' => 'Dodaj zdjęcia',
                'description' => 'Profile ze zdjęciami mają 80% więcej konwersji',
                'priority' => 'high'
            ]
        ];

        return $suggestions[$field] ?? [
            'title' => "Uzupełnij pole {$field}",
            'description' => 'Kompletny profil przyciąga więcej klientów',
            'priority' => 'low'
        ];
    }

    /**
     * Priorytyzuje zadania uzupełnienia profilu.
     *
     * @param array $missingFields Brakujące pola
     * @return array Posortowane według priorytetu
     */
    protected function prioritizeCompletionTasks(array $missingFields): array
    {
        $priorities = [
            'bio' => 10,
            'services' => 9,
            'photos' => 8,
            'phone' => 7,
            'experience_years' => 6,
            'certifications' => 5,
            'first_name' => 4,
            'city' => 3
        ];

        usort($missingFields, function ($a, $b) use ($priorities) {
            $priorityA = $priorities[$a] ?? 0;
            $priorityB = $priorities[$b] ?? 0;
            return $priorityB <=> $priorityA;
        });

        return $missingFields;
    }

    /**
     * Analizuje wpływ uzupełnienia pól na profil.
     *
     * @param array $missingFields Brakujące pola
     * @return array Analiza wpływu
     */
    protected function analyzeCompletionImpact(array $missingFields): array
    {
        $impact = [];

        foreach ($missingFields as $field) {
            $impact[$field] = match ($field) {
                'bio' => ['type' => 'conversion', 'value' => 60],
                'photos' => ['type' => 'conversion', 'value' => 80],
                'phone' => ['type' => 'trust', 'value' => 40],
                'certifications' => ['type' => 'pricing', 'value' => 25],
                'experience_years' => ['type' => 'pricing', 'value' => 15],
                default => ['type' => 'general', 'value' => 10]
            };
        }

        return $impact;
    }

    /**
     * Oblicza procent kompletności profilu.
     *
     * @param array $userData Dane użytkownika
     * @return int Procent kompletności
     */
    protected function calculateCompletionPercentage(array $userData): int
    {
        $allFields = ['first_name', 'city', 'bio', 'services', 'phone', 'experience_years', 'certifications', 'photos'];
        $completedFields = 0;

        foreach ($allFields as $field) {
            if (!empty($userData[$field])) {
                $completedFields++;
            }
        }

        return round(($completedFields / count($allFields)) * 100);
    }

    /**
     * Pobiera szablony motywacyjne.
     *
     * @param string $context Kontekst
     * @return array Szablony motywacyjne
     */
    protected function getMotivationalTemplates(string $context): array
    {
        return match ($context) {
            'completion' => [
                'Świetnie {name}! Twój profil wygląda coraz lepiej!',
                'Excellent {name}! Jesteś już bardzo blisko ukończenia!',
                'Brawo {name}! Każdy krok przybliża Cię do sukcesu!'
            ],
            'encouragement' => [
                'Nie poddawaj się {name}! Każdy ekspert był kiedyś początkującym.',
                'Pamiętaj {name}, że pasja to najważniejsza kwalifikacja!',
                'Jesteś na dobrej drodze {name}! Klienci pokochają Twoją opiekę!'
            ],
            'achievement' => [
                'Gratulacje {name}! Twój profil jest gotowy do podbijania serc właścicieli zwierząt!',
                'Brawo {name}! Czas pokazać światu, jaka z Ciebie fantastyczna opiekunka!',
                'Sukces {name}! Twój nowy biznes może ruszyć pełną parą!'
            ],
            default => [
                'Powodzenia {name}! Wierzymy w Twój sukces!',
                'Jesteś super {name}! Tak trzymaj!',
                'Świetna robota {name}! Kontynuuj!'
            ]
        };
    }

    /**
     * Pobiera wszystkie szablony danego typu.
     *
     * @param string $type Typ szablonu
     * @return array Szablony
     */
    protected function getAllTemplates(string $type): array
    {
        return match ($type) {
            'bio' => $this->bioTemplates,
            'suggestion' => $this->suggestionTemplates,
            default => []
        };
    }

    /**
     * Sprawdza czy szablon pasuje do filtrów.
     *
     * @param array $template Szablon
     * @param array $filters Filtry
     * @return bool Czy pasuje
     */
    protected function templateMatchesFilters(array $template, array $filters): bool
    {
        foreach ($filters as $key => $value) {
            if (!isset($template[$key]) || $template[$key] !== $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Skraca bio do docelowej długości.
     *
     * @param string $bio Bio do skrócenia
     * @param int $targetLength Docelowa długość
     * @return string Skrócone bio
     */
    protected function shortenBio(string $bio, int $targetLength): string
    {
        if (strlen($bio) <= $targetLength) {
            return $bio;
        }

        $sentences = explode('.', $bio);
        $shortened = '';

        foreach ($sentences as $sentence) {
            $testLength = strlen($shortened . $sentence . '.');
            if ($testLength <= $targetLength) {
                $shortened .= $sentence . '.';
            } else {
                break;
            }
        }

        return trim($shortened);
    }

    /**
     * Wydłuża bio dodając dodatkowe elementy.
     *
     * @param string $bio Bio do wydłużenia
     * @param int $targetLength Docelowa długość
     * @return string Wydłużone bio
     */
    protected function extendBio(string $bio, int $targetLength): string
    {
        $extensions = [
            ' Zawsze staram się zapewnić najlepszą opiekę.',
            ' Regularnie informuję właścicieli o postępach.',
            ' Twoje zwierzę będzie miało ze mną świetną zabawę!',
            ' Zapewniam pełne bezpieczeństwo i komfort.',
            ' Każde zwierzę traktuję z najwyższą troską.'
        ];

        $extended = $bio;
        foreach ($extensions as $extension) {
            if (strlen($extended . $extension) <= $targetLength) {
                $extended .= $extension;
            } else {
                break;
            }
        }

        return $extended;
    }
}