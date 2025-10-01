<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

/**
 * Serwis integracji z lokalnym AI (Ollama) dla Pet Sitter Assistant.
 *
 * Zapewnia komunikację z lokalnie uruchomionym modelem AI poprzez Ollama API.
 * Oferuje generowanie kreatywnych treści, personalizowanych bio i inteligentnych
 * sugestii z graceful fallback przy niedostępności serwisu.
 *
 * @package App\Services\AI
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class LocalAIAssistant
{
    /**
     * Konfiguracja Ollama.
     *
     * @var array
     */
    protected array $config;

    /**
     * Base URL dla Ollama API.
     *
     * @var string
     */
    protected string $baseUrl;

    /**
     * Cache prefix dla odpowiedzi AI.
     *
     * @var string
     */
    protected string $cachePrefix;

    /**
     * Status dostępności serwisu.
     *
     * @var bool|null
     */
    protected ?bool $isAvailable = null;

    /**
     * Fallback template system.
     *
     * @var TemplateSystem
     */
    protected TemplateSystem $templateSystem;

    /**
     * Konstruktor serwisu lokalnego AI.
     *
     * @param TemplateSystem $templateSystem System szablonów jako fallback
     */
    public function __construct(TemplateSystem $templateSystem)
    {
        $this->config = config('ai.ollama', []);
        $this->baseUrl = "http://{$this->config['host']}:{$this->config['port']}";
        $this->cachePrefix = config('ai.cache.prefix', 'ai_assistant:') . 'ollama:';
        $this->templateSystem = $templateSystem;
    }

    /**
     * Generuje spersonalizowane bio używając lokalnego AI.
     *
     * Tworzy unikalne, angażujące bio bazujące na danych użytkownika
     * z wykorzystaniem możliwości kreatywnych modelu AI.
     *
     * @param array $userData Dane użytkownika
     * @param array $preferences Preferencje stylu i długości
     * @return string Wygenerowane bio
     *
     * @example
     * $bio = $ai->generateBio([
     *     'first_name' => 'Anna',
     *     'experience_years' => 3,
     *     'pet_types' => ['dog', 'cat'],
     *     'services' => ['walking', 'sitting'],
     *     'location' => 'Warsaw'
     * ], ['style' => 'friendly', 'length' => 'medium']);
     */
    public function generateBio(array $userData, array $preferences = []): string
    {
        if (!$this->isServiceAvailable()) {
            Log::info('Ollama niedostępne, używam template system dla bio');
            return $this->templateSystem->generateBio($userData, $preferences);
        }

        $cacheKey = $this->cachePrefix . 'bio:' . md5(serialize($userData) . serialize($preferences));

        return Cache::remember($cacheKey, 3600, function () use ($userData, $preferences) {
            return $this->generateBioWithAI($userData, $preferences);
        });
    }

    /**
     * Generuje kontekstowe sugestie dla kroku kreatora.
     *
     * Wykorzystuje AI do stworzenia spersonalizowanych, inteligentnych
     * sugestii dostosowanych do konkretnego etapu procesu.
     *
     * @param int $step Numer kroku kreatora
     * @param array $userData Dane użytkownika
     * @param array $context Dodatkowy kontekst
     * @return array Generowane sugestie
     *
     * @example
     * $suggestions = $ai->generateStepSuggestions(5, [
     *     'experience_years' => 2,
     *     'pet_types' => ['dog'],
     *     'location' => 'Warsaw'
     * ]);
     */
    public function generateStepSuggestions(int $step, array $userData, array $context = []): array
    {
        if (!$this->isServiceAvailable()) {
            Log::info('Ollama niedostępne, używam template system dla sugestii');
            return $this->templateSystem->generateStepSuggestions($step, $userData, $context);
        }

        $cacheKey = $this->cachePrefix . "suggestions:{$step}:" . md5(serialize($userData));

        return Cache::remember($cacheKey, 1800, function () use ($step, $userData, $context) {
            return $this->generateSuggestionsWithAI($step, $userData, $context);
        });
    }

    /**
     * Generuje kreatywne opisy usług.
     *
     * Tworzy angażujące, profesjonalne opisy usług pet sitter
     * dostosowane do doświadczenia i specjalizacji użytkownika.
     *
     * @param array $services Lista usług
     * @param array $userData Dane użytkownika
     * @return array Opisy usług
     *
     * @example
     * $descriptions = $ai->generateServiceDescriptions([
     *     'dog_walking', 'pet_sitting'
     * ], ['experience_years' => 3, 'location' => 'Warsaw']);
     */
    public function generateServiceDescriptions(array $services, array $userData): array
    {
        if (!$this->isServiceAvailable()) {
            Log::info('Ollama niedostępne, zwracam podstawowe opisy usług');
            return $this->getBasicServiceDescriptions($services);
        }

        $descriptions = [];

        foreach ($services as $service) {
            $cacheKey = $this->cachePrefix . "service_desc:{$service}:" . md5(serialize($userData));

            $descriptions[$service] = Cache::remember($cacheKey, 7200, function () use ($service, $userData) {
                return $this->generateServiceDescriptionWithAI($service, $userData);
            });
        }

        return $descriptions;
    }

    /**
     * Generuje spersonalizowane komunikaty marketingowe.
     *
     * Tworzy przekonujące teksty promocyjne dostosowane do lokalnego
     * rynku i profilu użytkownika.
     *
     * @param array $userData Dane użytkownika
     * @param string $purpose Cel komunikatu (promotion, testimonial, etc.)
     * @return string Komunikat marketingowy
     */
    public function generateMarketingMessage(array $userData, string $purpose = 'promotion'): string
    {
        if (!$this->isServiceAvailable()) {
            Log::info('Ollama niedostępne, używam template system dla komunikatu');
            return $this->templateSystem->generateMotivationalMessage($userData, $purpose);
        }

        $cacheKey = $this->cachePrefix . "marketing:{$purpose}:" . md5(serialize($userData));

        return Cache::remember($cacheKey, 3600, function () use ($userData, $purpose) {
            return $this->generateMarketingMessageWithAI($userData, $purpose);
        });
    }

    /**
     * Generuje tekst używając Ollama AI z dowolnym promptem.
     *
     * Uniwersalna metoda do generowania tekstów - użyteczna dla różnych
     * przypadków użycia, gdzie potrzebujemy elastycznego generowania treści.
     *
     * @param string $prompt Prompt dla AI
     * @param int $minLength Minimalna długość odpowiedzi
     * @param int $maxLength Maksymalna długość odpowiedzi
     * @return string|null Wygenerowany tekst lub null w przypadku błędu
     *
     * @example
     * $text = $ai->generateTextWithPrompt(
     *     "Napisz profesjonalny opis motywacji dla opiekuna psów...",
     *     50,
     *     500
     * );
     */
    public function generateTextWithPrompt(string $prompt, int $minLength = 50, int $maxLength = 1000): ?string
    {
        if (!$this->isServiceAvailable()) {
            Log::info('🤖 Ollama niedostępne dla generateTextWithPrompt');
            return null;
        }

        try {
            Log::info('🤖 Wysyłam prompt do Ollama', [
                'prompt_length' => strlen($prompt),
                'min_length' => $minLength,
                'max_length' => $maxLength
            ]);

            $response = $this->makeAIRequest($prompt);

            if ($response && !empty($response['message']['content'])) {
                $text = trim($response['message']['content']);

                // Walidacja długości
                if (strlen($text) >= $minLength && strlen($text) <= $maxLength) {
                    Log::info('✅ Ollama wygenerowała tekst', [
                        'length' => strlen($text),
                        'preview' => substr($text, 0, 100) . '...'
                    ]);
                    return $text;
                } else {
                    Log::warning('⚠️ Ollama wygenerowała tekst o niewłaściwej długości', [
                        'length' => strlen($text),
                        'required_min' => $minLength,
                        'required_max' => $maxLength
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('💥 Błąd generowania tekstu przez Ollama', [
                'error' => $e->getMessage(),
                'prompt_preview' => substr($prompt, 0, 100)
            ]);
        }

        return null;
    }

    /**
     * Sprawdza dostępność serwisu Ollama.
     *
     * Wykonuje health check i cache'uje wynik aby uniknąć
     * ciągłych sprawdzeń podczas sesji użytkownika.
     *
     * @param bool $forceCheck Wymuś sprawdzenie pomijając cache
     * @return bool Status dostępności
     */
    public function isServiceAvailable(bool $forceCheck = false): bool
    {
        if (!$this->config['enabled']) {
            return false;
        }

        if (!$forceCheck && $this->isAvailable !== null) {
            return $this->isAvailable;
        }

        $cacheKey = $this->cachePrefix . 'health_check';

        $this->isAvailable = Cache::remember($cacheKey, 1800, function () {
            return $this->performHealthCheck();
        });

        return $this->isAvailable;
    }

    /**
     * Pobiera informacje o dostępnych modelach.
     *
     * @return array Lista dostępnych modeli AI
     */
    public function getAvailableModels(): array
    {
        if (!$this->isServiceAvailable()) {
            return [];
        }

        try {
            $response = Http::timeout($this->config['timeout'])
                ->get("{$this->baseUrl}/api/tags");

            if ($response->successful()) {
                return $response->json('models', []);
            }
        } catch (Exception $e) {
            Log::warning('Błąd pobierania listy modeli Ollama', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Wymusza odświeżenie statusu dostępności.
     *
     * @return bool Nowy status dostępności
     */
    public function refreshAvailability(): bool
    {
        Cache::forget($this->cachePrefix . 'health_check');
        $this->isAvailable = null;
        return $this->isServiceAvailable(true);
    }

    /**
     * Wykonuje health check serwisu Ollama.
     *
     * @return bool Status dostępności
     */
    protected function performHealthCheck(): bool
    {
        try {
            $response = Http::timeout(1)
                ->connectTimeout(1)
                ->get("{$this->baseUrl}/api/tags");

            $isAvailable = $response->successful();

            Log::info('Ollama health check', [
                'available' => $isAvailable,
                'status' => $response->status(),
                'url' => $this->baseUrl
            ]);

            return $isAvailable;
        } catch (ConnectionException $e) {
            Log::warning('Ollama connection failed', [
                'error' => $e->getMessage(),
                'url' => $this->baseUrl
            ]);
            return false;
        } catch (Exception $e) {
            Log::error('Ollama health check error', [
                'error' => $e->getMessage(),
                'url' => $this->baseUrl
            ]);
            return false;
        }
    }

    /**
     * Generuje bio używając AI z retry logic.
     *
     * @param array $userData Dane użytkownika
     * @param array $preferences Preferencje
     * @return string Wygenerowane bio
     */
    protected function generateBioWithAI(array $userData, array $preferences): string
    {
        $prompt = $this->buildBioPrompt($userData, $preferences);

        try {
            $response = $this->makeAIRequest($prompt);

            if ($response && !empty($response['message']['content'])) {
                $bio = trim($response['message']['content']);

                // Walidacja długości i jakości odpowiedzi
                if ($this->validateBioResponse($bio, $preferences)) {
                    Log::info('Wygenerowano bio używając Ollama AI', [
                        'length' => strlen($bio),
                        'style' => $preferences['style'] ?? 'default'
                    ]);
                    return $bio;
                }
            }
        } catch (Exception $e) {
            Log::error('Błąd generowania bio przez Ollama', ['error' => $e->getMessage()]);
        }

        // Fallback do template system
        Log::info('Fallback do template system dla bio');
        return $this->templateSystem->generateBio($userData, $preferences);
    }

    /**
     * Generuje sugestie używając AI.
     *
     * @param int $step Numer kroku
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return array Generowane sugestie
     */
    protected function generateSuggestionsWithAI(int $step, array $userData, array $context): array
    {
        $prompt = $this->buildSuggestionsPrompt($step, $userData, $context);

        try {
            $response = $this->makeAIRequest($prompt);

            if ($response && !empty($response['message']['content'])) {
                $suggestions = $this->parseSuggestionsResponse($response['message']['content']);

                if ($this->validateSuggestionsResponse($suggestions)) {
                    Log::info('Wygenerowano sugestie używając Ollama AI', [
                        'step' => $step,
                        'suggestions_count' => count($suggestions['tips'] ?? [])
                    ]);
                    return $suggestions;
                }
            }
        } catch (Exception $e) {
            Log::error('Błąd generowania sugestii przez Ollama', [
                'error' => $e->getMessage(),
                'step' => $step
            ]);
        }

        // Fallback do template system
        Log::info('Fallback do template system dla sugestii');
        return $this->templateSystem->generateStepSuggestions($step, $userData, $context);
    }

    /**
     * Generuje opis usługi używając AI.
     *
     * @param string $service Nazwa usługi
     * @param array $userData Dane użytkownika
     * @return string Opis usługi
     */
    protected function generateServiceDescriptionWithAI(string $service, array $userData): string
    {
        $prompt = $this->buildServiceDescriptionPrompt($service, $userData);

        try {
            $response = $this->makeAIRequest($prompt);

            if ($response && !empty($response['message']['content'])) {
                $description = trim($response['message']['content']);

                if (strlen($description) >= 50 && strlen($description) <= 300) {
                    return $description;
                }
            }
        } catch (Exception $e) {
            Log::error('Błąd generowania opisu usługi przez Ollama', [
                'error' => $e->getMessage(),
                'service' => $service
            ]);
        }

        // Fallback do podstawowego opisu
        return $this->getBasicServiceDescription($service);
    }

    /**
     * Generuje komunikat marketingowy używając AI.
     *
     * @param array $userData Dane użytkownika
     * @param string $purpose Cel komunikatu
     * @return string Komunikat marketingowy
     */
    protected function generateMarketingMessageWithAI(array $userData, string $purpose): string
    {
        $prompt = $this->buildMarketingPrompt($userData, $purpose);

        try {
            $response = $this->makeAIRequest($prompt);

            if ($response && !empty($response['message']['content'])) {
                return trim($response['message']['content']);
            }
        } catch (Exception $e) {
            Log::error('Błąd generowania komunikatu marketingowego przez Ollama', [
                'error' => $e->getMessage(),
                'purpose' => $purpose
            ]);
        }

        // Fallback do template system
        return $this->templateSystem->generateMotivationalMessage($userData, $purpose);
    }

    /**
     * Wykonuje request do Ollama API z retry logic.
     *
     * @param string $prompt Prompt dla AI
     * @return array|null Odpowiedź AI
     */
    protected function makeAIRequest(string $prompt): ?array
    {
        $maxRetries = $this->config['max_retries'] ?? 3;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $response = Http::timeout($this->config['timeout'])
                    ->post("{$this->baseUrl}/api/generate", [
                        'model' => $this->config['model'],
                        'prompt' => $prompt,
                        'stream' => false,
                        'options' => [
                            'temperature' => 0.7,
                            'max_tokens' => 500,
                            'top_p' => 0.9
                        ]
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::warning('Ollama request failed', [
                    'attempt' => $attempt + 1,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

            } catch (ConnectionException $e) {
                Log::warning('Ollama connection error', [
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage()
                ]);
            } catch (RequestException $e) {
                Log::warning('Ollama request error', [
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage()
                ]);
            }

            $attempt++;
            if ($attempt < $maxRetries) {
                sleep(1); // Krótkie opóźnienie przed retry
            }
        }

        Log::error('Ollama request failed after all retries', [
            'max_retries' => $maxRetries,
            'prompt_length' => strlen($prompt)
        ]);

        return null;
    }

    /**
     * Buduje prompt dla generowania bio.
     *
     * @param array $userData Dane użytkownika
     * @param array $preferences Preferencje
     * @return string Prompt
     */
    protected function buildBioPrompt(array $userData, array $preferences): string
    {
        $name = $userData['first_name'] ?? 'Opiekun';
        $experience = $userData['experience_years'] ?? 0;
        $petTypes = implode(', ', $userData['pet_types'] ?? ['zwierzęta']);
        $services = implode(', ', $userData['services'] ?? ['opieka']);
        $location = $userData['city'] ?? 'Polska';
        $style = $preferences['style'] ?? 'friendly';
        $length = $preferences['length'] ?? 'medium';

        $lengthGuide = match ($length) {
            'short' => '100-150 znaków',
            'medium' => '200-250 znaków',
            'long' => '300-400 znaków',
            default => '200-250 znaków'
        };

        $styleGuide = match ($style) {
            'professional' => 'formalny, profesjonalny ton',
            'friendly' => 'ciepły, przyjazny ton',
            'casual' => 'swobodny, nieformalny ton',
            default => 'ciepły, przyjazny ton'
        };

        return "Napisz bio dla opiekuna zwierząt o imieniu {$name}.

Szczegóły:
- Doświadczenie: {$experience} lat
- Specjalizacja: {$petTypes}
- Oferowane usługi: {$services}
- Lokalizacja: {$location}
- Styl: {$styleGuide}
- Długość: {$lengthGuide}

Wymagania:
- Używaj języka polskiego
- Bio powinno być angażujące i budować zaufanie
- Podkreśl pasję do zwierząt
- Unikaj przesadnych superlatywów
- Napisz w pierwszej osobie
- Zakończ zachętą do kontaktu

Bio:";
    }

    /**
     * Buduje prompt dla generowania sugestii.
     *
     * @param int $step Numer kroku
     * @param array $userData Dane użytkownika
     * @param array $context Kontekst
     * @return string Prompt
     */
    protected function buildSuggestionsPrompt(int $step, array $userData, array $context): string
    {
        $experience = $userData['experience_years'] ?? 0;
        $services = implode(', ', $userData['services'] ?? []);
        $location = $userData['city'] ?? 'Polska';

        $stepContext = match ($step) {
            1 => 'wypełnianie danych osobowych',
            2 => 'wybór lokalizacji działania',
            3 => 'pisanie bio/opisu',
            4 => 'określanie doświadczenia',
            5 => 'wybór oferowanych usług',
            6 => 'ustalanie dostępności',
            7 => 'wycena usług',
            8 => 'dodawanie certyfikatów',
            9 => 'dodawanie zdjęć',
            10 => 'przegląd końcowy profilu',
            default => 'tworzenie profilu opiekuna'
        };

        return "Wygeneruj 3 praktyczne wskazówki dla opiekuna zwierząt na etapie: {$stepContext} (krok {$step}).

Profil opiekuna:
- Doświadczenie: {$experience} lat
- Usługi: {$services}
- Lokalizacja: {$location}

Format odpowiedzi (używaj języka polskiego):
WSKAZÓWKI:
1. [pierwsza wskazówka]
2. [druga wskazówka]
3. [trzecia wskazówka]

OSTRZEŻENIA:
- [ewentualne ostrzeżenie jeśli potrzebne]

REKOMENDACJE:
- [dodatkowa rekomendacja]

Wskazówki powinny być:
- Konkretne i praktyczne
- Dostosowane do poziomu doświadczenia
- Pomocne w zwiększeniu atrakcyjności profilu
- Napisane w przyjaznym tonie";
    }

    /**
     * Buduje prompt dla opisu usługi.
     *
     * @param string $service Nazwa usługi
     * @param array $userData Dane użytkownika
     * @return string Prompt
     */
    protected function buildServiceDescriptionPrompt(string $service, array $userData): string
    {
        $experience = $userData['experience_years'] ?? 0;
        $location = $userData['city'] ?? 'Polska';

        $serviceNames = [
            'dog_walking' => 'spacery z psami',
            'pet_sitting' => 'opieka domowa nad zwierzętami',
            'overnight_care' => 'opieka nocna',
            'grooming' => 'pielęgnacja zwierząt',
            'feeding' => 'karmienie zwierząt',
            'vet_transport' => 'transport weterynaryjny'
        ];

        $serviceName = $serviceNames[$service] ?? $service;

        return "Napisz krótki, atrakcyjny opis usługi '{$serviceName}' dla opiekuna zwierząt.

Kontekst:
- Doświadczenie opiekuna: {$experience} lat
- Lokalizacja: {$location}

Wymagania:
- Długość: 80-150 znaków
- Język: polski
- Ton: profesjonalny ale przyjazny
- Podkreśl korzyści dla klienta
- Unikaj technicznych szczegółów
- Zakończ zachętą

Opis:";
    }

    /**
     * Buduje prompt dla komunikatu marketingowego.
     *
     * @param array $userData Dane użytkownika
     * @param string $purpose Cel komunikatu
     * @return string Prompt
     */
    protected function buildMarketingPrompt(array $userData, string $purpose): string
    {
        $name = $userData['first_name'] ?? 'Opiekun';
        $experience = $userData['experience_years'] ?? 0;
        $location = $userData['city'] ?? 'Polska';

        $purposeContext = match ($purpose) {
            'promotion' => 'promocyjny post na social media',
            'testimonial' => 'fragment referencji klienta',
            'achievement' => 'komunikat o osiągnięciu',
            'encouragement' => 'motywacyjna wiadomość',
            default => 'komunikat promocyjny'
        };

        return "Napisz krótki {$purposeContext} dla opiekuna zwierząt.

Szczegóły:
- Imię: {$name}
- Doświadczenie: {$experience} lat
- Lokalizacja: {$location}

Wymagania:
- Długość: 50-100 znaków
- Język: polski
- Ton: pozytywny i angażujący
- Podkreśl unikalność opiekuna
- Zachęć do działania

Komunikat:";
    }

    /**
     * Waliduje odpowiedź AI dla bio.
     *
     * @param string $bio Wygenerowane bio
     * @param array $preferences Preferencje
     * @return bool Czy bio jest poprawne
     */
    protected function validateBioResponse(string $bio, array $preferences): bool
    {
        $length = strlen($bio);
        $targetLength = match ($preferences['length'] ?? 'medium') {
            'short' => 150,
            'medium' => 250,
            'long' => 400,
            default => 250
        };

        // Sprawdź długość (±50% tolerancja)
        if ($length < $targetLength * 0.5 || $length > $targetLength * 1.5) {
            return false;
        }

        // Sprawdź czy nie zawiera placeholder'ów
        if (strpos($bio, '{') !== false || strpos($bio, '}') !== false) {
            return false;
        }

        // Sprawdź czy zawiera podstawowe elementy
        if (strlen(trim($bio)) < 50) {
            return false;
        }

        return true;
    }

    /**
     * Parsuje odpowiedź AI dla sugestii.
     *
     * @param string $response Odpowiedź AI
     * @return array Sparsowane sugestie
     */
    protected function parseSuggestionsResponse(string $response): array
    {
        $sections = [
            'tips' => [],
            'warnings' => [],
            'recommendations' => []
        ];

        $currentSection = null;
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) continue;

            // Rozpoznaj sekcje
            if (stripos($line, 'WSKAZÓWKI') !== false || stripos($line, 'TIPS') !== false) {
                $currentSection = 'tips';
                continue;
            } elseif (stripos($line, 'OSTRZEŻENIA') !== false || stripos($line, 'WARNINGS') !== false) {
                $currentSection = 'warnings';
                continue;
            } elseif (stripos($line, 'REKOMENDACJE') !== false || stripos($line, 'RECOMMENDATIONS') !== false) {
                $currentSection = 'recommendations';
                continue;
            }

            // Dodaj do aktualnej sekcji
            if ($currentSection && (preg_match('/^[\d\-\•\*]\s*(.+)/', $line, $matches) || $line[0] === '-')) {
                $text = $matches[1] ?? ltrim($line, '- •*');
                $sections[$currentSection][] = trim($text);
            }
        }

        return $sections;
    }

    /**
     * Waliduje odpowiedź AI dla sugestii.
     *
     * @param array $suggestions Sparsowane sugestie
     * @return bool Czy sugestie są poprawne
     */
    protected function validateSuggestionsResponse(array $suggestions): bool
    {
        // Sprawdź czy jest przynajmniej jedna wskazówka
        if (empty($suggestions['tips'])) {
            return false;
        }

        // Sprawdź długość wskazówek
        foreach ($suggestions['tips'] as $tip) {
            if (strlen($tip) < 10 || strlen($tip) > 200) {
                return false;
            }
        }

        return true;
    }

    /**
     * Pobiera podstawowe opisy usług (fallback).
     *
     * @param array $services Lista usług
     * @return array Opisy usług
     */
    protected function getBasicServiceDescriptions(array $services): array
    {
        $descriptions = [];
        foreach ($services as $service) {
            $descriptions[$service] = $this->getBasicServiceDescription($service);
        }
        return $descriptions;
    }

    /**
     * Pobiera podstawowy opis usługi (fallback).
     *
     * @param string $service Nazwa usługi
     * @return string Opis usługi
     */
    protected function getBasicServiceDescription(string $service): string
    {
        $basicDescriptions = [
            'dog_walking' => 'Profesjonalne spacery z Twoim psem w bezpiecznym środowisku. Zapewniam aktywność fizyczną i radość.',
            'pet_sitting' => 'Opieka domowa nad Twoim zwierzęciem w znajomym mu otoczeniu. Komfort i bezpieczeństwo gwarantowane.',
            'overnight_care' => 'Całodobowa opieka nad zwierzęciem. Twój pupil nie zostanie sam na noc.',
            'grooming' => 'Profesjonalna pielęgnacja zwierząt. Dbam o higienę i wygląd Twojego pupila.',
            'feeding' => 'Regularne karmienie zgodnie z dietą zwierzęcia. Niezawodność i punktualność.',
            'vet_transport' => 'Bezpieczny transport do weterynarza. Wsparcie w stresowych momentach.'
        ];

        return $basicDescriptions[$service] ?? 'Profesjonalna opieka nad Twoim zwierzęciem z pełnym zaangażowaniem.';
    }

    /**
     * Estymuje populację w danym obszarze używając AI.
     *
     * Wykorzystuje lokalną Ollama do inteligentnej analizy demograficznej
     * obszaru na podstawie nazwy miasta i promienia obsługi.
     *
     * @param string $city Nazwa miasta lub obszaru
     * @param float $radius Promień obsługi w kilometrach
     * @return array Dane demograficzne i szacunki
     */
    public function estimatePopulation(string $city, float $radius): array
    {
        if (!$this->isServiceAvailable()) {
            Log::info('AI service niedostępny - używam fallback estymacji populacji');
            return $this->getFallbackPopulationEstimate($city, $radius);
        }

        $prompt = "
        Jako ekspert demograficzny, oszacuj populację dla miasta {$city} w promieniu {$radius} km.

        Uwzględnij:
        - Gęstość zaludnienia miasta w Polsce
        - Typ obszaru (centrum, przedmieścia, peryferia)
        - Wzorce urbanistyczne polskich miast
        - Aktualne statystyki GUS
        - Trendy demograficzne w Polsce

        Zwróć odpowiedź WYŁĄCZNIE w formacie JSON (bez żadnego dodatkowego tekstu):
        {
            \"estimated_population\": liczba_mieszkańców,
            \"households\": liczba_gospodarstw_domowych,
            \"pet_owners\": szacowana_liczba_właścicieli_zwierząt,
            \"potential_clients\": potencjalni_klienci_PetHelp,
            \"confidence\": poziom_pewności_0_do_1,
            \"area_type\": \"centrum/przedmieścia/peryferia\",
            \"notes\": \"krótka_notatka_o_obszarze\"
        }
        ";

        try {
            $cacheKey = $this->cachePrefix . "population_estimate:" . md5($city . $radius);

            return Cache::remember($cacheKey, 3600, function () use ($prompt, $city, $radius) {
                $response = $this->makeAIRequest($prompt);

                if ($response && !empty($response['response'])) {
                    // Spróbuj sparsować JSON z odpowiedzi AI
                    $jsonResponse = $this->extractJsonFromResponse($response['response']);

                    if ($jsonResponse && $this->validatePopulationData($jsonResponse)) {
                        Log::info('AI population estimate successful', [
                            'city' => $city,
                            'radius' => $radius,
                            'population' => $jsonResponse['estimated_population']
                        ]);

                        return $jsonResponse;
                    }
                }

                // Fallback jeśli AI nie zwróciło prawidłowych danych
                Log::warning('AI population estimate failed - using fallback');
                return $this->getFallbackPopulationEstimate($city, $radius);
            });

        } catch (Exception $e) {
            Log::error('Błąd estymacji populacji przez AI', [
                'error' => $e->getMessage(),
                'city' => $city,
                'radius' => $radius
            ]);

            return $this->getFallbackPopulationEstimate($city, $radius);
        }
    }

    /**
     * Wyciąga JSON z odpowiedzi AI (czasem AI dodaje dodatkowy tekst).
     *
     * @param string $response Odpowiedź AI
     * @return array|null Sparsowany JSON lub null
     */
    protected function extractJsonFromResponse(string $response): ?array
    {
        // Usuń dodatkowy tekst i znajdź JSON
        $response = trim($response);

        // Znajdź pierwszy { i ostatni }
        $start = strpos($response, '{');
        $end = strrpos($response, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $jsonString = substr($response, $start, $end - $start + 1);
            $decoded = json_decode($jsonString, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Waliduje dane populacyjne z AI.
     *
     * @param array $data Dane do walidacji
     * @return bool Czy dane są poprawne
     */
    protected function validatePopulationData(array $data): bool
    {
        $required = ['estimated_population', 'households', 'pet_owners', 'potential_clients', 'confidence'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || !is_numeric($data[$field])) {
                return false;
            }
        }

        // Sprawdź sensowność danych
        return $data['estimated_population'] > 0 &&
               $data['confidence'] >= 0 &&
               $data['confidence'] <= 1;
    }

    /**
     * Fallback estymacja populacji gdy AI nie działa.
     *
     * @param string $city Nazwa miasta
     * @param float $radius Promień w km
     * @return array Podstawowe dane demograficzne
     */
    protected function getFallbackPopulationEstimate(string $city, float $radius): array
    {
        // Podstawowe estymacje dla polskich miast
        $cityData = $this->getBasicCityData($city);
        $area = pi() * pow($radius, 2); // km²

        $population = (int) ($area * $cityData['density']);
        $households = (int) ($population / 2.3); // Średnia wielkość gospodarstwa w Polsce
        $petOwners = (int) ($households * 0.37); // ~37% gospodarstw ma zwierzęta
        $potentialClients = (int) ($petOwners * 0.15); // ~15% może korzystać z usług

        return [
            'estimated_population' => $population,
            'households' => $households,
            'pet_owners' => $petOwners,
            'potential_clients' => $potentialClients,
            'confidence' => 0.6,
            'area_type' => $cityData['type'],
            'notes' => 'Estymacja fallback oparta na statystykach GUS'
        ];
    }

    /**
     * Pobiera podstawowe dane o mieście.
     *
     * @param string $city Nazwa miasta
     * @return array Dane miasta
     */
    protected function getBasicCityData(string $city): array
    {
        $cityName = strtolower($city);

        // Gęstość zaludnienia (mieszkańców/km²) dla głównych miast
        $cities = [
            'warszawa' => ['density' => 3500, 'type' => 'centrum'],
            'kraków' => ['density' => 2300, 'type' => 'centrum'],
            'gdańsk' => ['density' => 1800, 'type' => 'centrum'],
            'wrocław' => ['density' => 2200, 'type' => 'centrum'],
            'poznań' => ['density' => 2100, 'type' => 'centrum'],
            'łódź' => ['density' => 2000, 'type' => 'centrum'],
        ];

        // Sprawdź czy miasto jest w bazie
        foreach ($cities as $knownCity => $data) {
            if (str_contains($cityName, $knownCity)) {
                return $data;
            }
        }

        // Domyślne wartości dla mniejszych miast
        return ['density' => 800, 'type' => 'przedmieścia'];
    }
}